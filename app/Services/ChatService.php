<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ChatService
{
    /**
     * 메시지 전송 — 첨부 저장, 대화 갱신, 상대측 알림.
     */
    public static function send(
        Conversation $conversation,
        string $role,          // customer / store / admin / system
        ?int $senderId,
        string $senderName,
        ?string $body = null,
        ?UploadedFile $file = null
    ): Message {
        $attachment = null;
        $attachmentType = null;

        if ($file) {
            $dir = base_path("assets/uploads/chat/{$conversation->id}");
            @mkdir($dir, 0777, true);
            $name = Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            $attachment = "assets/uploads/chat/{$conversation->id}/{$name}";
            $attachmentType = str_starts_with((string) $file->getClientMimeType(), 'image/') ? 'image' : 'file';
        }

        $message = $conversation->messages()->create([
            'sender_role'     => $role,
            'sender_id'       => $senderId,
            'sender_name'     => $senderName,
            'body'            => $body,
            'attachment'      => $attachment,
            'attachment_type' => $attachmentType,
        ]);

        $conversation->update([
            'last_message_at'  => now(),
            'status'           => 'open',
            'customer_read_at' => $role === 'customer' ? now() : $conversation->customer_read_at,
            'staff_read_at'    => in_array($role, ['store', 'admin']) ? now() : $conversation->staff_read_at,
        ]);

        self::notifyOtherSide($conversation, $role, $body, $file);

        return $message;
    }

    private static function notifyOtherSide(Conversation $c, string $role, ?string $body, ?UploadedFile $file): void
    {
        $preview = $body ? Str::limit($body, 40) : ($file ? '📎 첨부파일' : '새 메시지');

        if ($role === 'customer') {
            // 고객 → 담당 지점 또는 본사(관리자)
            if ($c->store_id) {
                NotificationService::notify('store', $c->store_id, 'chat', '💬 새 상담 메시지',
                    "{$c->customer->name}: {$preview}", route('partner.chat.show', $c), ['in_app', 'push'], '💬');
            } else {
                foreach (\App\Models\Admin::pluck('id') as $adminId) {
                    NotificationService::notify('admin', $adminId, 'chat', '💬 새 상담 메시지',
                        "{$c->customer->name}: {$preview}", route('admin.chat.show', $c), ['in_app'], '💬');
                }
            }
        } else {
            // 지점/본사 → 고객
            NotificationService::notify('customer', $c->customer_id, 'chat', '💬 상담 답변 도착',
                "{$c->staff_label}: {$preview}", route('chat.show', $c), ['in_app', 'kakao'], '💬');
        }
    }

    /** 폴링 — afterId 이후 메시지 JSON */
    public static function poll(Conversation $conversation, int $afterId): array
    {
        return $conversation->messages()->where('id', '>', $afterId)->get()->map(fn ($m) => [
            'id'         => $m->id,
            'role'       => $m->sender_role,
            'name'       => $m->sender_name,
            'body'       => $m->body,
            'attachment' => $m->attachment ? (str_starts_with($m->attachment, 'http') ? $m->attachment : asset($m->attachment)) : null,
            'type'       => $m->attachment_type,
            'at'         => $m->created_at->format('H:i'),
        ])->all();
    }

    public static function markRead(Conversation $conversation, string $side): void
    {
        $conversation->update([
            ($side === 'customer' ? 'customer_read_at' : 'staff_read_at') => now(),
        ]);
    }
}

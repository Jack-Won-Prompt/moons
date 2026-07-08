<?php

namespace App\Services;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * 알림 발송 — 인앱 알림 저장 + 외부 채널(이메일/SMS/카카오/푸시)은 로그로 시뮬레이션.
     */
    public static function notify(
        string $role,
        int $id,
        string $type,
        string $title,
        ?string $body = null,
        ?string $link = null,
        array $channels = ['in_app'],
        string $icon = '🔔'
    ): AppNotification {
        $notification = AppNotification::create([
            'recipient_role' => $role,
            'recipient_id'   => $id,
            'type'           => $type,
            'icon'           => $icon,
            'title'          => $title,
            'body'           => $body,
            'link'           => $link,
            'channels'       => $channels,
        ]);

        // 외부 채널 디스패치 (데모: 로그) — 실제로는 PG/문자/알림톡/푸시 API 연동
        foreach (array_diff($channels, ['in_app']) as $channel) {
            Log::channel('single')->info("[NOTIFY:{$channel}] to {$role}#{$id} :: {$title} — {$body}");
        }

        return $notification;
    }

    public static function unreadCount(string $role, int $id): int
    {
        return AppNotification::for($role, $id)->unread()->count();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /** 상담 모니터링 — 전체 대화 + 통계 */
    public function index()
    {
        $conversations = Conversation::with(['customer', 'store'])
            ->orderByDesc('last_message_at')->orderByDesc('id')->paginate(20);

        $stats = [
            'total'    => Conversation::count(),
            'open'     => Conversation::where('status', 'open')->count(),
            'messages' => Message::count(),
            'to_hq'    => Conversation::whereNull('store_id')->count(),
        ];

        return view('admin.chat.index', compact('conversations', 'stats'));
    }

    public function show(Conversation $conversation)
    {
        // 본사가 담당(store 없음)이면 답변 가능, 아니면 모니터링(읽기)만
        ChatService::markRead($conversation, 'staff');
        $conversation->load('messages', 'customer', 'store', 'product');
        $canReply = is_null($conversation->store_id);

        return view('admin.chat.show', ['c' => $conversation, 'canReply' => $canReply]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        abort_unless(is_null($conversation->store_id), 403, '지점 담당 상담입니다.');
        abort_if(! $request->filled('body') && ! $request->hasFile('attachment'), 422);
        $admin = Auth::guard('admin')->user();

        ChatService::send($conversation, 'admin', $admin->id, 'MOONS 본사 · ' . $admin->name,
            $request->input('body'), $request->file('attachment'));

        return $request->wantsJson() ? response()->json(['ok' => true]) : back();
    }

    public function poll(Request $request, Conversation $conversation)
    {
        return response()->json(ChatService::poll($conversation, (int) $request->get('after', 0)));
    }
}

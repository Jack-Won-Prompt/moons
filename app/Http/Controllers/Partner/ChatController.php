<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['customer', 'product'])
            ->where('store_id', Auth::guard('partner')->id())
            ->orderByDesc('last_message_at')->orderByDesc('id')->get();

        return view('partner.chat.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        abort_unless($conversation->store_id === Auth::guard('partner')->id(), 403);
        ChatService::markRead($conversation, 'staff');
        $conversation->load('messages', 'customer', 'product');

        return view('partner.chat.show', ['c' => $conversation]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->store_id === Auth::guard('partner')->id(), 403);
        abort_if(! $request->filled('body') && ! $request->hasFile('attachment'), 422);
        $store = Auth::guard('partner')->user();

        ChatService::send($conversation, 'store', $store->id, $store->company_name,
            $request->input('body'), $request->file('attachment'));

        return $request->wantsJson() ? response()->json(['ok' => true]) : back();
    }

    public function poll(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->store_id === Auth::guard('partner')->id(), 403);
        ChatService::markRead($conversation, 'staff');

        return response()->json(ChatService::poll($conversation, (int) $request->get('after', 0)));
    }
}

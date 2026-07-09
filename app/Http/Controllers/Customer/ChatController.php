<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Product;
use App\Models\SellRequest;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['store', 'product'])
            ->where('customer_id', Auth::guard('web')->id())
            ->orderByDesc('last_message_at')->orderByDesc('id')->get();

        return view('customer.chat.index', compact('conversations'));
    }

    /** 상담 시작 (상품 문의 / 견적 상담 / 고객 상담) */
    public function start(Request $request)
    {
        $data = $request->validate([
            'type'            => ['required', 'in:quote,product,support'],
            'product_id'      => ['nullable', 'exists:products,id'],
            'sell_request_id' => ['nullable', 'exists:sell_requests,id'],
            'subject'         => ['nullable', 'string', 'max:200'],
            'message'         => ['nullable', 'string'],
        ]);

        $customerId = Auth::guard('web')->id();
        $storeId = null;
        $subject = $data['subject'] ?? '고객 상담';

        if (! empty($data['product_id'])) {
            $product = Product::find($data['product_id']);
            $storeId = $product?->partner_id;
            $subject = $product ? "[상품문의] {$product->brand} {$product->name}" : $subject;
        } elseif (! empty($data['sell_request_id'])) {
            $sr = SellRequest::where('customer_id', $customerId)->find($data['sell_request_id']);
            $storeId = $sr?->target_store_id;
            $subject = $sr ? "[견적상담] {$sr->code} {$sr->brand}" : $subject;
        }

        $conversation = Conversation::firstOrCreate(
            [
                'customer_id'     => $customerId,
                'type'            => $data['type'],
                'product_id'      => $data['product_id'] ?? null,
                'sell_request_id' => $data['sell_request_id'] ?? null,
                'status'          => 'open',
            ],
            [
                'code'     => 'CV-' . strtoupper(Str::random(6)),
                'store_id' => $storeId,
                'subject'  => Str::limit($subject, 190),
            ]
        );

        if (! empty($data['message'])) {
            ChatService::send($conversation, 'customer', $customerId, Auth::guard('web')->user()->name, $data['message']);
        }

        return redirect()->route('chat.show', $conversation);
    }

    /** 플로팅 위젯 — 고객 1:1 상담 대화 get-or-create + 메시지 반환 */
    public function widget()
    {
        $customerId = Auth::guard('web')->id();

        $conversation = Conversation::firstOrCreate(
            ['customer_id' => $customerId, 'type' => 'support', 'product_id' => null, 'sell_request_id' => null, 'status' => 'open'],
            ['code' => 'CV-' . strtoupper(Str::random(6)), 'store_id' => null, 'subject' => '1:1 고객 상담']
        );
        ChatService::markRead($conversation, 'customer');

        return response()->json([
            'conversation_id' => $conversation->id,
            'send_url'        => route('chat.send', $conversation),
            'poll_url'        => route('chat.poll', $conversation),
            'messages'        => ChatService::poll($conversation, 0),
        ]);
    }

    public function show(Conversation $conversation)
    {
        abort_unless($conversation->customer_id === Auth::guard('web')->id(), 403);
        ChatService::markRead($conversation, 'customer');
        $conversation->load('messages', 'store', 'product');

        return view('customer.chat.show', ['c' => $conversation]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->customer_id === Auth::guard('web')->id(), 403);
        $request->validate(['body' => ['nullable', 'string'], 'attachment' => ['nullable', 'file', 'max:8192']]);
        abort_if(! $request->filled('body') && ! $request->hasFile('attachment'), 422);

        ChatService::send($conversation, 'customer', $conversation->customer_id,
            Auth::guard('web')->user()->name, $request->input('body'), $request->file('attachment'));

        return $request->wantsJson() ? response()->json(['ok' => true]) : back();
    }

    public function poll(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->customer_id === Auth::guard('web')->id(), 403);
        ChatService::markRead($conversation, 'customer');

        return response()->json(ChatService::poll($conversation, (int) $request->get('after', 0)));
    }
}

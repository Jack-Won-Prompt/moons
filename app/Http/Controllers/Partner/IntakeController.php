<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\AuctionBid;
use App\Models\SellRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntakeController extends Controller
{
    /** 판매 접수 목록 — 우리 지점 지정 건 + 참여 가능한 경매 */
    public function index()
    {
        $me = Auth::guard('partner')->id();

        $direct = SellRequest::with(['customer', 'category'])
            ->where('target_type', 'store')->where('target_store_id', $me)
            ->latest()->get();

        $auctions = SellRequest::with(['customer', 'bids'])
            ->where('method', 'auction')
            ->whereIn('status', ['auctioning', 'customer_approved', 'inbound', 'settled'])
            ->latest()->get();

        return view('partner.intakes.index', compact('direct', 'auctions', 'me'));
    }

    public function show(SellRequest $sellRequest)
    {
        $me = Auth::guard('partner')->id();
        $isDirect  = $sellRequest->target_type === 'store' && $sellRequest->target_store_id === $me;
        $isAuction = $sellRequest->method === 'auction';
        abort_unless($isDirect || $isAuction, 403);

        $sellRequest->load(['customer', 'bids.store', 'category', 'winningStore', 'certificate']);
        $myBid = $sellRequest->bids->firstWhere('store_id', $me);

        return view('partner.intakes.show', ['sr' => $sellRequest, 'me' => $me, 'myBid' => $myBid]);
    }

    /** 정품 감정 + 견적 등록 (지점 직접 판매 건) */
    public function appraise(Request $request, SellRequest $sellRequest)
    {
        $me = Auth::guard('partner')->id();
        abort_unless($sellRequest->target_type === 'store' && $sellRequest->target_store_id === $me, 403);

        $data = $request->validate([
            'checklist'        => ['nullable', 'array'],
            'appraisal_result' => ['required', 'in:authentic,fake,uncertain'],
            'appraiser'        => ['required', 'string', 'max:80'],
            'quote_price'      => ['nullable', 'integer', 'min:0'],
            'memo'             => ['nullable', 'string'],
        ]);

        $store = Auth::guard('partner')->user();

        $sellRequest->update([
            'appraisal'        => $data['checklist'] ?? [],
            'appraisal_result' => $data['appraisal_result'],
            'appraiser'        => $data['appraiser'] . ' (' . $store->company_name . ')',
            'quote_price'      => $data['quote_price'] ?? null,
            'memo'             => $data['memo'] ?? null,
            'status'           => $data['appraisal_result'] === 'fake'
                ? 'rejected'
                : ($data['quote_price'] ? 'quoted' : 'appraising'),
        ]);

        return back()->with('status', '감정 결과와 견적을 등록했습니다.');
    }

    /** 경매 입찰 (등록/수정) */
    public function bid(Request $request, SellRequest $sellRequest)
    {
        abort_unless($sellRequest->method === 'auction' && $sellRequest->status === 'auctioning', 400, '입찰할 수 없는 상태입니다.');
        $me = Auth::guard('partner')->id();

        $data = $request->validate([
            'bid_price' => ['required', 'integer', 'min:1000'],
            'message'   => ['nullable', 'string', 'max:255'],
        ]);

        AuctionBid::updateOrCreate(
            ['sell_request_id' => $sellRequest->id, 'store_id' => $me],
            ['bid_price' => $data['bid_price'], 'message' => $data['message'] ?? null, 'status' => 'active']
        );

        return back()->with('status', '입찰가를 등록했습니다.');
    }

    /** 입고 확인 (고객 승인 후, 낙찰/지정 지점) */
    public function inbound(SellRequest $sellRequest)
    {
        $me = Auth::guard('partner')->id();
        $mine = $sellRequest->winning_store_id === $me || $sellRequest->target_store_id === $me;
        abort_unless($mine && $sellRequest->status === 'customer_approved', 400);

        $sellRequest->update(['status' => 'inbound']);

        return back()->with('status', '입고 처리했습니다. 본사 감정서 발급 대기 중입니다.');
    }
}

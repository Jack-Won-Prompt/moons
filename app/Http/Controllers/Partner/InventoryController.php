<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\StockTransfer;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    /** 내 재고 조회 · 위치 관리 */
    public function index()
    {
        $inventories = Inventory::with('product')
            ->where('store_id', Auth::guard('partner')->id())->latest()->get();

        return view('partner.inventory.index', compact('inventories'));
    }

    /** 타 지점 재고 조회 (이동 요청 가능) */
    public function stores()
    {
        $me = Auth::guard('partner')->id();
        $others = Inventory::with(['product', 'store'])
            ->where('store_id', '!=', $me)->where('quantity', '>', 0)
            ->orderBy('store_id')->get();

        return view('partner.inventory.stores', compact('others', 'me'));
    }

    public function requestTransfer(Request $request)
    {
        $data = $request->validate([
            'inventory_id'  => ['required', 'exists:inventories,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'reason'        => ['nullable', 'string', 'max:200'],
            'customer_wish' => ['nullable', 'boolean'],
        ]);
        $me = Auth::guard('partner')->id();
        $src = Inventory::findOrFail($data['inventory_id']);
        abort_if($src->store_id === $me, 400, '자기 지점입니다.');
        abort_if($data['quantity'] > $src->quantity, 400, '요청 수량이 재고보다 많습니다.');

        $transfer = StockTransfer::create([
            'code'          => 'TR-' . strtoupper(Str::random(6)),
            'product_id'    => $src->product_id,
            'from_store_id' => $src->store_id,
            'to_store_id'   => $me,
            'quantity'      => $data['quantity'],
            'status'        => 'requested',
            'customer_wish' => $request->boolean('customer_wish'),
            'reason'        => $data['reason'] ?? null,
        ]);

        NotificationService::notify('store', $src->store_id, 'transfer', '📦 재고 이동 요청',
            Auth::guard('partner')->user()->company_name . "이 이동을 요청했습니다.", route('partner.inventory.transfers'), ['in_app'], '📦');

        return redirect()->route('partner.inventory.transfers')->with('status', "이동 요청 {$transfer->code} 생성됨");
    }

    /** 받은 요청(내가 from) + 보낸 요청(내가 to) */
    public function transfers()
    {
        $me = Auth::guard('partner')->id();
        $incoming = StockTransfer::with(['product', 'toStore'])->where('from_store_id', $me)->latest()->get();
        $outgoing = StockTransfer::with(['product', 'fromStore'])->where('to_store_id', $me)->latest()->get();

        return view('partner.inventory.transfers', compact('incoming', 'outgoing', 'me'));
    }

    /** 상태 전이: approve/reject/ship (from_store) · complete (to_store) */
    public function act(Request $request, StockTransfer $stockTransfer)
    {
        $me = Auth::guard('partner')->id();
        $action = $request->input('action');
        $isFrom = $stockTransfer->from_store_id === $me;
        $isTo   = $stockTransfer->to_store_id === $me;

        if ($action === 'approve' && $isFrom && $stockTransfer->status === 'requested') {
            $stockTransfer->update(['status' => 'approved']);
        } elseif ($action === 'reject' && $isFrom && $stockTransfer->status === 'requested') {
            $stockTransfer->update(['status' => 'rejected']);
        } elseif ($action === 'ship' && $isFrom && $stockTransfer->status === 'approved') {
            $stockTransfer->update(['status' => 'shipping']);
        } elseif ($action === 'complete' && $isTo && $stockTransfer->status === 'shipping') {
            DB::transaction(function () use ($stockTransfer) {
                $from = Inventory::where('store_id', $stockTransfer->from_store_id)->where('product_id', $stockTransfer->product_id)->first();
                if ($from) {
                    $from->decrement('quantity', min($stockTransfer->quantity, $from->quantity));
                }
                $to = Inventory::firstOrCreate(
                    ['store_id' => $stockTransfer->to_store_id, 'product_id' => $stockTransfer->product_id],
                    ['quantity' => 0, 'location' => '입고대기']
                );
                $to->increment('quantity', $stockTransfer->quantity);
                $stockTransfer->update(['status' => 'completed']);
            });
            NotificationService::notify('store', $stockTransfer->from_store_id, 'transfer', '✅ 재고 이동 완료',
                "{$stockTransfer->code} 이동이 완료되었습니다.", route('partner.inventory.transfers'), ['in_app'], '✅');
        } else {
            abort(400, '허용되지 않은 처리입니다.');
        }

        return back()->with('status', '처리되었습니다.');
    }
}

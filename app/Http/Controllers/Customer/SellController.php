<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Partner;
use App\Models\SellRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SellController extends Controller
{
    /** 판매하기 — 상품 등록 폼 */
    public function create()
    {
        return view('customer.sell.create', [
            'categories' => Category::orderBy('sort')->orderBy('name')->get(),
            'stores'     => Partner::where('status', 'approved')->orderBy('company_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'target_type'     => ['required', 'in:head_office,store'],
            'target_store_id' => ['nullable', 'required_if:target_type,store', 'exists:partners,id'],
            'category_id'     => ['nullable', 'exists:categories,id'],
            'brand'           => ['required', 'string', 'max:120'],
            'title'           => ['required', 'string', 'max:200'],
            'description'     => ['nullable', 'string'],
            'method'          => ['required', 'in:quote,auction'],
            'delivery_method' => ['required', 'in:visit,parcel'],
            'visit_at'        => ['nullable', 'date'],
            'desired_price'   => ['nullable', 'integer', 'min:0'],
            'photos'          => ['nullable', 'array', 'max:10'],
            'photos.*'        => ['image', 'max:8192'],
        ]);

        $code = 'SR-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));

        // 사진 여러 장 저장
        $paths = [];
        if ($request->hasFile('photos')) {
            $dir = base_path("assets/uploads/sell/{$code}");
            @mkdir($dir, 0777, true);
            foreach ($request->file('photos') as $i => $file) {
                $name = ($i + 1) . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $name);
                $paths[] = "assets/uploads/sell/{$code}/{$name}";
            }
        }

        // 경매는 특정 지점 지정 없이 여러 지점이 입찰
        $target = $data['method'] === 'auction' ? 'head_office' : $data['target_type'];

        SellRequest::create([
            'code'            => $code,
            'customer_id'     => Auth::guard('web')->id(),
            'target_type'     => $target,
            'target_store_id' => $target === 'store' ? $data['target_store_id'] : null,
            'category_id'     => $data['category_id'] ?? null,
            'brand'           => $data['brand'],
            'title'           => $data['title'],
            'description'     => $data['description'] ?? null,
            'photos'          => $paths,
            'method'          => $data['method'],
            'delivery_method' => $data['delivery_method'],
            'visit_at'        => $data['visit_at'] ?? null,
            'desired_price'   => $data['desired_price'] ?? null,
            'status'          => $data['method'] === 'auction' ? 'auctioning' : 'received',
        ]);

        return redirect()->route('sell.history')
            ->with('status', "판매 접수가 완료되었습니다. 접수번호 {$code}");
    }

    /** 판매 진행현황 */
    public function history()
    {
        $requests = SellRequest::with(['store', 'winningStore', 'certificate'])
            ->where('customer_id', Auth::guard('web')->id())
            ->latest()->paginate(10);

        return view('customer.sell.history', compact('requests'));
    }

    public function show(SellRequest $sellRequest)
    {
        abort_unless($sellRequest->customer_id === Auth::guard('web')->id(), 403);
        $sellRequest->load(['store', 'bids.store', 'winningStore', 'certificate', 'category']);

        return view('customer.sell.show', ['sr' => $sellRequest]);
    }

    /** 고객 승인 — 일반견적 수락 또는 경매 낙찰 지점 선택 */
    public function approve(Request $request, SellRequest $sellRequest)
    {
        abort_unless($sellRequest->customer_id === Auth::guard('web')->id(), 403);

        if ($sellRequest->method === 'auction') {
            $data = $request->validate(['bid_id' => ['required', 'exists:auction_bids,id']]);
            $bid = $sellRequest->bids()->findOrFail($data['bid_id']);

            $sellRequest->bids()->update(['status' => 'lost']);
            $bid->update(['status' => 'won']);
            $sellRequest->update([
                'winning_store_id' => $bid->store_id,
                'quote_price'      => $bid->bid_price,
                'status'           => 'customer_approved',
            ]);
        } else {
            abort_if($sellRequest->status !== 'quoted', 400, '승인 가능한 견적이 없습니다.');
            $sellRequest->update(['status' => 'customer_approved']);
        }

        return back()->with('status', '견적을 승인했습니다. 상품을 지정 방법으로 발송/방문해 주세요.');
    }
}

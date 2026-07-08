<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\SellRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppraisalController extends Controller
{
    /** 접수 현황 / 감정 관리 */
    public function index(Request $request)
    {
        $requests = SellRequest::with(['customer', 'store', 'winningStore', 'certificate'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(15)->withQueryString();

        $counts = SellRequest::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');

        return view('admin.sell.index', compact('requests', 'counts'));
    }

    public function show(SellRequest $sellRequest)
    {
        $sellRequest->load(['customer', 'store', 'bids.store', 'winningStore', 'certificate', 'category']);

        return view('admin.sell.show', ['sr' => $sellRequest]);
    }

    /** 본사 직접 감정 + 견적 (target=head_office) */
    public function appraise(Request $request, SellRequest $sellRequest)
    {
        $data = $request->validate([
            'checklist'        => ['nullable', 'array'],
            'appraisal_result' => ['required', 'in:authentic,fake,uncertain'],
            'appraiser'        => ['required', 'string', 'max:80'],
            'quote_price'      => ['nullable', 'integer', 'min:0'],
            'memo'             => ['nullable', 'string'],
        ]);

        $sellRequest->update([
            'appraisal'        => $data['checklist'] ?? [],
            'appraisal_result' => $data['appraisal_result'],
            'appraiser'        => $data['appraiser'] . ' (MOONS 본사)',
            'quote_price'      => $data['quote_price'] ?? null,
            'memo'             => $data['memo'] ?? null,
            'status'           => $data['appraisal_result'] === 'fake'
                ? 'rejected'
                : ($data['quote_price'] ? 'quoted' : 'appraising'),
        ]);

        return back()->with('status', '감정 결과와 견적을 등록했습니다.');
    }

    /** 감정서(블록체인) 발급 + DPP 생성 + 정산완료 */
    public function issueCertificate(SellRequest $sellRequest)
    {
        abort_if($sellRequest->certificate, 400, '이미 감정서가 발급되었습니다.');
        abort_unless(in_array($sellRequest->status, ['customer_approved', 'inbound']), 400, '입고 이후 발급 가능합니다.');
        abort_if($sellRequest->appraisal_result === 'fake', 400, '가품은 발급 불가합니다.');

        $issuer = $sellRequest->target_type === 'store'
            ? ($sellRequest->store?->company_name ?? '지점')
            : ($sellRequest->winningStore?->company_name ?? 'MOONS 본사');

        $now = now();
        $cert = new Certificate([
            'code'            => 'MOONS-' . $now->format('Y') . '-' . strtoupper(Str::random(6)),
            'sell_request_id' => $sellRequest->id,
            'brand'           => $sellRequest->brand,
            'model'           => $sellRequest->title,
            'category'        => $sellRequest->category?->name,
            'result'          => $sellRequest->appraisal_result === 'pending' ? 'authentic' : $sellRequest->appraisal_result,
            'appraiser'       => $sellRequest->appraiser ?? 'MOONS 감정팀',
            'issuer'          => $issuer,
            'thumbnail'       => $sellRequest->photos[0] ?? null,
            'issued_at'       => $now,
            'dpp'             => [
                ['type' => 'appraisal', 'at' => $now->toDateTimeString(), 'by' => $sellRequest->appraiser ?? 'MOONS', 'note' => '정품 감정 완료'],
                ['type' => 'ownership', 'at' => $now->toDateTimeString(), 'by' => 'MOONS', 'note' => '소유권 이전: 고객 → ' . $issuer],
                ['type' => 'storage',   'at' => $now->toDateTimeString(), 'by' => $issuer, 'note' => '지점 입고·보관'],
            ],
        ]);
        $cert->blockchain_hash = $cert->computeHash();
        $cert->save();

        $sellRequest->update(['status' => 'settled']);

        return back()->with('status', "감정서 {$cert->code} 가 발급되었습니다. (블록체인 해시 저장 완료)");
    }
}

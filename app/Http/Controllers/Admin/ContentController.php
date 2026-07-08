<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Faq;
use App\Models\Notice;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function index()
    {
        return view('admin.content.index', [
            'banners'    => Banner::orderBy('sort')->get(),
            'promotions' => Promotion::latest()->get(),
            'notices'    => Notice::orderByDesc('is_pinned')->latest()->get(),
            'faqs'       => Faq::orderBy('sort')->get(),
        ]);
    }

    public function storeBanner(Request $request)
    {
        $data = $request->validate([
            'eyebrow'  => ['nullable', 'string', 'max:60'],
            'title'    => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'gradient' => ['nullable', 'string', 'max:60'],
            'link'     => ['nullable', 'string', 'max:200'],
            'position' => ['required', 'in:hero,strip'],
            'sort'     => ['nullable', 'integer'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        Banner::create($data);

        return back()->with('status', '배너를 추가했습니다.');
    }

    public function storePromotion(Request $request)
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:120'],
            'subtitle'     => ['nullable', 'string', 'max:200'],
            'description'  => ['nullable', 'string'],
            'gradient'     => ['nullable', 'string', 'max:60'],
            'brand'        => ['nullable', 'string', 'max:80'],
            'min_discount' => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);
        Promotion::create([
            'code'        => 'PR-' . strtoupper(Str::random(5)),
            'title'       => $data['title'],
            'subtitle'    => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'gradient'    => $data['gradient'] ?? '#1a1a2e,#4b1248',
            'filters'     => array_filter(['brand' => $data['brand'] ?? null, 'min_discount' => $data['min_discount'] ?? null]),
        ]);

        return back()->with('status', '기획전을 추가했습니다.');
    }

    public function storeNotice(Request $request)
    {
        Notice::create($request->validate([
            'category'  => ['required', 'in:notice,event'],
            'title'     => ['required', 'string', 'max:200'],
            'body'      => ['nullable', 'string'],
            'is_pinned' => ['nullable', 'boolean'],
        ]));

        return back()->with('status', '공지를 등록했습니다.');
    }

    public function storeFaq(Request $request)
    {
        Faq::create($request->validate([
            'category' => ['required', 'string', 'max:40'],
            'question' => ['required', 'string', 'max:200'],
            'answer'   => ['required', 'string'],
            'sort'     => ['nullable', 'integer'],
        ]));

        return back()->with('status', 'FAQ를 등록했습니다.');
    }

    public function destroy(Request $request, string $type, int $id)
    {
        $model = ['banner' => Banner::class, 'promotion' => Promotion::class, 'notice' => Notice::class, 'faq' => Faq::class][$type] ?? null;
        abort_unless($model, 404);
        $model::findOrFail($id)->delete();

        return back()->with('status', '삭제했습니다.');
    }
}

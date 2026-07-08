<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    /** 감정서 조회 폼 */
    public function index(Request $request)
    {
        if ($code = $request->get('code')) {
            return redirect()->route('verify.show', $code);
        }

        return view('verify.index');
    }

    /** 감정서 + DPP(상품 생애 이력) 공개 검증 */
    public function show(string $code)
    {
        $certificate = Certificate::with('sellRequest')->where('code', $code)->first();

        return view('verify.show', [
            'code'        => $code,
            'certificate' => $certificate,
        ]);
    }

    /** QR 코드 (SVG) — 검증 URL 인코딩 */
    public function qr(string $code)
    {
        $url = route('verify.show', $code);
        $renderer = new ImageRenderer(new RendererStyle(240, 1), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString($url);

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }
}

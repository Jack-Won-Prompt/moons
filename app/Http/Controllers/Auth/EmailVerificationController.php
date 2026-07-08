<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    /** 인증 안내 화면 (데모: 인증 링크를 화면/로그로 제공) */
    public function notice(Request $request)
    {
        $user = Auth::guard('web')->user();
        if ($user && $user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        return view('auth.customer.verify', ['link' => $user ? $this->buildLink($user) : null]);
    }

    /** 인증 링크 클릭 처리 */
    public function verify(Request $request, $id, $hash)
    {
        $user = \App\Models\User::findOrFail($id);
        abort_unless(hash_equals((string) $hash, sha1($user->getEmailForVerification())), 403, '유효하지 않은 인증 링크입니다.');

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        Auth::guard('web')->login($user);

        return redirect()->route('home')->with('status', '이메일 인증이 완료되었습니다.');
    }

    /** 재발송 (데모: 링크를 flash + 로그) */
    public function resend(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (! $user || $user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $link = $this->buildLink($user);
        Log::info("[EMAIL:verify] to {$user->email} :: {$link}");

        return back()->with('verify_link', $link)->with('status', '인증 메일을 재발송했습니다. (데모: 아래 링크로 확인)');
    }

    private function buildLink($user): string
    {
        return URL::temporarySignedRoute('verification.verify', now()->addHours(24), [
            'id'   => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);
    }
}

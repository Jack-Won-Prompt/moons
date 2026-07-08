<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    private const PROVIDERS = ['kakao' => '카카오', 'naver' => '네이버', 'google' => '구글'];

    /**
     * 소셜 로그인 시작.
     * 실제 환경: return Socialite::driver($provider)->redirect();
     * 데모 환경: 소셜 제공자 동의 화면(시뮬레이션)을 표시.
     */
    public function redirect(string $provider)
    {
        abort_unless(isset(self::PROVIDERS[$provider]), 404);

        return view('auth.customer.social', [
            'provider'      => $provider,
            'provider_name' => self::PROVIDERS[$provider],
        ]);
    }

    /**
     * 소셜 로그인 콜백.
     * 실제 환경: $social = Socialite::driver($provider)->user(); 로 프로필 취득.
     * 데모 환경: 제공자별 데모 계정을 생성/로그인.
     */
    public function callback(Request $request, string $provider)
    {
        abort_unless(isset(self::PROVIDERS[$provider]), 404);

        $name  = $request->input('name', self::PROVIDERS[$provider] . ' 데모회원');
        $email = $provider . '@social.moons';

        $user = User::updateOrCreate(
            ['provider' => $provider, 'provider_id' => $email],
            [
                'name'              => $name,
                'email'             => $email,
                'email_verified_at' => now(),   // 소셜 이메일은 인증 완료로 간주
            ]
        );

        Auth::guard('web')->login($user, true);
        $request->session()->regenerate();

        return redirect()->route('home')->with('status', "{$name}님, {$provider} 계정으로 로그인했습니다.");
    }
}

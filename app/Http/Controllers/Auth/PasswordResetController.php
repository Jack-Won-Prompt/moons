<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function request()
    {
        return view('auth.customer.forgot');
    }

    /** 재설정 링크 발송 (데모: 링크를 화면에 표시) */
    public function email(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => '해당 이메일의 계정을 찾을 수 없습니다.']);
        }

        $token = Password::broker()->createToken($user);
        $link  = route('password.reset', ['token' => $token, 'email' => $user->email]);
        Log::info("[EMAIL:password-reset] to {$user->email} :: {$link}");

        return back()->with('reset_link', $link)->with('status', '비밀번호 재설정 링크를 발송했습니다. (데모: 아래 링크로 진행)');
    }

    public function reset(Request $request, string $token)
    {
        return view('auth.customer.reset', ['token' => $token, 'email' => $request->email]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PasswordReset
            ? redirect()->route('login')->with('status', '비밀번호가 변경되었습니다. 다시 로그인해 주세요.')
            : back()->withErrors(['email' => __($status)]);
    }
}

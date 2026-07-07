<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('partner')->check()) {
            return redirect()->route('partner.dashboard');
        }

        return view('partner.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('partner')->attempt($credentials, $request->boolean('remember'))) {
            if (Auth::guard('partner')->user()->status !== 'approved') {
                Auth::guard('partner')->logout();

                return back()->withErrors(['email' => '승인 대기 중이거나 정지된 파트너 계정입니다.'])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('partner.dashboard'));
        }

        return back()->withErrors(['email' => '파트너 인증에 실패했습니다.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('partner.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:partners,email'],
            'phone'        => ['nullable', 'string', 'max:40'],
            'brand'        => ['nullable', 'string', 'max:255'],
            'password'     => ['required', 'confirmed', 'min:6'],
        ]);

        // New partners land in "pending" until an admin approves them.
        $data['status'] = 'pending';
        Partner::create($data);

        return redirect()->route('partner.login')
            ->with('status', '입점 신청이 접수되었습니다. 관리자 승인 후 로그인하실 수 있습니다.');
    }

    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('partner.login');
    }
}

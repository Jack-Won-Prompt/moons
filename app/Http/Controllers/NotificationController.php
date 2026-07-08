<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** 현재 요청 경로 기준으로 역할/ID 판별 */
    private function actor(Request $request): array
    {
        if ($request->is('admin/*') && auth('admin')->check()) {
            return ['admin', auth('admin')->id(), 'layouts.admin'];
        }
        if ($request->is('partner/*') && auth('partner')->check()) {
            return ['store', auth('partner')->id(), 'layouts.partner'];
        }

        return ['customer', auth('web')->id(), 'layouts.storefront'];
    }

    public function index(Request $request)
    {
        [$role, $id] = $this->actor($request);
        $notifications = AppNotification::for($role, $id)->latest()->paginate(20);

        return view('notifications.index', compact('notifications', 'role'));
    }

    /** 폴링 — 미읽음 개수 */
    public function unread(Request $request)
    {
        [$role, $id] = $this->actor($request);

        return response()->json(['count' => AppNotification::for($role, $id)->unread()->count()]);
    }

    public function read(Request $request, AppNotification $notification)
    {
        [$role, $id] = $this->actor($request);
        abort_unless($notification->recipient_role === $role && $notification->recipient_id === $id, 403);
        $notification->update(['read_at' => now()]);

        return $notification->link ? redirect($notification->link) : back();
    }

    public function readAll(Request $request)
    {
        [$role, $id] = $this->actor($request);
        AppNotification::for($role, $id)->unread()->update(['read_at' => now()]);

        return back()->with('status', '모든 알림을 읽음 처리했습니다.');
    }
}

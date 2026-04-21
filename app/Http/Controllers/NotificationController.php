<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::forUser(auth()->id())
            ->latest()
            ->paginate(20);

        return view('notifications', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        // Validasi link: hanya redirect ke URL internal (cegah open redirect)
        if ($notification->link) {
            $parsed = parse_url($notification->link);
            $isInternal = !isset($parsed['host'])
                || $parsed['host'] === request()->getHost();

            if ($isInternal) {
                return redirect($notification->link);
            }
        }

        return back();
    }

    public function markAllAsRead()
    {
        Notification::forUser(auth()->id())->unread()->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Get unread count for AJAX.
     */
    public function unreadCount()
    {
        $count = Notification::forUser(auth()->id())->unread()->count();

        return response()->json(['count' => $count]);
    }
}

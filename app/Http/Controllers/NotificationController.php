<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Mengelola notifikasi in-app (channel 'database'), sebagai pelengkap web push.
 * Lihat penjelasan lengkap alur notifikasi di README bagian 2.1.
 */
class NotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}

<?php

namespace App\Jobs;

use App\Models\Undangan;
use App\Models\User;
use App\Notifications\UndanganBaruNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

/**
 * Dijalankan lewat queue (bukan langsung di request HTTP) karena mengirim web-push
 * adalah panggilan jaringan per-subscription -- bisa lambat kalau user banyak.
 * Jalankan `php artisan queue:work` di server (atau pakai supervisor/horizon).
 */
class SendUndanganNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Undangan $undangan)
    {
    }

    public function handle(): void
    {
        $this->undangan->loadMissing('roles');

        $penerima = $this->undangan->notify_all
            ? User::query()
            : User::whereIn('role_id', $this->undangan->roles->pluck('id'));

        // chunk() supaya tetap ringan walau jumlah user besar (tidak load semua ke memori sekaligus)
        $penerima->chunk(100, function ($users) {
            Notification::send($users, new UndanganBaruNotification($this->undangan));
        });
    }
}

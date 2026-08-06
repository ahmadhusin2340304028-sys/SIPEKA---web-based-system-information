<?php

namespace App\Notifications;

use App\Models\Undangan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class UndanganBaruNotification extends Notification
{
    use Queueable;

    public function __construct(public Undangan $undangan)
    {
    }

    /**
     * database  -> "lonceng notifikasi" in-app (selalu berhasil, tidak tergantung izin browser)
     * WebPush   -> notifikasi push asli ke luar tab browser / saat aplikasi tidak dibuka
     */
    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, self $notification): WebPushMessage
    {
        $pihakTerkait = $this->undangan->roles->pluck('nama')->join(', ');

        // route('undangan.index') dibatasi middleware role:admin. Sebelumnya URL ini
        // dipakai untuk SEMUA penerima, jadi staff (non-admin) yang mengklik notifikasi
        // push akan mendapat error 403. Di sini disamakan dengan link notifikasi in-app
        // di navbar: admin -> halaman Kelola Undangan, staff -> dashboard.
        /** @var User $notifiable */
        $url = $notifiable->isAdmin() ? route('undangan.index') : route('dashboard');

        return (new WebPushMessage)
            ->title('Undangan Baru: '.str($this->undangan->judul_kegiatan)->limit(60))
            ->icon('/assets/image/dinsos_logo.png')
            ->body(
                $this->undangan->tanggal->translatedFormat('d F Y')
                .' • '.$this->undangan->tempat
                ."\nPihak terkait: ".$pihakTerkait
            )
            ->action('Lihat Undangan', 'lihat')
            ->data(['url' => $url])
            ->options(['TTL' => 1000]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'undangan_id' => $this->undangan->id,
            'judul_kegiatan' => $this->undangan->judul_kegiatan,
            'tanggal' => $this->undangan->tanggal->toDateString(),
            'tempat' => $this->undangan->tempat,
            'pihak_terkait' => $this->undangan->roles->pluck('nama'),
        ];
    }
}

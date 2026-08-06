<?php

// File ini normalnya dipublikasikan otomatis lewat:
//   php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider"
// Disertakan di sini sebagai referensi konfigurasi VAPID yang dipakai package
// laravel-notification-channels/webpush. Generate key dengan:
//   php artisan webpush:vapid

return [
    'VAPID' => [
        'subject' => env('APP_URL', 'http://localhost'),
        'publicKey' => env('VAPID_PUBLIC_KEY'),
        'privateKey' => env('VAPID_PRIVATE_KEY'),
        'pemFile' => env('VAPID_PEM_FILE'),
    ],
];

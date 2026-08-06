<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Skema mengikuti konvensi paket laravel-notification-channels/webpush
     * (trait HasPushSubscriptions pada model User membaca tabel ini).
     * Satu user bisa punya banyak subscription (banyak browser/device).
     */
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->morphs('subscribable'); // subscribable_type, subscribable_id -> App\Models\User
            // VARCHAR (bukan TEXT) supaya bisa dipakai di composite unique index di bawah --
            // MySQL/InnoDB menolak TEXT/BLOB dalam index tanpa panjang key eksplisit.
            // 500 karakter cukup untuk endpoint push subscription (FCM/Mozilla/dst).
            $table->string('endpoint', 500);
            $table->string('public_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('content_encoding')->nullable();
            $table->timestamps();

            $table->unique(['subscribable_type', 'subscribable_id', 'endpoint'], 'push_subs_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};

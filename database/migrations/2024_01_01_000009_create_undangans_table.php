<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('undangans', function (Blueprint $table) {
            $table->id();
            $table->text('judul_kegiatan');
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('tempat');
            $table->string('pihak_mengundang');
            $table->enum('status_kegiatan', ['Belum Terlaksana', 'Terlaksana'])->default('Belum Terlaksana');
            $table->foreignId('menghadiri_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('delegasi_keterangan')->nullable();
            $table->string('bukti_path')->nullable();
            // true  = notifikasi push dikirim ke SEMUA user (tetap menampilkan daftar pihak terkait di isi notif)
            // false = notifikasi push hanya dikirim ke user yang role-nya ada di daftar pihak terkait (role_undangan)
            $table->boolean('notify_all')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('undangans');
    }
};

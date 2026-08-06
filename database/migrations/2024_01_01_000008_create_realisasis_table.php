<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ini adalah inti normalisasi: menggantikan 48 kolom lama
     * (realisasi_bulan1..12, realisasi_anggaran_bulan1..12, bukti1..12, keterangan1..12)
     * menjadi satu baris per bulan. Menghilangkan repeating group (pelanggaran 1NF)
     * dan membuat SUM/AVG per rentang bulan bisa dilakukan lewat query agregat biasa,
     * bukan menjumlahkan 12 nama kolom secara manual di PHP/SQL.
     */
    public function up(): void
    {
        Schema::create('realisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_kinerja_id')->constrained('rencana_kinerjas')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->decimal('realisasi_fisik', 15, 2)->nullable();
            $table->decimal('realisasi_anggaran', 15, 2)->nullable();
            $table->string('bukti_path')->nullable();
            $table->text('keterangan')->nullable();
            // Kapan data bulan ini benar-benar diinput/diupdate oleh staff.
            // Dipakai untuk menghitung kriteria "ketepatan waktu" pada metode SAW.
            $table->timestamp('dilaporkan_pada')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['rencana_kinerja_id', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasis');
    }
};

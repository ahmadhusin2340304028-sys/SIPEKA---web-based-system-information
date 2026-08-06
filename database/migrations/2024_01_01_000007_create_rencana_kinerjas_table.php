<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Rencana kinerja" = instance tahunan dari satu sub-kegiatan: target & pagu anggaran tahun berjalan.
     * Ini menggantikan kolom target/pagu_anggaran/tahun yang dulu duplikat di tabel `kegiatan` lama.
     */
    public function up(): void
    {
        Schema::create('rencana_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_kegiatan_id')->constrained('sub_kegiatans')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->decimal('target', 15, 2)->default(0);
            $table->decimal('pagu_anggaran', 15, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['sub_kegiatan_id', 'tahun']);
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rencana_kinerjas');
    }
};

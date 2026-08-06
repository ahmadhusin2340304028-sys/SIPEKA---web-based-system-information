<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bidangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('kode', 40)->unique()->comment('slug, dipakai untuk route binding /bidang/{kode}');
            $table->string('kelompok')->nullable()->comment('Sekretariat | Bidang Sosial | Bidang PM - untuk pengelompokan menu & filter rekap');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bidangs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('slug')->unique();
            // bidang_id terisi hanya untuk 6 role operasional (staff input realisasi).
            // Role struktural (Admin, Kepala Dinas, dst) -> bidang_id null.
            $table->foreignId('bidang_id')->nullable()->constrained('bidangs')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

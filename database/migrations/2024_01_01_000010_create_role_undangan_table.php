<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot many-to-many: menggantikan kolom `bidang_terkait` (string comma-separated) lama.
     */
    public function up(): void
    {
        Schema::create('role_undangan', function (Blueprint $table) {
            $table->foreignId('undangan_id')->constrained('undangans')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['undangan_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_undangan');
    }
};

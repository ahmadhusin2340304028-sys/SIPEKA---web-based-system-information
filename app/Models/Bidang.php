<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bidang extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'kode', 'kelompok'];

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Route-model binding memakai kolom `kode` (bukan id) supaya URL rapi: /bidang/kepegawaian
     */
    public function getRouteKeyName(): string
    {
        return 'kode';
    }
}

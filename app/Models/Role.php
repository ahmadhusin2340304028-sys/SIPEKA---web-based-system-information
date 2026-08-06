<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'slug', 'bidang_id'];

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function undangans(): BelongsToMany
    {
        return $this->belongsToMany(Undangan::class, 'role_undangan');
    }

    public function isAdmin(): bool
    {
        return $this->slug === 'admin';
    }

    /**
     * Role operasional = role yang terhubung ke satu bidang (staff input realisasi).
     * Role struktural (Admin, Kepala Dinas, dst) -> bidang_id null.
     */
    public function isOperational(): bool
    {
        return $this->bidang_id !== null;
    }
}

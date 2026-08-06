<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Undangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_kegiatan',
        'tanggal',
        'waktu',
        'tempat',
        'pihak_mengundang',
        'status_kegiatan',
        'menghadiri_user_id',
        'delegasi_keterangan',
        'bukti_path',
        'notify_all',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'notify_all' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_undangan');
    }

    public function menghadiri(): BelongsTo
    {
        return $this->belongsTo(User::class, 'menghadiri_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Filter undangan yang pihak-terkait-nya mencakup role tertentu.
     * Dipakai supaya staff non-admin hanya melihat undangan yang relevan dengan role-nya.
     */
    public function scopeUntukRole(Builder $query, Role $role): Builder
    {
        return $query->whereHas('roles', fn (Builder $q) => $q->where('roles.id', $role->id));
    }
}

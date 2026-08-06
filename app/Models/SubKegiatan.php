<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubKegiatan extends Model
{
    use HasFactory;

    protected $fillable = ['kegiatan_id', 'nama', 'sasaran_strategis', 'indikator_kinerja', 'satuan'];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function rencanaKinerjas(): HasMany
    {
        return $this->hasMany(RencanaKinerja::class);
    }

    /**
     * Scope untuk filter dropdown "Sub Kegiatan" per bidang, dipakai oleh BidangRealisasiController.
     * Menembus relasi kegiatan -> program -> bidang lewat whereHas (tetap 1 query, tidak N+1).
     */
    public function scopeUntukBidang(Builder $query, Bidang $bidang): Builder
    {
        return $query->whereHas(
            'kegiatan.program',
            fn (Builder $q) => $q->where('bidang_id', $bidang->id)
        );
    }
}

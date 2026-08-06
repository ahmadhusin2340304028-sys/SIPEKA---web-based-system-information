<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu baris = realisasi kinerja+anggaran+bukti+keterangan untuk SATU sub-kegiatan pada SATU bulan.
 * Menggantikan kolom realisasi_bulanN / realisasi_anggaran_bulanN / buktiN / keteranganN (N=1..12)
 * pada tabel `kegiatan` lama.
 */
class Realisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'rencana_kinerja_id',
        'bulan',
        'realisasi_fisik',
        'realisasi_anggaran',
        'bukti_path',
        'keterangan',
        'dilaporkan_pada',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'bulan' => 'integer',
            'realisasi_fisik' => 'decimal:2',
            'realisasi_anggaran' => 'decimal:2',
            'dilaporkan_pada' => 'datetime',
        ];
    }

    public function rencanaKinerja(): BelongsTo
    {
        return $this->belongsTo(RencanaKinerja::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * RencanaKinerja = target & pagu anggaran satu sub-kegiatan pada satu tahun.
 * Ini menggantikan baris lebar `kegiatan` lama (yang punya 48 kolom bulanan).
 * Data bulanan sekarang ada di relasi realisasis().
 */
class RencanaKinerja extends Model
{
    use HasFactory;

    protected $fillable = ['sub_kegiatan_id', 'tahun', 'target', 'pagu_anggaran', 'created_by'];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'target' => 'decimal:2',
            'pagu_anggaran' => 'decimal:2',
        ];
    }

    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class);
    }

    public function realisasis(): HasMany
    {
        return $this->hasMany(Realisasi::class)->orderBy('bulan');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Susun realisasi per bulan 1..12, mengisi bulan yang belum ada data dengan null.
     * Dipakai di halaman detail (menggantikan akses langsung ke $data['realisasi_bulan1'] dst).
     * realisasis harus sudah di-eager-load sebelum memanggil ini agar tidak query per bulan.
     */
    public function realisasiPerBulan(): array
    {
        $existing = $this->realisasis->keyBy('bulan');

        $hasil = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $hasil[$bulan] = $existing->get($bulan);
        }

        return $hasil;
    }

    public function totalRealisasiFisik(): float
    {
        return (float) $this->realisasis->sum('realisasi_fisik');
    }

    public function totalRealisasiAnggaran(): float
    {
        return (float) $this->realisasis->sum('realisasi_anggaran');
    }

    public function persenKinerja(): float
    {
        if ((float) $this->target <= 0) {
            return 0.0;
        }

        return round(min(100, ($this->totalRealisasiFisik() / (float) $this->target) * 100), 2);
    }

    public function persenAnggaran(): float
    {
        if ((float) $this->pagu_anggaran <= 0) {
            return 0.0;
        }

        return round(min(100, ($this->totalRealisasiAnggaran() / (float) $this->pagu_anggaran) * 100), 2);
    }

    /**
     * Data akumulasi per triwulan (TW1 = bulan 1-3, TW2 = bulan 4-6, dst), mereplikasi
     * logika `$tw[$i]` pada aplikasi lama (kepegawaian.php, pemberdayaan.php, dst):
     * setiap triwulan menampilkan realisasi PADA triwulan itu saja, tapi sisa target/sisa
     * pagu/persentase bersifat KUMULATIF sejak awal tahun sampai triwulan tsb.
     *
     * realisasis harus sudah di-eager-load sebelum memanggil ini (hindari query per bulan).
     */
    public function triwulanData(): array
    {
        $mappingTw = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        $realisasiPerBulan = $this->realisasis->keyBy('bulan');
        $target = (float) $this->target;
        $pagu = (float) $this->pagu_anggaran;

        $totalRealisasiAnggaran = 0.0;
        $totalRealisasiTarget = 0.0;
        $hasil = [];

        foreach ($mappingTw as $tw => $bulanList) {
            $realisasiAnggaranTw = 0.0;
            $realisasiTargetTw = 0.0;

            foreach ($bulanList as $bulan) {
                $r = $realisasiPerBulan->get($bulan);
                $realisasiTargetTw += (float) ($r->realisasi_fisik ?? 0);
                $realisasiAnggaranTw += (float) ($r->realisasi_anggaran ?? 0);
            }

            $totalRealisasiAnggaran += $realisasiAnggaranTw;
            $totalRealisasiTarget += $realisasiTargetTw;

            $hasil[$tw] = [
                'bulan_list' => $bulanList,
                'realisasi_anggaran_tw' => $realisasiAnggaranTw ?: null,
                'realisasi_target_tw' => $realisasiTargetTw ?: null,
                'sisa_anggaran' => $pagu - $totalRealisasiAnggaran,
                'sisa_target' => $target - $totalRealisasiTarget,
                'persentase_anggaran' => ($pagu > 0 && $realisasiAnggaranTw > 0)
                    ? round(($totalRealisasiAnggaran / $pagu) * 100, 2)
                    : null,
                'persentase_target' => ($target > 0 && $realisasiTargetTw > 0)
                    ? round(($totalRealisasiTarget / $target) * 100, 2)
                    : null,
            ];
        }

        return $hasil;
    }
}

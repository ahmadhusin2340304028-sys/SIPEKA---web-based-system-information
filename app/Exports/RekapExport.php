<?php

namespace App\Exports;

use App\Models\RencanaKinerja;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Menggantikan export.php lama (yang mencetak <table> HTML mentah dan
 * menyuntikkan header "Content-Type: application/vnd.ms-excel" secara manual).
 * Laravel Excel menghasilkan .xlsx asli dan bisa dipakai ulang untuk export lain
 * tinggal ganti query-nya (mis. RekapExport khusus per-bulan) tanpa duplikasi HTML.
 */
class RekapExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Bidang', 'Program', 'Kegiatan', 'Sub Kegiatan',
            'Target', 'Realisasi Kinerja', 'Sisa Target', '% Kinerja',
            'Pagu Anggaran', 'Realisasi Anggaran', 'Sisa Pagu', '% Anggaran',
        ];
    }

    public function map($rk): array
    {
        /** @var RencanaKinerja $rk */
        $target = (float) $rk->target;
        $fisik = (float) $rk->total_realisasi_fisik;
        $pagu = (float) $rk->pagu_anggaran;
        $anggaran = (float) $rk->total_realisasi_anggaran;

        return [
            $rk->subKegiatan->kegiatan->program->bidang->nama,
            $rk->subKegiatan->kegiatan->program->nama,
            $rk->subKegiatan->kegiatan->nama,
            $rk->subKegiatan->nama,
            $target,
            $fisik,
            $target - $fisik,
            $target > 0 ? round($fisik / $target * 100, 2) : 0,
            $pagu,
            $anggaran,
            $pagu - $anggaran,
            $pagu > 0 ? round($anggaran / $pagu * 100, 2) : 0,
        ];
    }
}

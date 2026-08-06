<?php

namespace App\Services;

use App\Models\Bidang;
use App\Models\RencanaKinerja;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Metode Simple Additive Weighting (SAW) untuk merangking bidang/seksi berdasarkan:
 *
 *   C1 - % realisasi kinerja (fisik) terhadap target   -> bobot 0.35 (benefit)
 *   C2 - % realisasi anggaran terhadap pagu anggaran   -> bobot 0.35 (benefit)
 *   C3 - % ketepatan waktu pelaporan bulanan            -> bobot 0.30 (benefit)
 *
 * Langkah SAW yang dipakai (standar untuk kriteria benefit):
 *   1. Hitung nilai mentah tiap kriteria per bidang (x_ij).
 *   2. Normalisasi: r_ij = x_ij / max(x_j)  -- dibagi nilai TERTINGGI antar bidang pada kriteria itu.
 *   3. Skor akhir: V_i = SUM(w_j * r_ij) untuk semua kriteria j.
 *   4. Urutkan V_i menurun -> ranking 1 = bidang terbaik.
 *
 * Catatan desain:
 * - C1 dihitung sebagai RATA-RATA persentase capaian per sub-kegiatan (bukan total realisasi / total target),
 *   karena satuan tiap sub-kegiatan berbeda-beda (Orang, Dokumen, %, dst) sehingga menjumlahkan angka mentah
 *   lintas satuan tidak valid. Merata-ratakan persentase per item membuatnya unit-agnostic.
 * - C2 boleh dijumlahkan langsung (Rupiah, satuan sama untuk semua sub-kegiatan).
 * - C3 dihitung dari kapan staff benar-benar menginput data bulan tsb (`dilaporkan_pada`)
 *   dibandingkan tenggat waktu = akhir bulan bersangkutan + N hari (config('sipeka.batas_hari_lapor')).
 *
 * Query dijaga tetap efisien: hanya SATU query (dengan eager load) untuk mengambil seluruh
 * rencana_kinerjas + realisasis tahun berjalan, sisanya dihitung di memori (bukan query per bidang).
 */
class SawService
{
    public function rank(int $tahun): Collection
    {
        $bidangs = Bidang::orderBy('nama')->get();

        $rencanaPerBidang = RencanaKinerja::query()
            ->where('tahun', $tahun)
            ->with(['realisasis', 'subKegiatan.kegiatan.program'])
            ->get()
            ->groupBy(fn (RencanaKinerja $rk) => $rk->subKegiatan->kegiatan->program->bidang_id);

        $batasHari = (int) config('sipeka.batas_hari_lapor', 10);
        $bulanBerjalan = $tahun === now()->year ? now()->month : 12;

        $mentah = $bidangs->map(function (Bidang $bidang) use ($rencanaPerBidang, $batasHari, $bulanBerjalan) {
            $items = $rencanaPerBidang->get($bidang->id, collect());

            if ($items->isEmpty()) {
                return [
                    'bidang' => $bidang,
                    'c1_kinerja' => 0.0,
                    'c2_anggaran' => 0.0,
                    'c3_ketepatan' => 0.0,
                    'jumlah_kegiatan' => 0,
                ];
            }

            return [
                'bidang' => $bidang,
                'c1_kinerja' => $this->hitungC1Kinerja($items),
                'c2_anggaran' => $this->hitungC2Anggaran($items),
                'c3_ketepatan' => $this->hitungC3Ketepatan($items, $batasHari, $bulanBerjalan),
                'jumlah_kegiatan' => $items->count(),
            ];
        });

        return $this->normalisasiDanRanking($mentah);
    }

    private function hitungC1Kinerja(Collection $items): float
    {
        $persenList = $items
            ->map(function (RencanaKinerja $rk) {
                if ((float) $rk->target <= 0) {
                    return null;
                }

                $totalFisik = $rk->realisasis->sum('realisasi_fisik');

                return min(100, ($totalFisik / (float) $rk->target) * 100);
            })
            ->filter(fn ($v) => $v !== null);

        return $persenList->isNotEmpty() ? round($persenList->avg(), 2) : 0.0;
    }

    private function hitungC2Anggaran(Collection $items): float
    {
        $totalAnggaran = $items->sum(fn (RencanaKinerja $rk) => $rk->realisasis->sum('realisasi_anggaran'));
        $totalPagu = $items->sum(fn (RencanaKinerja $rk) => (float) $rk->pagu_anggaran);

        if ($totalPagu <= 0) {
            return 0.0;
        }

        return round(min(100, ($totalAnggaran / $totalPagu) * 100), 2);
    }

    private function hitungC3Ketepatan(Collection $items, int $batasHari, int $bulanBerjalan): float
    {
        $tepatWaktu = 0;
        $wajibLapor = 0;

        foreach ($items as $rk) {
            /** @var RencanaKinerja $rk */
            $realisasiByBulan = $rk->realisasis->keyBy('bulan');

            for ($bulan = 1; $bulan <= $bulanBerjalan; $bulan++) {
                $wajibLapor++;

                $deadline = Carbon::create((int) $rk->tahun, $bulan, 1)->addMonthNoOverflow()->addDays($batasHari);
                $realisasi = $realisasiByBulan->get($bulan);

                $sudahLapor = $realisasi
                    && $realisasi->realisasi_fisik !== null
                    && $realisasi->dilaporkan_pada !== null;

                if ($sudahLapor && $realisasi->dilaporkan_pada->lte($deadline)) {
                    $tepatWaktu++;
                }
            }
        }

        return $wajibLapor > 0 ? round(($tepatWaktu / $wajibLapor) * 100, 2) : 0.0;
    }

    private function normalisasiDanRanking(Collection $mentah): Collection
    {
        $max1 = $mentah->max('c1_kinerja') ?: 1;
        $max2 = $mentah->max('c2_anggaran') ?: 1;
        $max3 = $mentah->max('c3_ketepatan') ?: 1;

        $bobot = config('sipeka.saw');

        $ranked = $mentah->map(function (array $row) use ($max1, $max2, $max3, $bobot) {
            $r1 = $row['c1_kinerja'] / $max1;
            $r2 = $row['c2_anggaran'] / $max2;
            $r3 = $row['c3_ketepatan'] / $max3;

            $skor = ($r1 * $bobot['bobot_kinerja'])
                + ($r2 * $bobot['bobot_anggaran'])
                + ($r3 * $bobot['bobot_ketepatan_waktu']);

            return array_merge($row, [
                'r1' => round($r1, 4),
                'r2' => round($r2, 4),
                'r3' => round($r3, 4),
                'skor' => round($skor, 4),
            ]);
        })->sortByDesc('skor')->values();

        return $ranked->map(function (array $row, int $idx) {
            $row['rank'] = $idx + 1;

            return $row;
        });
    }
}

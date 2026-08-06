<?php

namespace App\Http\Controllers;

use App\Exports\RekapExport;
use App\Models\RencanaKinerja;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);
        $kelompok = $request->get('kelompok');

        $query = $this->baseQuery($tahun, $kelompok);
        $rencana = $query->get();

        return view('rekap.index', [
            'rencana' => $rencana,
            'tahun' => $tahun,
            'kelompok' => $kelompok,
            'totalKegiatan' => $rencana->count(),
            'totalPagu' => $rencana->sum('pagu_anggaran'),
            'totalRealisasiAnggaran' => $rencana->sum('total_realisasi_anggaran'),
        ]);
    }

    public function export(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);
        $kelompok = $request->get('kelompok');

        return Excel::download(
            new RekapExport($this->baseQuery($tahun, $kelompok)->get()),
            "rekap_kegiatan_{$tahun}.xlsx"
        );
    }

    private function baseQuery(int $tahun, ?string $kelompok)
    {
        $query = RencanaKinerja::with('subKegiatan.kegiatan.program.bidang')
            ->withSum('realisasis as total_realisasi_fisik', 'realisasi_fisik')
            ->withSum('realisasis as total_realisasi_anggaran', 'realisasi_anggaran')
            ->where('tahun', $tahun);

        if ($kelompok) {
            $query->whereHas(
                'subKegiatan.kegiatan.program.bidang',
                fn ($q) => $q->where('kelompok', $kelompok)
            );
        }

        return $query;
    }
}

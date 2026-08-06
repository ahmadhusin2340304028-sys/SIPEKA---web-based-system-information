<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRealisasiRequest;
use App\Models\Bidang;
use App\Models\RencanaKinerja;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Menggantikan 12 file duplikat pada aplikasi lama:
 *   - editKepegawaian.php, editPemberdayaan.php, editPerlindungan.php,
 *     editPerencanaan.php, editRehabilitasi.php, EditPM.php
 *   - kepegawaian.php, pemberdayaan.php, perlindungan.php,
 *     perencanaan.php, rehabilitasi.php, pemberdayaanMasyarakat.php
 *
 * Keenam file itu ~95% identik, hanya berbeda pada nama bidang yang di-hardcode.
 * Di sini cukup satu controller + 2 view Blade, di-parameterisasi lewat route model
 * binding {bidang} (kolom `kode`). Menambah bidang baru = tambah 1 baris seeder,
 * tanpa menyentuh kode sama sekali.
 */
class BidangRealisasiController extends Controller
{
    public function index(Request $request, Bidang $bidang)
    {
        $subKegiatans = SubKegiatan::untukBidang($bidang)
            ->whereHas('rencanaKinerjas', fn ($q) => $q->where('tahun', '>=', now()->year - 4))
            ->orderBy('nama')
            ->get();

        $tahunList = RencanaKinerja::whereHas(
                'subKegiatan',
                fn ($q) => $q->untukBidang($bidang)
            )
            ->where('tahun', '>=', now()->year - 4)
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $rencana = null;

        if ($request->filled('sub_kegiatan_id') && $request->filled('tahun')) {
            $rencana = RencanaKinerja::with('realisasis')
                ->where('sub_kegiatan_id', $request->sub_kegiatan_id)
                ->where('tahun', $request->tahun)
                ->first();
        }

        return view('bidang.index', [
            'bidang' => $bidang,
            'subKegiatans' => $subKegiatans,
            'tahunList' => $tahunList,
            'rencana' => $rencana,
            'canInput' => Auth::user()->canInputBidang($bidang),
        ]);
    }

    public function edit(Request $request, Bidang $bidang)
    {
        $this->authorizeInput($bidang);

        $subKegiatans = SubKegiatan::untukBidang($bidang)->orderBy('nama')->get();

        $tahunList = RencanaKinerja::whereHas('subKegiatan', fn ($q) => $q->untukBidang($bidang))
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $rencana = null;
        $realisasiBulan = null;

        if ($request->filled('sub_kegiatan_id') && $request->filled('tahun')) {
            $rencana = RencanaKinerja::with('realisasis')
                ->where('sub_kegiatan_id', $request->sub_kegiatan_id)
                ->where('tahun', $request->tahun)
                ->first();

            if ($rencana && $request->filled('bulan')) {
                $realisasiBulan = $rencana->realisasis->firstWhere('bulan', (int) $request->bulan);
            }
        }

        return view('bidang.edit', compact('bidang', 'subKegiatans', 'tahunList', 'rencana', 'realisasiBulan'));
    }

    public function update(StoreRealisasiRequest $request, Bidang $bidang)
    {
        $this->authorizeInput($bidang);

        $rencana = RencanaKinerja::whereHas('subKegiatan', fn ($q) => $q->untukBidang($bidang))
            ->findOrFail($request->rencana_kinerja_id);

        $realisasi = $rencana->realisasis()->firstOrNew(['bulan' => $request->bulan]);
        $realisasi->realisasi_fisik = $request->realisasi_fisik;
        $realisasi->realisasi_anggaran = $request->realisasi_anggaran;
        $realisasi->keterangan = $request->keterangan;
        $realisasi->dilaporkan_pada = now();
        $realisasi->updated_by = Auth::id();

        if ($request->hasFile('bukti')) {
            if ($realisasi->bukti_path) {
                Storage::disk('public')->delete($realisasi->bukti_path);
            }
            $realisasi->bukti_path = $request->file('bukti')->store('bukti-realisasi', 'public');
        }

        $realisasi->save();

        // Kembali ke halaman index dengan sub_kegiatan_id & tahun yang sama sudah terisi di
        // query string, supaya data yang baru saja diedit langsung tampil -- staff tidak perlu
        // memilih ulang Sub Kegiatan + Tahun dari dropdown seperti sebelumnya.
        return redirect()
            ->route('bidang.index', [
                'bidang' => $bidang,
                'sub_kegiatan_id' => $rencana->sub_kegiatan_id,
                'tahun' => $rencana->tahun,
            ])
            ->with('notif', ['type' => 'success', 'message' => 'Data berhasil disimpan!']);
    }

    private function authorizeInput(Bidang $bidang): void
    {
        abort_unless(
            Auth::user()->canInputBidang($bidang),
            403,
            'Hanya staff '.$bidang->nama.' yang dapat mengisi data ini.'
        );
    }
}

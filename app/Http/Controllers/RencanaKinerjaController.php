<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRencanaKinerjaRequest;
use App\Models\Bidang;
use App\Models\Kegiatan;
use App\Models\Program;
use App\Models\RencanaKinerja;
use App\Models\SubKegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CRUD data master kegiatan untuk Admin. Menggantikan input_data.php.
 * Form tetap satu layar (sama seperti sebelumnya) untuk UX yang identik,
 * tapi di belakang layar disebar ke 4 tabel ternormalisasi lewat firstOrCreate,
 * supaya Program/Kegiatan yang sama tidak tersimpan berulang sebagai teks berbeda.
 */
class RencanaKinerjaController extends Controller
{
    public function index()
    {
        // withSum -> subquery tunggal, BUKAN query per baris (tidak N+1) walau tabelnya besar.
        $rencana = RencanaKinerja::with('subKegiatan.kegiatan.program.bidang')
            ->withSum('realisasis as total_realisasi_fisik', 'realisasi_fisik')
            ->withSum('realisasis as total_realisasi_anggaran', 'realisasi_anggaran')
            ->latest('created_at')
            ->paginate(20);

        $bidangs = Bidang::orderBy('nama')->get();

        return view('kegiatan.index', compact('rencana', 'bidangs'));
    }

    public function store(StoreRencanaKinerjaRequest $request)
    {
        DB::transaction(function () use ($request) {
            $bidang = Bidang::findOrFail($request->bidang_id);

            $program = Program::firstOrCreate([
                'bidang_id' => $bidang->id,
                'nama' => $request->program,
            ]);

            $kegiatan = Kegiatan::firstOrCreate([
                'program_id' => $program->id,
                'nama' => $request->kegiatan,
            ]);

            $subKegiatan = SubKegiatan::firstOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'nama' => $request->subkegiatan],
                [
                    'sasaran_strategis' => $request->sasaran,
                    'indikator_kinerja' => $request->indikator,
                    'satuan' => $request->satuan,
                ]
            );

            RencanaKinerja::updateOrCreate(
                ['sub_kegiatan_id' => $subKegiatan->id, 'tahun' => $request->tahun],
                [
                    'target' => $request->target,
                    'pagu_anggaran' => $request->pagu_anggaran,
                    'created_by' => Auth::id(),
                ]
            );
        });

        return back()->with('notif', ['type' => 'success', 'message' => 'Data berhasil ditambah!']);
    }

    public function update(StoreRencanaKinerjaRequest $request, RencanaKinerja $rencanaKinerja)
    {
        DB::transaction(function () use ($request, $rencanaKinerja) {
            $bidang = Bidang::findOrFail($request->bidang_id);

            $program = Program::firstOrCreate(['bidang_id' => $bidang->id, 'nama' => $request->program]);
            $kegiatan = Kegiatan::firstOrCreate(['program_id' => $program->id, 'nama' => $request->kegiatan]);

            $subKegiatan = $rencanaKinerja->subKegiatan;
            $subKegiatan->update([
                'kegiatan_id' => $kegiatan->id,
                'nama' => $request->subkegiatan,
                'sasaran_strategis' => $request->sasaran,
                'indikator_kinerja' => $request->indikator,
                'satuan' => $request->satuan,
            ]);

            $rencanaKinerja->update([
                'tahun' => $request->tahun,
                'target' => $request->target,
                'pagu_anggaran' => $request->pagu_anggaran,
            ]);
        });

        return back()->with('notif', ['type' => 'success', 'message' => 'Data berhasil diubah!']);
    }

    public function destroy(RencanaKinerja $rencanaKinerja)
    {
        $rencanaKinerja->delete();

        return back()->with('notif', ['type' => 'warning', 'message' => 'Data berhasil dihapus!']);
    }
}

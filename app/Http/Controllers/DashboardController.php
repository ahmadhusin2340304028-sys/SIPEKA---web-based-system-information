<?php

namespace App\Http\Controllers;

use App\Models\RencanaKinerja;
use App\Models\Undangan;
use App\Services\SawService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private SawService $saw)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = (int) $request->get('tahun', now()->year);

        $ranking = $this->saw->rank($tahun);
        $bidangTerbaik = $ranking->first();

        // Sub kegiatan yang sampai saat ini belum ada realisasi fisik sama sekali tahun berjalan.
        // whereDoesntHave -> tetap satu query (LEFT JOIN + IS NULL di belakang layar), tidak N+1.
        $subKegiatanNol = RencanaKinerja::where('tahun', $tahun)
            ->whereDoesntHave('realisasis', fn ($q) => $q->where('realisasi_fisik', '>', 0))
            ->count();

        $undanganQuery = Undangan::query()->with('roles');

        if (! $user->isAdmin()) {
            $undanganQuery
                ->untukRole($user->role)
                ->where('status_kegiatan', $request->get('status', 'Belum Terlaksana'));
        }

        if ($search = $request->get('search')) {
            $undanganQuery->where(function ($q) use ($search) {
                $q->where('judul_kegiatan', 'like', "%{$search}%")
                    ->orWhere('tempat', 'like', "%{$search}%")
                    ->orWhere('pihak_mengundang', 'like', "%{$search}%");
            });
        }

        $undangans = $undanganQuery->latest('tanggal')->paginate(5)->withQueryString();

        return view('dashboard', [
            'ranking' => $ranking,
            'bidangTerbaik' => $bidangTerbaik,
            'subKegiatanNol' => $subKegiatanNol,
            'totalUndangan' => Undangan::count(),
            'undangans' => $undangans,
            'isAdmin' => $user->isAdmin(),
            'tahun' => $tahun,
        ]);
    }
}

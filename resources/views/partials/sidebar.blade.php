@php
    use App\Models\Bidang;

    $user = auth()->user();
    $roleSlug = $user->role->slug;
    $currentRoute = Route::currentRouteName();
    $currentBidangId = request()->route('bidang')?->id;

    // Bidang dikelompokkan lewat kolom `kelompok` (lihat BidangSeeder), bukan hardcode nama.
    $sekretariat = Bidang::where('kelompok', 'Sekretariat')->orderBy('nama')->get();
    $bidangSosial = Bidang::where('kelompok', 'Bidang Sosial')->orderBy('nama')->get();
    $bidangPM = Bidang::where('kelompok', 'Bidang PM')->orderBy('nama')->first();

    // Meniru logika visibilitas submenu pada sidebar.php lama: role eksekutif tertentu
    // tidak perlu melihat submenu input yang bukan wewenangnya.
    $hideSekretariat = in_array($roleSlug, ['kepala-dinas', 'kepala-bidang-sosial', 'kepala-bidang-pemberdayaan-masyarakat', 'admin']);
    $hideBidangSosial = in_array($roleSlug, ['kepala-bidang-pemberdayaan-masyarakat', 'kepala-dinas', 'admin']);
    $hideBidangPM = in_array($roleSlug, ['kepala-bidang-sosial', 'kepala-dinas', 'admin']);

    $sekretariatOpen = $currentBidangId && $sekretariat->contains('id', $currentBidangId);
    $bidangSosialOpen = $currentBidangId && $bidangSosial->contains('id', $currentBidangId);
@endphp
<div class="sidebar">
    <div class="sidebar-brand d-flex align-items-center ms-3 mb-3">
        <img src="{{ asset('assets/image/dinsos_logo.png') }}" width="42" alt="">
        <div class="ms-2">
            <h5 class="mb-0 border-bottom pb-1">SIPEKA</h5>
            <small class="text-white-50">Dinsos - PM</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="{{ $currentRoute === 'dashboard' ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="{{ route('rekap.index') }}" class="{{ $currentRoute === 'rekap.index' ? 'active' : '' }}">
            <i class="bi bi-folder-check"></i> Rekapitulasi
        </a>

        @if ($user->isAdmin())
            <a href="{{ route('rencana-kinerja.index') }}" class="{{ $currentRoute === 'rencana-kinerja.index' ? 'active' : '' }}">
                <i class="bi bi-file-earmark-plus"></i> Kelola Kegiatan
            </a>
            <a href="{{ route('undangan.index') }}" class="{{ $currentRoute === 'undangan.index' ? 'active' : '' }}">
                <i class="bi bi-envelope-plus"></i> Kelola Undangan
            </a>
        @endif

        @unless ($hideSekretariat)
            <a class="d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
               href="#sekretariatMenu" role="button"
               aria-expanded="{{ $sekretariatOpen ? 'true' : 'false' }}">
                <span><i class="bi bi-briefcase"></i> Sekretariat</span>
                <i class="bi bi-caret-down-fill small"></i>
            </a>
            <div class="collapse submenu {{ $sekretariatOpen ? 'show' : '' }}" id="sekretariatMenu">
                @foreach ($sekretariat as $b)
                    <a href="{{ route('bidang.index', $b) }}" class="{{ $currentBidangId === $b->id ? 'active' : '' }}">
                        {{ $b->nama }}
                    </a>
                @endforeach
            </div>
        @endunless

        @unless ($hideBidangSosial)
            <a class="d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
               href="#bidangSosialMenu" role="button"
               aria-expanded="{{ $bidangSosialOpen ? 'true' : 'false' }}">
                <span><i class="bi bi-diagram-3-fill"></i> Bidang Sosial</span>
                <i class="bi bi-caret-down-fill small"></i>
            </a>
            <div class="collapse submenu {{ $bidangSosialOpen ? 'show' : '' }}" id="bidangSosialMenu">
                @foreach ($bidangSosial as $b)
                    <a href="{{ route('bidang.index', $b) }}" class="{{ $currentBidangId === $b->id ? 'active' : '' }}">
                        {{ $b->nama }}
                    </a>
                @endforeach
            </div>
        @endunless

        @if (!$hideBidangPM && $bidangPM)
            {{-- Pemberdayaan Masyarakat berdiri sendiri (bukan bagian dari kelompok lain), jadi tautan langsung, tanpa dropdown --}}
            <a href="{{ route('bidang.index', $bidangPM) }}" class="{{ $currentBidangId === $bidangPM->id ? 'active' : '' }}">
                <i class="bi bi-person-rolodex"></i> {{ $bidangPM->nama }}
            </a>
        @endif
    </nav>
</div>

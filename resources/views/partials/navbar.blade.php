@php
    $user = auth()->user();
    $jabatan = explode(' ', $user->name)[0] ?? $user->name;
    $unread = $user->unreadNotifications;
@endphp
<nav class="app-navbar navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-icon-nav d-lg-none" type="button" id="sidebarToggle"
                    aria-label="Buka/tutup menu" aria-expanded="false">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h5 class="mb-0 text-white">@yield('page-header', 'SIPEKA')</h5>
        </div>

        <div class="d-flex align-items-center gap-2 gap-md-3 ms-auto">
            <span class="text-white-50 small d-none d-lg-inline" id="currentDateTime">
                <i class="bi bi-clock"></i> --
            </span>

            {{-- Notifikasi in-app (pelengkap web push -- lihat README §2.1) --}}
            <div class="dropdown">
                <button class="btn btn-icon-nav position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    @if ($unread->count())
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unread->count() }}
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" style="width:320px; max-height:400px; overflow-y:auto;">
                    <div class="d-flex justify-content-between align-items-center px-2 pb-2 border-bottom mb-1">
                        <strong class="small">Notifikasi</strong>
                        @if ($unread->count())
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <button class="btn btn-link btn-sm p-0 text-decoration-none">Tandai semua dibaca</button>
                            </form>
                        @endif
                    </div>
                    @forelse ($user->notifications()->latest()->take(8)->get() as $n)
                        <div class="dropdown-item small py-2 rounded {{ $n->read_at ? '' : 'bg-light fw-semibold' }}">
                            <a href="{{ $user->isAdmin() ? route('undangan.index') : route('dashboard') }}" class="text-decoration-none text-dark">
                                <i class="bi bi-envelope-paper text-primary me-1"></i>
                                {{ $n->data['judul_kegiatan'] ?? 'Undangan baru' }}
                                <div class="text-muted" style="font-size:11px;">{{ $n->created_at->diffForHumans() }}</div>
                            </a>
                        </div>
                    @empty
                        <p class="text-muted small px-2 mb-0 py-2">Belum ada notifikasi.</p>
                    @endforelse
                </div>
            </div>

            {{-- Akun --}}
            <div class="dropdown account-dropdown">
                <button class="btn btn-icon-nav d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5 me-1"></i>
                    <span class="d-none d-md-inline small">Hallo, {{ $user->name }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-3" style="min-width:230px;">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-circle fs-1 text-primary me-2"></i>
                        <div>
                            <strong class="d-block">{{ $jabatan }}</strong>
                            <span class="text-muted small">{{ $user->role->nama }}</span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger px-0">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

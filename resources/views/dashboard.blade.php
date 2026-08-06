@extends('layouts.app')
@section('title', 'Dashboard - SIPEKA')
@section('page-header', 'Dashboard')
@section('content')
<div class="container-fluid">

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body d-flex gap-3 align-items-center">
                <div class="stat-icon"><i class="bi bi-trophy"></i></div>
                <div>
                    <small class="text-muted d-block">Bidang Terbaik (Metode SAW)</small>
                    <h6 class="text-primary mb-0">{{ $bidangTerbaik['bidang']->nama ?? '-' }}</h6>
                    <small class="text-muted">Skor: {{ $bidangTerbaik['skor'] ?? '-' }}</small>
                </div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body d-flex gap-3 align-items-center">
                <div class="stat-icon"><i class="bi bi-graph-down-arrow"></i></div>
                <div>
                    <small class="text-muted d-block">Sub Kegiatan Belum Ada Realisasi ({{ $tahun }})</small>
                    <h4 class="text-primary mb-0">{{ $subKegiatanNol }}</h4>
                </div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body d-flex gap-3 align-items-center">
                <div class="stat-icon"><i class="bi bi-envelope"></i></div>
                <div>
                    <small class="text-muted d-block">Total Undangan</small>
                    <h4 class="text-primary mb-0">{{ $totalUndangan }}</h4>
                </div>
            </div></div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-bar-chart-steps"></i> Peringkat Kinerja Bidang (SAW) - Tahun {{ $tahun }}</span>
            <form method="GET" class="d-flex gap-2">
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for ($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped small align-middle mb-1">
                <thead class="table-light text-center">
                    <tr>
                        <th>Peringkat</th>
                        <th>Bidang</th>
                        <th>% Realisasi Kinerja <br><small class="text-muted">(bobot 35%)</small></th>
                        <th>% Realisasi Anggaran <br><small class="text-muted">(bobot 35%)</small></th>
                        <th>% Ketepatan Waktu <br><small class="text-muted">(bobot 30%)</small></th>
                        <th>Skor SAW</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ranking as $row)
                        <tr class="{{ $row['rank'] === 1 ? 'table-success' : '' }}">
                            <td class="text-center fw-bold">
                                @if ($row['rank'] === 1) <i class="bi bi-trophy-fill text-warning"></i> @endif
                                {{ $row['rank'] }}
                            </td>
                            <td>{{ $row['bidang']->nama }}</td>
                            <td class="text-center">{{ $row['c1_kinerja'] }}%</td>
                            <td class="text-center">{{ $row['c2_anggaran'] }}%</td>
                            <td class="text-center">{{ $row['c3_ketepatan'] }}%</td>
                            <td class="text-center fw-bold">{{ $row['skor'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <small class="text-muted">
                Skor = (35% x normalisasi Kinerja) + (35% x normalisasi Anggaran) + (30% x normalisasi Ketepatan Waktu).
                Tiap kriteria dinormalisasi terhadap nilai tertinggi antar bidang pada tahun berjalan.
            </small>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><i class="bi bi-envelope"></i> Undangan</div>
        <div class="card-body">
            @forelse ($undangans as $u)
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $u->judul_kegiatan }}</strong>
                        <span class="badge {{ $u->status_kegiatan === 'Terlaksana' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $u->status_kegiatan }}
                        </span>
                    </div>
                    <div>{{ $u->tanggal->translatedFormat('d F Y') }} • {{ \Carbon\Carbon::parse($u->waktu)->format('H:i') }} WIB • {{ $u->tempat }}</div>
                    <small class="text-muted">Pihak terkait: {{ $u->roles->pluck('nama')->join(', ') }}</small>

                    @unless ($isAdmin)
                        @if ($u->status_kegiatan !== 'Terlaksana')
                            <form method="POST" action="{{ route('undangan.hadiri', $u) }}" enctype="multipart/form-data" class="mt-2 d-flex gap-2 align-items-end">
                                @csrf
                                <input type="file" name="gambar" class="form-control form-control-sm" accept="application/pdf">
                                <input type="text" name="keterangan" class="form-control form-control-sm" placeholder="Keterangan delegasi (opsional)">
                                <button class="btn btn-primary btn-sm text-nowrap">Tandai Terlaksana</button>
                            </form>
                        @endif
                    @endunless
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada undangan.</p>
            @endforelse
            {{ $undangans->links() }}
        </div>
    </div>
</div>
@endsection

{{-- Menggantikan kepegawaian.php / pemberdayaan.php / perlindungan.php /
     perencanaan.php / rehabilitasi.php / pemberdayaanMasyarakat.php --}}
@extends('layouts.app')
@section('title', $bidang->nama.' - SIPEKA')
@section('page-header', $bidang->nama)
@section('content')
<div class="container-fluid">

    @if ($canInput)
        <a href="{{ route('bidang.edit', $bidang) }}" class="btn btn-primary mb-3">
            <i class="bi bi-pencil-square"></i> Input / Edit Realisasi
        </a>
    @else
        <p class="text-muted"><i class="bi bi-info-circle-fill"></i> Hanya staff {{ $bidang->nama }} yang dapat mengisi data ini.</p>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header"><i class="bi bi-funnel me-1"></i> Filter Data</div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Sub Kegiatan</label>
                    <select name="sub_kegiatan_id" class="form-select" required>
                        <option value="">-- Pilih Sub Kegiatan --</option>
                        @foreach ($subKegiatans as $sk)
                            <option value="{{ $sk->id }}" {{ request('sub_kegiatan_id') == $sk->id ? 'selected' : '' }}>
                                {{ $sk->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tahun</label>
                    <select name="tahun" class="form-select" required>
                        <option value="">-- Pilih Tahun --</option>
                        @foreach ($tahunList as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
                    <a href="{{ route('bidang.index', $bidang) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    @if (!$rencana)
        <div class="text-muted text-center py-5">
            <i class="bi bi-clipboard-data fs-1 d-block mb-2"></i>
            Pilih sub kegiatan dan tahun terlebih dahulu.
        </div>
    @else
        @php
            $tw = $rencana->triwulanData();
            $satuan = $rencana->subKegiatan->satuan;
            $bulanNama = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $bulanData = $rencana->realisasiPerBulan();
        @endphp

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <dl class="row mb-0 small">
                            <dt class="col-sm-4">Sasaran</dt><dd class="col-sm-8">{{ $rencana->subKegiatan->sasaran_strategis }}</dd>
                            <dt class="col-sm-4">Indikator</dt><dd class="col-sm-8">{{ $rencana->subKegiatan->indikator_kinerja }}</dd>
                            <dt class="col-sm-4">Program</dt><dd class="col-sm-8">{{ $rencana->subKegiatan->kegiatan->program->nama }}</dd>
                            <dt class="col-sm-4">Kegiatan</dt><dd class="col-sm-8">{{ $rencana->subKegiatan->kegiatan->nama }}</dd>
                            <dt class="col-sm-4">Sub Kegiatan</dt><dd class="col-sm-8">{{ $rencana->subKegiatan->nama }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-4 border-start">
                        <div class="mb-2">
                            <small class="text-muted d-block">Target Tahunan</small>
                            <strong>{{ number_format($rencana->target, 2, ',', '.') }} {{ $satuan }}</strong>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Capaian Kinerja Sampai Saat Ini</small>
                            <strong class="text-primary">{{ $rencana->persenKinerja() }}%</strong>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Pagu Anggaran Tahunan</small>
                            <strong>Rp {{ number_format($rencana->pagu_anggaran, 0, ',', '.') }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Capaian Anggaran Sampai Saat Ini</small>
                            <strong class="text-primary">{{ $rencana->persenAnggaran() }}%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mb-2"><i class="bi bi-bar-chart-steps"></i> Realisasi per Triwulan</h6>
        <div class="row g-3">
            @foreach ($tw as $i => $data)
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-header text-center">Triwulan {{ $i }}</div>
                        <div class="card-body small">
                            @if ($data['realisasi_target_tw'] === null && $data['realisasi_anggaran_tw'] === null)
                                <p class="text-muted mb-0 text-center py-3">Belum ada realisasi</p>
                            @else
                                <ul class="list-unstyled mb-2">
                                    <li><strong>Realisasi Kinerja:</strong> {{ number_format($data['realisasi_target_tw'] ?? 0, 2, ',', '.') }} {{ $satuan }}</li>
                                    <li><strong>% Kinerja (kumulatif):</strong> {{ $data['persentase_target'] ?? '-' }}%</li>
                                    <li><strong>Sisa Target:</strong> {{ number_format($data['sisa_target'], 2, ',', '.') }} {{ $satuan }}</li>
                                </ul>
                                <hr class="my-2">
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Realisasi Anggaran:</strong> Rp {{ number_format($data['realisasi_anggaran_tw'] ?? 0, 0, ',', '.') }}</li>
                                    <li><strong>% Anggaran (kumulatif):</strong> {{ $data['persentase_anggaran'] ?? '-' }}%</li>
                                    <li><strong>Sisa Anggaran:</strong> Rp {{ number_format($data['sisa_anggaran'], 0, ',', '.') }}</li>
                                </ul>
                            @endif
                        </div>
                        <div class="card-footer bg-white text-end border-0 pb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalTw{{ $i }}">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal detail per-bulan untuk triwulan ini --}}
                <div class="modal fade" id="modalTw{{ $i }}" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <div>
                                    <h5 class="modal-title mb-0">Detail Triwulan {{ $i }}</h5>
                                    <small>{{ $rencana->subKegiatan->nama }}</small>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    @foreach ($data['bulan_list'] as $bulan)
                                        @php $b = $bulanData[$bulan]; @endphp
                                        <div class="col-md-4">
                                            <div class="border rounded p-3 h-100 d-flex flex-column">
                                                <h6 class="text-center fw-bold text-success mb-3">{{ $bulanNama[$bulan] }}</h6>
                                                <ul class="list-unstyled small mb-0 flex-grow-1">
                                                    <li><strong>Realisasi Kinerja:</strong> {{ $b?->realisasi_fisik ?? '-' }} {{ $b?->realisasi_fisik ? $satuan : '' }}</li>
                                                    <li><strong>Realisasi Anggaran:</strong> {{ $b?->realisasi_anggaran ? 'Rp '.number_format($b->realisasi_anggaran, 0, ',', '.') : '-' }}</li>
                                                    <li>
                                                        <strong>Bukti:</strong>
                                                        @if ($b?->bukti_path)
                                                            <a href="{{ Storage::url($b->bukti_path) }}" target="_blank">Lihat</a>
                                                        @else <span class="text-muted">-</span> @endif
                                                    </li>
                                                    <li><strong>Keterangan:</strong><br><span class="text-muted">{{ $b?->keterangan ?: '-' }}</span></li>
                                                </ul>

                                                {{-- Klik langsung ke form edit dengan sub kegiatan, tahun, dan bulan
                                                     yang sudah terpilih -- tidak perlu pilih ulang dari dropdown. --}}
                                                @if ($canInput)
                                                    <a href="{{ route('bidang.edit', $bidang) }}?sub_kegiatan_id={{ $rencana->sub_kegiatan_id }}&tahun={{ $rencana->tahun }}&bulan={{ $bulan }}"
                                                       class="btn btn-sm btn-outline-primary w-100 mt-2">
                                                        <i class="bi bi-pencil-square"></i> Edit Bulan Ini
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

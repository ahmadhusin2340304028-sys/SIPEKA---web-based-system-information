{{-- Menggantikan editKepegawaian.php / editPemberdayaan.php / editPerlindungan.php /
     editPerencanaan.php / editRehabilitasi.php / EditPM.php --}}
@extends('layouts.app')
@section('title', 'Input Realisasi - '.$bidang->nama)
@section('page-header', $bidang->nama.' - Input Realisasi')
@section('content')
<div class="container-fluid">
    <a href="{{ route('bidang.index', array_merge(['bidang' => $bidang], request()->only(['sub_kegiatan_id', 'tahun']))) }}" class="btn btn-primary mb-3">
        <i class="bi bi-arrow-bar-left"></i> Kembali
    </a>

    <div class="card shadow-sm mb-3">
        <div class="card-header"><i class="bi bi-funnel me-1"></i> Pilih Data & Bulan</div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Sub Kegiatan</label>
                    <select name="sub_kegiatan_id" class="form-select" required>
                        <option value="">-- Pilih Sub Kegiatan --</option>
                        @foreach ($subKegiatans as $sk)
                            <option value="{{ $sk->id }}" {{ request('sub_kegiatan_id') == $sk->id ? 'selected' : '' }}>{{ $sk->nama }}</option>
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
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Bulan</label>
                    <select name="bulan" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        @foreach (range(1,12) as $b)
                            <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary"><i class="bi bi-caret-down"></i> Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    @if ($rencana)
        {{-- Ringkasan data master, supaya staff bisa memastikan sub-kegiatan yang diedit sudah benar
             sebelum mengisi angka realisasi -- ini sengaja ditampilkan lagi di sini (bukan cuma
             di halaman index) karena admin/staff sering membuka halaman edit langsung lewat bookmark. --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-card-checklist me-1"></i> Ringkasan Data (Pastikan Sudah Benar)</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Sasaran Strategis</strong><div>{{ $rencana->subKegiatan->sasaran_strategis }}</div></li>
                <li class="list-group-item"><strong>Indikator Kinerja</strong><div>{{ $rencana->subKegiatan->indikator_kinerja }}</div></li>
                <li class="list-group-item"><strong>Program</strong><div>{{ $rencana->subKegiatan->kegiatan->program->nama }}</div></li>
                <li class="list-group-item"><strong>Kegiatan</strong><div>{{ $rencana->subKegiatan->kegiatan->nama }}</div></li>
                <li class="list-group-item"><strong>Sub Kegiatan</strong><div>{{ $rencana->subKegiatan->nama }}</div></li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Target Tahunan</strong>
                            <div>{{ number_format($rencana->target, 2, ',', '.') }} {{ $rencana->subKegiatan->satuan }}</div>
                        </div>
                        <div class="col-md-6">
                            <strong>Sisa Target</strong>
                            <div class="text-primary fw-semibold">
                                {{ number_format($rencana->target - $rencana->totalRealisasiFisik(), 2, ',', '.') }} {{ $rencana->subKegiatan->satuan }}
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Pagu Anggaran</strong>
                            <div>Rp {{ number_format($rencana->pagu_anggaran, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <strong>Sisa Pagu Anggaran</strong>
                            <div class="text-primary fw-semibold">
                                Rp {{ number_format($rencana->pagu_anggaran - $rencana->totalRealisasiAnggaran(), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        @if (request('bulan'))
            <div class="card">
                <div class="card-header">
                    Input Realisasi Bulan {{ \Carbon\Carbon::create()->month((int) request('bulan'))->translatedFormat('F') }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bidang.update', $bidang) }}" enctype="multipart/form-data" class="row g-3 align-items-end">
                        @csrf
                        <input type="hidden" name="rencana_kinerja_id" value="{{ $rencana->id }}">
                        <input type="hidden" name="bulan" value="{{ request('bulan') }}">

                        <div class="col-md-3">
                            <label class="form-label">Realisasi Target</label>
                            <input type="number" step="0.01" name="realisasi_fisik" class="form-control @error('realisasi_fisik') is-invalid @enderror"
                                   value="{{ old('realisasi_fisik', $realisasiBulan?->realisasi_fisik) }}" required>
                            @error('realisasi_fisik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Realisasi Anggaran</label>
                            <input type="number" name="realisasi_anggaran" class="form-control @error('realisasi_anggaran') is-invalid @enderror"
                                   value="{{ old('realisasi_anggaran', $realisasiBulan?->realisasi_anggaran) }}" required>
                            @error('realisasi_anggaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bukti Dukung (PDF)</label>
                            <input type="file" name="bukti" class="form-control @error('bukti') is-invalid @enderror" accept="application/pdf">
                            @error('bukti') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if ($realisasiBulan?->bukti_path)
                                <small class="text-muted d-block">
                                    Bukti saat ini: <a href="{{ Storage::url($realisasiBulan->bukti_path) }}" target="_blank">lihat</a>
                                </small>
                            @endif
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3"
                                      placeholder="Kosongkan jika tidak ada kendala tertentu">{{ old('keterangan', $realisasiBulan?->keterangan) }}</textarea>
                            @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="text-muted text-center py-4">Pilih bulan yang mau diisi terlebih dahulu.</div>
        @endif
    @else
        <div class="text-muted text-center py-5">
            <i class="bi bi-clipboard-data fs-1 d-block mb-2"></i>
            Pilih sub kegiatan dan tahun terlebih dahulu.
        </div>
    @endif
</div>
@endsection

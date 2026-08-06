@extends('layouts.app')
@section('title', 'Rekapitulasi - SIPEKA')
@section('page-header', 'Rekapitulasi')
@section('content')
<div class="container-fluid mt-3">
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-select">
                @for ($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Kelompok</label>
            <select name="kelompok" class="form-select">
                <option value="">Semua</option>
                <option value="Sekretariat" {{ $kelompok=='Sekretariat'?'selected':'' }}>Sekretariat</option>
                <option value="Bidang Sosial" {{ $kelompok=='Bidang Sosial'?'selected':'' }}>Bidang Sosial</option>
                <option value="Bidang PM" {{ $kelompok=='Bidang PM'?'selected':'' }}>Bidang PM</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button class="btn btn-primary">Terapkan</button>
            <a class="btn btn-outline-success" href="{{ route('rekap.export', request()->query()) }}">
                <i class="bi bi-file-earmark-excel"></i> Export
            </a>
        </div>
    </form>

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
            <small>Total Kegiatan</small><h4 class="text-primary">{{ $totalKegiatan }}</h4>
        </div></div></div>
        <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
            <small>Total Pagu Anggaran</small><h4 class="text-primary">Rp {{ number_format($totalPagu,0,',','.') }}</h4>
        </div></div></div>
        <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
            <small>Total Realisasi Anggaran</small><h4 class="text-primary">Rp {{ number_format($totalRealisasiAnggaran,0,',','.') }}</h4>
        </div></div></div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped small">
            <thead class="table-primary text-center">
                <tr>
                    <th>Bidang</th><th>Kegiatan</th><th>Sub Kegiatan</th>
                    <th>Target</th><th>Realisasi</th><th>% Kinerja</th>
                    <th>Pagu</th><th>Realisasi Anggaran</th><th>% Anggaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rencana as $rk)
                    @php
                        $target = (float) $rk->target;
                        $fisik = (float) $rk->total_realisasi_fisik;
                        $pagu = (float) $rk->pagu_anggaran;
                        $anggaran = (float) $rk->total_realisasi_anggaran;
                    @endphp
                    <tr>
                        <td>{{ $rk->subKegiatan->kegiatan->program->bidang->nama }}</td>
                        <td>{{ $rk->subKegiatan->kegiatan->nama }}</td>
                        <td>{{ $rk->subKegiatan->nama }}</td>
                        <td class="text-end">{{ number_format($target,2,',','.') }}</td>
                        <td class="text-end">{{ number_format($fisik,2,',','.') }}</td>
                        <td class="text-center">{{ $target>0 ? round($fisik/$target*100,2) : 0 }}%</td>
                        <td class="text-end">{{ number_format($pagu,0,',','.') }}</td>
                        <td class="text-end">{{ number_format($anggaran,0,',','.') }}</td>
                        <td class="text-center">{{ $pagu>0 ? round($anggaran/$pagu*100,2) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Kelola Kegiatan - SIPEKA')
@section('page-header', 'Kelola Data Kegiatan')
@section('content')
<div class="container-fluid mt-3">
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahKegiatan">
        <i class="bi bi-plus-lg"></i> Tambah Data Kegiatan
    </button>

    <div class="table-responsive">
        <table class="table table-bordered table-striped small">
            <thead>
                <tr>
                    <th>Bidang</th><th>Program</th><th>Kegiatan</th><th>Sub Kegiatan</th>
                    <th>Satuan</th><th>Target</th><th>Pagu</th><th>Tahun</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rencana as $rk)
                    <tr>
                        <td>{{ $rk->subKegiatan->kegiatan->program->bidang->nama }}</td>
                        <td>{{ $rk->subKegiatan->kegiatan->program->nama }}</td>
                        <td>{{ $rk->subKegiatan->kegiatan->nama }}</td>
                        <td>{{ $rk->subKegiatan->nama }}</td>
                        <td>{{ $rk->subKegiatan->satuan }}</td>
                        <td>{{ number_format($rk->target,2,',','.') }}</td>
                        <td>{{ number_format($rk->pagu_anggaran,0,',','.') }}</td>
                        <td>{{ $rk->tahun }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit{{ $rk->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('rencana-kinerja.destroy', $rk) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="edit{{ $rk->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('rencana-kinerja.update', $rk) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="_modal_target" value="edit{{ $rk->id }}">
                                    @include('kegiatan._form', ['bidangs' => $bidangs, 'rk' => $rk])
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $rencana->links() }}
</div>

<div class="modal fade" id="tambahKegiatan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('rencana-kinerja.store') }}">
                @csrf
                <input type="hidden" name="_modal_target" value="tambahKegiatan">
                @include('kegiatan._form', ['bidangs' => $bidangs, 'rk' => null])
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

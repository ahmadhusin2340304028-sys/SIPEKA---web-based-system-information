@extends('layouts.app')
@section('title', 'Kelola Undangan - SIPEKA')
@section('page-header', 'Kelola Surat Undangan')
@section('content')
<div class="container-fluid mt-3">
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahUndangan">
        <i class="bi bi-plus-lg"></i> Tambah Undangan
    </button>

    <div class="table-responsive">
        <table class="table table-bordered table-striped small">
            <thead class="table-light text-center">
                <tr>
                    <th>Kegiatan</th><th>Tanggal</th><th>Waktu</th><th>Tempat</th>
                    <th>Pihak Mengundang</th><th>Pihak Terkait</th><th>Menghadiri</th>
                    <th>Status</th><th>Kirim Notif</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($undangans as $u)
                    <tr>
                        <td>{{ $u->judul_kegiatan }}</td>
                        <td class="text-nowrap">{{ $u->tanggal->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($u->waktu)->format('H:i') }}</td>
                        <td>{{ $u->tempat }}</td>
                        <td>{{ $u->pihak_mengundang }}</td>
                        <td>{{ $u->roles->pluck('nama')->join(', ') }}</td>
                        <td>{{ $u->menghadiri?->name ?? '-' }}</td>
                        <td>{{ $u->status_kegiatan }}</td>
                        <td class="text-center">{{ $u->notify_all ? 'Semua User' : 'Pihak Terkait Saja' }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit{{ $u->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('undangan.destroy', $u) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus data?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    {{-- Modal edit --}}
                    <div class="modal fade" id="edit{{ $u->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('undangan.update', $u) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="_modal_target" value="edit{{ $u->id }}">
                                    @include('undangan._form', ['roles' => $roles, 'undangan' => $u])
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

    {{ $undangans->links() }}
</div>

{{-- Modal tambah --}}
<div class="modal fade" id="tambahUndangan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('undangan.store') }}">
                @csrf
                <input type="hidden" name="_modal_target" value="tambahUndangan">
                @include('undangan._form', ['roles' => $roles, 'undangan' => null])
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

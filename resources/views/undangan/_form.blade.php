<div class="modal-header">
    <h5 class="modal-title">{{ $undangan ? 'Edit' : 'Tambah' }} Undangan</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            Data belum tersimpan, mohon periksa kembali isian yang ditandai merah di bawah ini.
        </div>
    @endif

    <div class="mb-3">
        <label class="form-label">Kegiatan</label>
        <textarea name="judul_kegiatan" class="form-control @error('judul_kegiatan') is-invalid @enderror" rows="2" required>{{ old('judul_kegiatan', $undangan?->judul_kegiatan) }}</textarea>
        @error('judul_kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $undangan?->tanggal?->format('Y-m-d')) }}" required>
            @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Waktu</label>
            <input type="time" name="waktu" class="form-control @error('waktu') is-invalid @enderror" value="{{ old('waktu', $undangan?->waktu) }}" required>
            @error('waktu') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Tempat</label>
            <input type="text" name="tempat" class="form-control @error('tempat') is-invalid @enderror" value="{{ old('tempat', $undangan?->tempat) }}" required>
            @error('tempat') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Pihak Yang Mengundang</label>
        <input type="text" name="pihak_mengundang" class="form-control @error('pihak_mengundang') is-invalid @enderror" value="{{ old('pihak_mengundang', $undangan?->pihak_mengundang) }}" required>
        @error('pihak_mengundang') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    @if ($undangan)
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status_kegiatan" class="form-select">
                <option value="Belum Terlaksana" {{ old('status_kegiatan', $undangan->status_kegiatan) === 'Belum Terlaksana' ? 'selected' : '' }}>Belum Terlaksana</option>
                <option value="Terlaksana" {{ old('status_kegiatan', $undangan->status_kegiatan) === 'Terlaksana' ? 'selected' : '' }}>Terlaksana</option>
            </select>
        </div>
    @endif

    <div class="mb-3">
        <label class="form-label">Pihak Yang Terkait / Diundang</label>
        <div class="row @error('bidang_terkait') is-invalid @enderror">
            @php $terpilih = old('bidang_terkait', $undangan?->roles->pluck('id')->toArray() ?? []); @endphp
            @foreach ($roles as $role)
                <div class="col-md-6 form-check">
                    <input class="form-check-input" type="checkbox" name="bidang_terkait[]"
                           value="{{ $role->id }}" id="role{{ ($undangan?->id ?? 'new').$role->id }}"
                           {{ in_array($role->id, $terpilih) ? 'checked' : '' }}>
                    <label class="form-check-label" for="role{{ ($undangan?->id ?? 'new').$role->id }}">{{ $role->nama }}</label>
                </div>
            @endforeach
        </div>
        @error('bidang_terkait')
            <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    @unless ($undangan)
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="notify_all" value="1" id="notifyAll" {{ old('notify_all') ? 'checked' : '' }}>
            <label class="form-check-label" for="notifyAll">
                Kirim notifikasi push ke <strong>semua user</strong> (bukan hanya pihak terkait di atas)
            </label>
        </div>
    @endunless
</div>

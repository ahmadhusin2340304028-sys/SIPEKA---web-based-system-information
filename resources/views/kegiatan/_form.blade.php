@php $sk = $rk?->subKegiatan; $kg = $sk?->kegiatan; $pr = $kg?->program; @endphp
<div class="modal-header">
    <h5 class="modal-title">{{ $rk ? 'Edit' : 'Tambah' }} Data Kegiatan</h5>
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
        <label class="form-label">Sasaran Strategis</label>
        <textarea name="sasaran" class="form-control @error('sasaran') is-invalid @enderror" rows="2" required>{{ old('sasaran', $sk?->sasaran_strategis) }}</textarea>
        @error('sasaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Indikator Kinerja</label>
        <textarea name="indikator" class="form-control @error('indikator') is-invalid @enderror" rows="2" required>{{ old('indikator', $sk?->indikator_kinerja) }}</textarea>
        @error('indikator') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Program</label>
        <textarea name="program" class="form-control @error('program') is-invalid @enderror" rows="2" required>{{ old('program', $pr?->nama) }}</textarea>
        @error('program') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Kegiatan</label>
        <textarea name="kegiatan" class="form-control @error('kegiatan') is-invalid @enderror" rows="2" required>{{ old('kegiatan', $kg?->nama) }}</textarea>
        @error('kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Sub Kegiatan</label>
        <textarea name="subkegiatan" class="form-control @error('subkegiatan') is-invalid @enderror" rows="2" required>{{ old('subkegiatan', $sk?->nama) }}</textarea>
        @error('subkegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Satuan</label>
            <input type="text" name="satuan" class="form-control @error('satuan') is-invalid @enderror" value="{{ old('satuan', $sk?->satuan) }}" required>
            @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Target</label>
            <input type="number" step="0.01" name="target" class="form-control @error('target') is-invalid @enderror" value="{{ old('target', $rk?->target) }}" required>
            @error('target') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror" value="{{ old('tahun', $rk?->tahun ?? now()->year) }}" required>
            @error('tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Pagu Anggaran</label>
            <input type="number" name="pagu_anggaran" class="form-control @error('pagu_anggaran') is-invalid @enderror" value="{{ old('pagu_anggaran', $rk?->pagu_anggaran) }}" required>
            @error('pagu_anggaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Urusan / Bidang</label>
            <select name="bidang_id" class="form-select @error('bidang_id') is-invalid @enderror" required>
                <option value="">-- Pilih Urusan --</option>
                @foreach ($bidangs as $b)
                    <option value="{{ $b->id }}" {{ old('bidang_id', $pr?->bidang_id) == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                @endforeach
            </select>
            @error('bidang_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

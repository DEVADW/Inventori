<div class="row g-3">
    <div class="col-md-6 col-lg-4">
        <label for="no_aset" class="form-label">Nomor Aset</label>
        <input id="no_aset" name="no_aset" type="text"
            class="form-control @error('no_aset') is-invalid @enderror"
            value="{{ old('no_aset', $inventoritv->no_aset ?? '') }}" placeholder="Contoh: AST-0001">
        @error('no_aset')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 col-lg-4">
        <label for="merek" class="form-label">Merek</label>
        <input id="merek" name="merek" type="text"
            class="form-control @error('merek') is-invalid @enderror"
            value="{{ old('merek', $inventoritv->merek ?? '') }}" placeholder="Contoh: Samsung">
        @error('merek')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 col-lg-4">
        <label for="tipe" class="form-label">Tipe</label>
        <input id="tipe" name="tipe" type="text"
            class="form-control @error('tipe') is-invalid @enderror"
            value="{{ old('tipe', $inventoritv->tipe ?? '') }}" placeholder="Contoh: LED 32 Inch">
        @error('tipe')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 col-lg-4">
        <label for="serial_number" class="form-label">Serial Number</label>
        <input id="serial_number" name="serial_number" type="text"
            class="form-control @error('serial_number') is-invalid @enderror"
            value="{{ old('serial_number', $inventoritv->serial_number ?? '') }}" placeholder="Serial number">
        @error('serial_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 col-lg-4">
        <label for="department" class="form-label">Department</label>
        <input id="department" name="department" type="text"
            class="form-control @error('department') is-invalid @enderror"
            value="{{ old('department', $inventoritv->department ?? '') }}" placeholder="Contoh: ICT MD">
        @error('department')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 col-lg-4">
        <label for="lokasi" class="form-label">Lokasi</label>
        <input id="lokasi" name="lokasi" type="text"
            class="form-control @error('lokasi') is-invalid @enderror"
            value="{{ old('lokasi', $inventoritv->lokasi ?? '') }}" placeholder="Contoh: Ruang Meeting">
        @error('lokasi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger mt-3">
        {{ $errors->first('error') }}
    </div>
@endif

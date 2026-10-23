@php
    $statusOptions = ['Terpasang', 'Standby', 'Nonaktif'];
@endphp

<div class="row g-3">
    <div class="col-md-6 col-lg-4">
        <label for="nomor_msisdn" class="form-label">Nomor MSISDN</label>
        <input id="nomor_msisdn" name="nomor_msisdn" type="text"
            class="form-control @error('nomor_msisdn') is-invalid @enderror"
            value="{{ old('nomor_msisdn', $simcard->nomor_msisdn ?? '') }}" placeholder="Contoh: 081234567890">
        @error('nomor_msisdn')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 col-lg-4">
        <label for="jumlah_paket_kuota" class="form-label">Jumlah Paket Kuota</label>
        <input id="jumlah_paket_kuota" name="jumlah_paket_kuota" type="text"
            class="form-control @error('jumlah_paket_kuota') is-invalid @enderror"
            value="{{ old('jumlah_paket_kuota', $simcard->jumlah_paket_kuota ?? '') }}" placeholder="Contoh: 10 GB">
        @error('jumlah_paket_kuota')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 col-lg-4">
        <label for="status_kartu" class="form-label">Status Kartu</label>
        <select name="status_kartu" id="status_kartu" class="form-select @error('status_kartu') is-invalid @enderror">
            <option value="">------- Pilih Status ------</option>
            @foreach ($statusOptions as $opt)
                <option value="{{ $opt }}" {{ old('status_kartu', $simcard->status_kartu ?? '') == $opt ? 'selected' : '' }}>
                    {{ $opt }}
                </option>
            @endforeach
        </select>
        @error('status_kartu')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="remark" class="form-label">Remark</label>
        <textarea id="remark" name="remark" rows="3" class="form-control @error('remark') is-invalid @enderror"
            placeholder="Catatan tambahan">{{ old('remark', $simcard->remark ?? '') }}</textarea>
        @error('remark')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger mt-3">
        {{ $errors->first('error') }}
    </div>
@endif

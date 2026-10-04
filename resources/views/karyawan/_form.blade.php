@php
   $karyawans = [
       'nrp' => ['NRP', 'Conth: 2503134'], 
       'nama' => ['Nama', 'Nama lengkap'], 
   ];

   // biar enak melihara nya kalau ada yang baru tinggal tambah
   $jabatan = [
       'SH' => 'Section Head', 
       'GL' => 'Group Leader', 
       'ICT' => 'ICT', 
       'hardware_enggineer' => 'Hardware Engineer',
       'ICT_technician' => 'ICT Technician',
       'helper' => 'Helper ICT'
   ];

   
@endphp

<div class="row g-3">
   @foreach ($karyawans as $field => [$label, $placeholder])
      <div class="col-md-6 col-lg-4">
         <label for="{{ $field }}" class="form-label">{{ $label }}</label>
         <input
            id="{{ $field }}" 
            name="{{ $field }}" 
            type="text" 
            class="form-control @error($field) is-invalid @enderror"
            value="{{ old($field, $karyawan->$field ?? '') }}" 
            placeholder="{{ $placeholder }}">
         @error($field)
            <div class="invalid-feedback">{{ $message }}</div>
         @enderror
      </div>
   @endforeach

   <div class="col-md-6 col-lg-4">
         <label for="departemen" class="form-label">Departemen</label>
         <input
            id="departmen" 
            name="departemen" 
            type="text" 
            class="form-control @error('departemen') is-invalid @enderror"
            value="{{ old('departemen', $karyawan->departemen ?? 'ICT MD') }}" 
            placeholder="Contoh : ICTMD, FA, SHE">
         @error('departemen')
            <div class="invalid-feedback">{{ $message }}</div>
         @enderror
      </div>

   <div class="col-md-6 col-lg-4">
      <label for="jabatan" class="form-label">Jabatan</label>
      <select name="jabatan" id="jabatan" class="form-select @error('jabatan') is-invalid @enderror">
         <option value="">------- Pilih Jabatan ------</option>
         @foreach ($jabatan as $key => $opt)
            <option value="{{ $key }}" {{ old('jabatan', $karyawan->jabatan ?? '') == $key ? 'selected' : '' }}>
               {{ $opt }}
            </option>
         @endforeach
      </select>
      @error('jabatan')
         <div class="invalid-feedback">{{ $message }}</div>
      @enderror
   </div>
</div>

@if($errors->has('error'))
   <div class="alert alert-danger mt-3">
      {{ $errors->first('error') }}
   </div>
@endif
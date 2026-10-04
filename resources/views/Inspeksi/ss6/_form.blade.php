@php
    /*
   |--------------------------------------------------------------------------
   | Data Unit
   |--------------------------------------------------------------------------
   */
    $dataUnit = [
        'no_asset' => 'No. Asset',
        'no_lambung' => 'No. Lambung',
        'tanggal_inspeksi' => 'Tanggal Inspeksi',
        'serial_number' => 'Serial Number',
    ];

    /*
   |--------------------------------------------------------------------------
   | Field Hasil Inspeksi
   |--------------------------------------------------------------------------
   */
    $kondisiFields = [
        'kondisi_monitor' => 'Kondisi Monitor',
        'kondisi_bracket' => 'Kondisi Bracket',
        'kondisi_car_charger' => 'Kondisi Car Charger',
        'kondisi_kabel_power' => 'Kondisi Kabel Power',
        'kondisi_app_lock' => 'Kondisi App Lock',
        'software_ppa_teams' => 'Software PPA / Teams',
        'kondisi_baterai' => 'Kondisi Baterai',
    ];
@endphp


{{-- ========================================================= --}}
{{-- DATA UNIT --}}
{{-- ========================================================= --}}

<div class="mb-4">
    <h6 class="fw-bold mb-3">Data Unit</h6>

    <div class="row">

        @foreach ($dataUnit as $name => $label)
            <div class="col-md-6 mb-3">

                <label for="{{ $name }}" class="form-label">
                    {{ $label }}
                    <span class="text-danger">*</span>
                </label>

                @if ($name === 'tanggal_inspeksi')
                    <input type="date" name="{{ $name }}" id="{{ $name }}"
                        class="form-control @error($name) is-invalid @enderror"
                        value="{{ old($name, $inspeksi->$name ?? '') }}" required>
                @else
                    <input type="text" name="{{ $name }}" id="{{ $name }}"
                        class="form-control @error($name) is-invalid @enderror"
                        value="{{ old($name, $inspeksi->$name ?? '') }}" placeholder="Masukkan {{ strtolower($label) }}"
                        required>
                @endif

                @error($name)
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>
        @endforeach

    </div>
</div>


<hr>


{{-- ========================================================= --}}
{{-- HASIL INSPEKSI --}}
{{-- ========================================================= --}}

<div class="mb-4">

    <h6 class="fw-bold mb-3">
        Hasil Inspeksi
    </h6>

    <div class="row">

        @foreach ($kondisiFields as $name => $label)
            @php
                $dataKondisi = $inspeksi->{$name} ?? [];

                $ketersediaanValue = old($name . '_ketersediaan', $dataKondisi['ketersediaan'] ?? '');

                $kondisiValue = old($name . '_kondisi', $dataKondisi['kondisi'] ?? '');
            @endphp

            <div class="col-md-6 mb-4">

                <label class="form-label fw-semibold">
                    {{ $label }}
                    <span class="text-danger">*</span>
                </label>


                {{-- KETERSEDIAAN --}}
                <div class="mb-2">

                    <small class="text-muted d-block mb-1">
                        Ketersediaan
                    </small>

                    {{-- ADA --}}
                    <div class="form-check form-check-inline">

                        <input type="radio" class="form-check-input" name="{{ $name }}_ketersediaan"
                            id="{{ $name }}_ada" value="Ada"
                            {{ $ketersediaanValue === 'Ada' ? 'checked' : '' }} required>

                        <label class="form-check-label" for="{{ $name }}_ada">
                            Ada
                        </label>

                    </div>


                    {{-- TIDAK ADA --}}
                    <div class="form-check form-check-inline">

                        <input type="radio" class="form-check-input" name="{{ $name }}_ketersediaan"
                            id="{{ $name }}_tidak_ada" value="Tidak ada"
                            {{ $ketersediaanValue === 'Tidak ada' ? 'checked' : '' }}>

                        <label class="form-check-label" for="{{ $name }}_tidak_ada">
                            Tidak ada
                        </label>

                    </div>

                </div>


                {{-- KONDISI --}}
                <div>

                    <small class="text-muted d-block mb-1">
                        Kondisi
                    </small>

                    {{-- BAIK --}}
                    <div class="form-check form-check-inline">

                        <input type="radio" class="form-check-input" name="{{ $name }}_kondisi"
                            id="{{ $name }}_baik" value="Baik"
                            {{ $kondisiValue === 'Baik' ? 'checked' : '' }} required>

                        <label class="form-check-label" for="{{ $name }}_baik">
                            Baik
                        </label>

                    </div>


                    {{-- RUSAK --}}
                    <div class="form-check form-check-inline">

                        <input type="radio" class="form-check-input" name="{{ $name }}_kondisi"
                            id="{{ $name }}_rusak" value="Rusak"
                            {{ $kondisiValue === 'Rusak' ? 'checked' : '' }}>

                        <label class="form-check-label" for="{{ $name }}_rusak">
                            Rusak
                        </label>

                    </div>

                </div>


                {{-- ERROR --}}
                @error($name . '_ketersediaan')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror

                @error($name . '_kondisi')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror

            </div>
        @endforeach

        {{-- ================================================= --}}
        {{-- OUTPUT POWER CHARGER --}}
        {{-- ================================================= --}}

        @php
            $outputPowerValue = old('output_powercharge', $inspeksi->output_powercharge ?? '');
        @endphp

        <div class="col-md-6 mb-4">

            <label for="output_powercharge" class="form-label fw-semibold">
                Output Power Charger
                <span class="text-danger">*</span>
            </label>

            <div class="input-group">

                <input type="number" name="output_powercharge" id="output_power_charger"
                    class="form-control @error('output_powercharge') is-invalid @enderror"
                    value="{{ old('output_powercharge', $inspeksi->output_power_charger ?? '') }}"
                    placeholder="Masukkan tegangan" min="0" step="0.1" required>

                <span class="input-group-text">
                    Volt
                </span>

            </div>

            @error('output_powercharge')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>


<hr>


{{-- ========================================================= --}}
{{-- KETERANGAN --}}
{{-- ========================================================= --}}

<div class="mb-4">

    <h6 class="fw-bold mb-3">
        Keterangan
    </h6>

    <label for="keterangan" class="form-label">
        Keterangan
    </label>

    <textarea name="keterangan" id="keterangan" rows="4"
        class="form-control @error('keterangan') is-invalid @enderror" placeholder="Masukkan keterangan jika ada">{{ old('keterangan', $inspeksi->keterangan ?? '') }}</textarea>

    @error('keterangan')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

</div>

@include('Inspeksi.partials.photo-upload')



<hr>


{{-- ========================================================= --}}
{{-- ERROR VALIDATION --}}
{{-- ========================================================= --}}

@if ($errors->any())

    <div class="alert alert-danger">

        <strong>
            Terdapat kesalahan pada input:
        </strong>

        <ul class="mb-0 mt-2">

            @foreach ($errors->all() as $error)
                <li>
                    {{ $error }}
                </li>
            @endforeach

        </ul>

    </div>

@endif

<div class="row g-3">
    <div class="col-md-6 col-lg-4">
        <label for="code_unit" class="form-label">Code Number Unit</label>
        <input id="code_unit" name="code_unit" type="text"
            class="form-control @error('code_unit') is-invalid @enderror"
            value="{{ old('code_unit', $unitoperasi->code_unit ?? '') }}" placeholder="Contoh: C77023">
        @error('code_unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 col-lg-4">
        <label for="type_unit" class="form-label">Type Unit</label>
        <input id="type_unit" name="type_unit" type="text"
            class="form-control @error('type_unit') is-invalid @enderror"
            value="{{ old('type_unit', $unitoperasi->type_unit ?? '') }}" placeholder="Contoh: CAT777">
        @error('type_unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @php
        $selectedSimCardIds = old('sim_card_ids', isset($unitoperasi) ? $unitoperasi->simCards->pluck('id')->all() : []);
    @endphp
    <div class="col-md-6 col-lg-4">
        <x-sim-card-select :options="$availableSimCards" :selected="$selectedSimCardIds" />
    </div>

    <div class="col-md-6 col-lg-4">
        <label class="form-label d-block">Kelengkapan Unit</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="ofa" id="ofa" value="1"
                    {{ old('ofa', $unitoperasi->ofa ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="ofa">OFA</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="icc" id="icc" value="1"
                    {{ old('icc', $unitoperasi->icc ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="icc">ICC</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="ss6" id="ss6" value="1"
                    {{ old('ss6', $unitoperasi->ss6 ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="ss6">TAB SS6</label>
            </div>
        </div>
    </div>

    <div id="ofaFields" class="col-md-6 col-lg-4" hidden>
        <label for="serial_number_module" class="form-label">Serial Number Module</label>
        <input id="serial_number_module" name="serial_number_module" type="text"
            class="form-control @error('serial_number_module') is-invalid @enderror"
            value="{{ old('serial_number_module', $unitoperasi->serial_number_module ?? '') }}"
            placeholder="Serial number module OFA">
        @error('serial_number_module')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div id="ss6Fields" class="col-md-6 col-lg-4" hidden>
        <label for="serial_number_tab" class="form-label">Serial Number TAB</label>
        <input id="serial_number_tab" name="serial_number_tab" type="text"
            class="form-control @error('serial_number_tab') is-invalid @enderror"
            value="{{ old('serial_number_tab', $unitoperasi->serial_number_tab ?? '') }}"
            placeholder="Serial number TAB SS6">
        @error('serial_number_tab')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div id="ss6Fields2" class="col-md-6 col-lg-4" hidden>
        <label for="no_aset_tab" class="form-label">No Aset TAB</label>
        <input id="no_aset_tab" name="no_aset_tab" type="text"
            class="form-control @error('no_aset_tab') is-invalid @enderror"
            value="{{ old('no_aset_tab', $unitoperasi->no_aset_tab ?? '') }}" placeholder="Nomor aset TAB SS6">
        @error('no_aset_tab')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="remark" class="form-label">Remark</label>
        <textarea id="remark" name="remark" rows="3" class="form-control @error('remark') is-invalid @enderror"
            placeholder="Catatan tambahan">{{ old('remark', $unitoperasi->remark ?? '') }}</textarea>
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

<script>
    (function () {
        const ofaCheckbox = document.getElementById('ofa');
        const ss6Checkbox = document.getElementById('ss6');
        const ofaFields = document.getElementById('ofaFields');
        const ss6Fields = document.getElementById('ss6Fields');
        const ss6Fields2 = document.getElementById('ss6Fields2');

        const toggle = (checkbox, ...fields) => {
            const show = checkbox.checked;
            fields.forEach((field) => {
                field.hidden = !show;
            });
        };

        toggle(ofaCheckbox, ofaFields);
        toggle(ss6Checkbox, ss6Fields, ss6Fields2);

        ofaCheckbox.addEventListener('change', () => toggle(ofaCheckbox, ofaFields));
        ss6Checkbox.addEventListener('change', () => toggle(ss6Checkbox, ss6Fields, ss6Fields2));
    })();
</script>

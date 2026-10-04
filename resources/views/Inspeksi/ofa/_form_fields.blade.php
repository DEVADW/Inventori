@php
    $storedItems = old('item_pemeriksaan', $ofa->item_pemeriksaan ?? []);

    $itemsByKey = collect($storedItems)->keyBy(fn($item) => ($item['section'] ?? '') . '|' . ($item['nama'] ?? ''));

    if (isset($ofa)) {
        // EDIT
        $tim = old('tim_pelaksana', $ofa->tim_pelaksana ?? []);
    } else {
        // CREATE
        $tim = old('tim_pelaksana', [
            [
                'nama' => $inspector->nama,
                'nrp' => $inspector->nrp,
                'jabatan' => 'Hardware Engineer',
                'departemen' => 'ICT',
                'perusahaan' => 'PT Star Perkasa Technology',
            ],
        ]);
    }

    $itemIndex = 0;
@endphp

@if ($errors->any())
    <div class="col-12">
        <div class="alert alert-danger">
            <strong>Data belum lengkap!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row g-3">
    <div class="col-12">
        <h6 class="text-primary fw-bold border-bottom pb-2">Informasi Dokumen</h6>
    </div>
    @foreach (['project_name' => 'Project Name', 'version' => 'Version', 'divisi_department' => 'Divisi / Departemen'] as $field => $label)
        <div class="col-md-4"><label for="{{ $field }}" class="form-label">{{ $label }}</label><input
                id="{{ $field }}" name="{{ $field }}" class="form-control"
                value="{{ old($field, $ofa->$field ?? ($field === 'project_name' ? 'OFA' : '')) }}">
        </div>
    @endforeach
    <div class="col-12 mt-3">
        <h6 class="text-primary fw-bold border-bottom pb-2">Identitas Unit</h6>
    </div>

    


    <div class="col-md-4">
        <label for="code_number_unit" class="form-label">Code Number Unit<span class="text-danger">*</span></label>
        <div class="position-relative">
            <input id="code_number_unit" name="code_number_unit"
                class="form-control @error('code_number_unit') is-invalid @enderror"
                value="{{ old('code_number_unit', $ofa->code_number_unit ?? '') }}" required autocomplete="off"
                data-unit-select2>
            <div id="unitSearchDropdown" class="dropdown-menu w-100 shadow-sm border mt-1"
                style="display:none; max-height:260px; overflow:auto; z-index:1060;">
                <div class="list-group list-group-flush" id="unitSearchResults"></div>
            </div>
        </div>
        @error('code_number_unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    <div class="col-md-4">
        <label for="type_unit" class="form-label">Type Unit<span class="text-danger">*</span></label>
        <input id="type_unit" name="type_unit" class="form-control @error('type_unit') is-invalid @enderror"
            value="{{ old('type_unit', $ofa->type_unit ?? '') }}" required readonly>
        @error('type_unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="serial_number_modul" class="form-label">Serial Number Modul</label>
        <input id="serial_number_modul" name="serial_number_modul"
            class="form-control @error('serial_number_modul') is-invalid @enderror"
            value="{{ old('serial_number_modul', $ofa->serial_number_modul ?? '') }}">
        @error('serial_number_modul')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="jobsite" class="form-label">Jobsite<span class="text-danger">*</span></label>
        <input id="jobsite" name="jobsite" class="form-control @error('jobsite') is-invalid @enderror"
            value="{{ old('jobsite', $ofa->jobsite ?? '') }}" required>
        @error('jobsite')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="location" class="form-label">Location<span class="text-danger">*</span></label>
        <input id="location" name="location" class="form-control @error('location') is-invalid @enderror"
            value="{{ old('location', $ofa->location ?? '') }}" required>
        @error('location')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label class="form-label">Date</label><input type="date" name="tanggal_inspeksi"
            class="form-control"
            value="{{ isset($ofa) ? $ofa->created_at?->format('d-m-Y') : now()->format('d-m-Y') }}"></div>
    <div class="col-12 mt-4">
        <h6 class="text-primary fw-bold border-bottom pb-2">Item Pemeriksaan</h6>
    </div>
    <div class="col-12 table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>Item Pemeriksaan</th>
                    <th class="text-center">Baik</th>
                    <th class="text-center">Rusak</th>
                    <th class="text-center">N/A</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($checklistSections as $section => $sectionItems)
                    <tr class="table-info">
                        <td colspan="6" class="fw-bold">{{ $section }}</td>
                    </tr>
                    @foreach ($sectionItems as $number => $name)
                        @php $item = $itemsByKey->get($section . '|' . $name, ['status' => '', 'keterangan' => '']); @endphp
                        <tr>
                            <td class="text-center">{{ $number + 1 }}</td>
                            <td>{{ $name }}<input type="hidden"
                                    name="item_pemeriksaan[{{ $itemIndex }}][nama]"
                                    value="{{ $name }}"><input type="hidden"
                                    name="item_pemeriksaan[{{ $itemIndex }}][section]" value="{{ $section }}">
                            </td>
                            @foreach (['baik' => 'Baik', 'rusak' => 'Rusak', 'na' => 'N/A'] as $status => $label)
                                <td class="text-center"><input type="radio" class="form-check-input"
                                        name="item_pemeriksaan[{{ $itemIndex }}][status]"
                                        value="{{ $status }}" @checked(($item['status'] ?? '') === $status) required
                                        aria-label="{{ $label }}"></td>
                            @endforeach
                            <td><input name="item_pemeriksaan[{{ $itemIndex }}][keterangan]"
                                    class="form-control form-control-sm" value="{{ $item['keterangan'] ?? '' }}"
                                    placeholder="Keterangan / tindakan"></td>
                        </tr>
                        @php $itemIndex++; @endphp
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-12"><label for="catatan_tambahan" class="form-label">Catatan Tambahan</label>
        <textarea id="catatan_tambahan" name="catatan_tambahan" class="form-control" rows="3">{{ old('catatan_tambahan', $ofa->catatan_tambahan ?? '') }}</textarea>
    </div>
    <div class="col-12 mt-4">
        <h6 class="text-primary fw-bold border-bottom pb-2">Tim Pelaksana</h6>
    </div>
    <div class="col-12 table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>NRP</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Perusahaan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="inspection-team">
                @foreach ($tim as $index => $anggota)
                    <tr>
                        <td><input name="tim_pelaksana[{{ $index }}][nama]" class="form-control"
                                value="{{ $anggota['nama'] ?? '' }}" required></td>
                        <td><input name="tim_pelaksana[{{ $index }}][nrp]" class="form-control"
                                value="{{ $anggota['nrp'] ?? '' }}"></td>
                        <td><input name="tim_pelaksana[{{ $index }}][jabatan]" class="form-control"
                                value="{{ $anggota['jabatan'] ?? '' }}" default="Hardware Engineer"></td>
                        <td><input name="tim_pelaksana[{{ $index }}][departemen]" class="form-control"
                                value="{{ $anggota['departemen'] ?? '' }}"></td>
                        <td><input name="tim_pelaksana[{{ $index }}][perusahaan]" class="form-control"
                                value="{{ $anggota['perusahaan'] ?? '' }}" default="PT Star Perkasa Technology"
                                required></td>
                        <td class="text-center"><button type="button"
                                class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table><button type="button" id="add-team-member" class="btn btn-sm btn-outline-primary"><i
                class="bi bi-plus-lg"></i> Tambah Pelaksana</button>
    </div>

    @include('Inspeksi.partials.photo-upload')
</div>

@push('scripts')
    <script>
        let teamIndex = {{ count($tim) }};
        document.getElementById('add-team-member').addEventListener('click', () => {
            document.getElementById('inspection-team').insertAdjacentHTML('beforeend',
                `<tr><td><input name="tim_pelaksana[${teamIndex}][nama]" class="form-control" required></td><td><input name="tim_pelaksana[${teamIndex}][nrp]" class="form-control"></td><td><input name="tim_pelaksana[${teamIndex}][jabatan]" class="form-control"></td><td><input name="tim_pelaksana[${teamIndex}][departemen]" class="form-control"></td><td><input name="tim_pelaksana[${teamIndex}][perusahaan]" class="form-control" required></td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td></tr>`
            );
            teamIndex++;
        });
        document.addEventListener('click', event => {
            if (event.target.closest('.remove-row')) event.target.closest('tr').remove();
        });
    </script>
@endpush

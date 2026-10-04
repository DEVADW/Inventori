@php
    $jabatan = strtolower(trim((string) (Auth::user()->jabatan ?? '')));
    $jabatan = preg_replace('/\s+/', '_', $jabatan);
    $isUploadAndCamera = in_array($jabatan, ['ict', 'helper', 'hardware_enggineer', 'ict_technician'], true);
    $isCameraOnly = in_array($jabatan, ['hardware_enggineer', 'ict_technician'], true);
@endphp

<div class="col-12">
    <div class="photo-upload-panel">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <label for="photoInput" class="form-label fw-semibold mb-1">
                    <i class="bi bi-camera me-1"></i>Foto Inspeksi
                </label>
                <p class="text-body-secondary small mb-0">Ambil satu foto perangkat sebagai bukti inspeksi.</p>
            </div>
            <span class="badge text-bg-light border fw-normal">JPG, PNG, WEBP · Maks. 5 MB</span>
        </div>

        @if ($isUploadAndCamera)
            <div class="d-flex flex-wrap gap-2">
                <input type="file" name="photos[]" accept="image/*" id="photoInputUpload" class="visually-hidden">
                <label for="photoInputUpload" class="photo-input-trigger">
                    <i class="bi bi-upload"></i>
                    <span>Pilih Foto</span>
                </label>

                <input type="file" name="photos[]" accept="image/*" capture="environment" id="photoInputCamera"
                    class="visually-hidden">
                <label for="photoInputCamera" class="photo-input-trigger">
                    <i class="bi bi-camera-fill"></i>
                    <span>Buka Kamera</span>
                </label>
            </div>
        @else
            <input type="file" name="photos[]" accept="image/*" capture="environment" id="photoInput"
                class="visually-hidden">
            <label for="photoInput" class="photo-input-trigger">
                <i class="bi bi-camera-fill"></i>
                <span>Buka Kamera</span>
            </label>
        @endif

        <div id="previewBox" class="photo-preview-empty" aria-live="polite">
            <i class="bi bi-image text-body-secondary"></i>
            <span>Belum ada foto dipilih</span>
        </div>
    </div>
</div>

<style>
    .photo-upload-panel {
        border: 1px solid var(--bs-border-color);
        border-radius: .75rem;
        padding: 1rem;
        background: color-mix(in srgb, var(--bs-secondary-bg) 55%, transparent);
    }

    .photo-input-trigger {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .625rem .875rem;
        border: 1px solid var(--bs-primary);
        border-radius: .5rem;
        color: var(--bs-primary);
        background: var(--bs-body-bg);
        cursor: pointer;
        font-weight: 600;
        transition: background-color .15s ease, color .15s ease;
    }

    .photo-input-trigger:hover,
    .photo-input-trigger:focus-within {
        color: #fff;
        background: var(--bs-primary);
    }

    .photo-preview-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        min-height: 7rem;
        margin-top: 1rem;
        padding: 1rem;
        border: 1px dashed var(--bs-border-color);
        border-radius: .625rem;
        color: var(--bs-secondary-color);
        text-align: center;
    }

    .photo-preview-empty>i {
        font-size: 1.25rem;
    }

    .photo-preview-card {
        display: flex;
        align-items: center;
        gap: .875rem;
        margin-top: 1rem;
        padding: .625rem;
        border: 1px solid var(--bs-border-color);
        border-radius: .625rem;
        background: var(--bs-body-bg);
    }

    .photo-preview-image {
        width: 6.5rem;
        height: 5rem;
        flex: 0 0 auto;
        border-radius: .4rem;
        object-fit: cover;
        background: var(--bs-secondary-bg);
    }

    .photo-preview-details {
        min-width: 0;
        flex: 1 1 auto;
    }

    .photo-preview-name {
        overflow: hidden;
        margin-bottom: .25rem;
        color: var(--bs-body-color);
        font-size: .875rem;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .photo-remove-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        flex: 0 0 auto;
        border: 1px solid var(--bs-danger-border-subtle);
        border-radius: 50%;
        color: var(--bs-danger);
        background: transparent;
    }

    .photo-remove-button:hover,
    .photo-remove-button:focus-visible {
        color: #fff;
        background: var(--bs-danger);
    }

    @media (max-width: 575.98px) {
        .photo-preview-card {
            align-items: flex-start;
        }

        .photo-preview-image {
            width: 5.5rem;
            height: 4.25rem;
        }
    }
</style>

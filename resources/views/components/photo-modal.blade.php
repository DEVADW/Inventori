<div class="modal fade" id="photoPreviewModal" tabindex="-1" aria-labelledby="photoPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="photoPreviewModalLabel">
                    <i class="bi bi-image me-2"></i>Foto Inspeksi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-2 p-md-3 text-center bg-body-secondary">
                <img id="photoPreviewImage" src="" alt="Foto inspeksi" class="img-fluid rounded shadow-sm"
                    style="max-height: 70vh; object-fit: contain;">
                <p id="photoPreviewEmpty" class="text-body-secondary my-5 d-none">
                    <i class="bi bi-image fs-1 d-block mb-2"></i>Foto belum tersedia.
                </p>
            </div>
            <div class="modal-footer justify-content-between">
                <span id="photoPreviewName" class="text-body-secondary small text-truncate"></span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('show.bs.modal', function (event) {
                if (event.target.id !== 'photoPreviewModal') return;

                const button = event.relatedTarget;
                const image = document.getElementById('photoPreviewImage');
                const emptyState = document.getElementById('photoPreviewEmpty');
                const name = document.getElementById('photoPreviewName');
                const photoUrl = button?.dataset.photoUrl || '';

                image.src = photoUrl;
                image.classList.toggle('d-none', !photoUrl);
                emptyState.classList.toggle('d-none', Boolean(photoUrl));
                name.textContent = button?.dataset.photoName || '';
            });

            document.addEventListener('hidden.bs.modal', function (event) {
                if (event.target.id !== 'photoPreviewModal') return;
                document.getElementById('photoPreviewImage').src = '';
            });
        </script>
    @endpush
@endonce

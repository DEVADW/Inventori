@props(['route'])

<div>
    <!-- Simplicity is the essence of happiness. - Cedric Bledsoe -->
    <div class="modal fade" id="cloneModal" tabindex="-1" aria-labelledby="cloneModalLabel" aria-hidden="true"
        style="z-index: 99999;">
        <div class="modal-dialog modal-dialog-centered" style="z-index: 999999;">
            <form action="{{ $route }}" method="POST">
                @csrf
                <div class="modal-content"
                    style="border-radius: 15px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.3);">
                    <div class="modal-header"
                        style="background:  #b91c1c; color: white; border-radius: 15px 15px 0 0;">
                        <h5 class="modal-title" id="cloneModalLabel">Clone Data Inspeksi</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" style="color: #333;">
                        <div class="alert alert-danger border-0 shadow-sm" style="font-size: 0.9rem;">
                            <i class="fas fa-info-circle me-2"></i>
                            Sistem akan menyalin <strong>seluruh data</strong> dari bulan sumber ke bulan tujuan.
                            Tanggal setiap data akan diatur secara acak (1-25).
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Dari Bulan (Sumber Data)</label>
                            <input type="month" name="dari_bulan" class="form-control" required
                                style="border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Ke Bulan (Tujuan Cloning)</label>
                            <input type="month" name="ke_bulan" class="form-control" required
                                style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                            style="border-radius: 8px; font-weight: 600;">Batal</button>
                        <button type="submit" class="btn px-4"
                            style="background: #b91c1c; color: white; border-radius: 8px; font-weight: 600;">
                            <i class="fas fa-rocket me-1"></i> Jalankan Cloning
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
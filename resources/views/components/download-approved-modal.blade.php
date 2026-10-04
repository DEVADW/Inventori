@props(['route', 'search' => null])

<div class="modal fade" id="downloadApprovedModal" tabindex="-1" aria-labelledby="downloadApprovedModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ $route }}" method="GET" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="downloadApprovedModalLabel">
                    <i class="bi bi-file-zip me-2"></i>Unduh PDF Approved
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Pilih periode inspeksi yang ingin diunduh.</p>
                <div class="row g-3">
                    <div class="col-7">
                        <label for="download-approved-month" class="form-label">Bulan</label>
                        <select id="download-approved-month" name="month" class="form-select" required>
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}" @selected((int) request('month', now()->month) === $month)>
                                    {{ \DateTime::createFromFormat('!m', (string) $month)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-5">
                        <label for="download-approved-year" class="form-label">Tahun</label>
                        <input id="download-approved-year" type="number" name="year" class="form-control"
                            value="{{ request('year', now()->year) }}" min="2000" max="2100" required>
                    </div>
                </div>
                @if ($search)
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-download me-1"></i>Download PDF
                </button>
            </div>
        </form>
    </div>
</div>

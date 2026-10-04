@props(['action' => ''])

<div>
    <div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title" id="editStatusLabel">Ubah Status Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editStatusForm" action="" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body py-3">
                        <!-- Ringkasan Data Peminjaman -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2 text-primary">Detail Peminjaman:</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="35%" class="text-muted">Nama Peminjam</td>
                                        <td>: <strong id="modal-nama">-</strong> (<span id="modal-nrp">-</span>)</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nama Perangkat</td>
                                        <td>: <strong id="modal-perangkat">-</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">No. Asset</td>
                                        <td>: <strong id="modal-asset">-</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <p class="text-center mb-0">Ubah status peminjaman barang ini:</p>

                        <!-- Hidden input untuk menampung status -->
                        <input type="hidden" name="status_peminjaman" id="statusInput" value="">
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                        <div>
                            <button type="submit" onclick="setStatusValue('Belum Dikembalikan')"
                                class="btn btn-danger">
                                Belum Dikembalikan
                            </button>
                            <button type="submit" onclick="setStatusValue('Dikembalikan')" class="btn btn-success">
                                Dikembalikan
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    function setStatusValue(status) {
        document.getElementById('statusInput').value = status;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editStatusModal');

        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                const updateUrl = button.getAttribute('data-bs-url');
                const nama = button.getAttribute('data-bs-nama');
                const nrp = button.getAttribute('data-bs-nrp');
                const perangkat = button.getAttribute('data-bs-perangkat');
                const asset = button.getAttribute('data-bs-asset');

                const form = document.getElementById('editStatusForm');
                if (form && updateUrl) {
                    form.action = updateUrl;
                }

                document.getElementById('modal-nama').textContent = nama || '-';
                document.getElementById('modal-nrp').textContent = nrp || '-';
                document.getElementById('modal-perangkat').textContent = perangkat || '-';
                document.getElementById('modal-asset').textContent = asset || '-';
            });
        }
    });
</script>

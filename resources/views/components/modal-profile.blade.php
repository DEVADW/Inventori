@props(['user'])

<div>
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title fw-bold" id="editUserModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Enctype enctype="multipart/form-data" wajib untuk upload file -->
                <form id="editUser" action="{{ route('users.update', auth()->user()->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body p-4">
                        <!-- Preview & Input Foto Profil -->
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="modalEditPreview"
                                    src="{{ auth()->user()->foto ? asset('storage/' . auth()->user()->foto) : asset('images/default-avatar.png') }}"
                                    alt="Profile Picture"
                                    class="rounded-circle img-thumbnail shadow-sm object-fit-cover"
                                    style="width: 100px; height: 100px;">

                                <label for="modalEditFoto"
                                    class="btn btn-sm btn-warning rounded-circle position-absolute bottom-0 end-0 shadow-sm"
                                    title="Ubah Foto">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                            </div>
                            <input type="file" name="foto" id="modalEditFoto" class="d-none" accept="image/*">
                            <div class="form-text small mt-1">Format: JPG, JPEG, PNG (Max. 2MB)</div>
                        </div>

                        <!-- Input Nama -->
                        <div class="mb-3">
                            <label for="modalEditNama" class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" id="modalEditNama" required>
                        </div>

                        <!-- Input NRP -->
                        <div class="mb-3">
                            <label for="modalEditNrp" class="form-label fw-semibold">NRP</label>
                            <input type="text" name="nrp" class="form-control" id="modalEditNrp" required>
                        </div>

                        <!-- Input Password Baru -->
                        <div class="mb-3">
                            <label for="modalEditPassword" class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control" id="modalEditPassword"
                                placeholder="Kosongkan jika tidak ingin mengubah">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editUserModal');
        const fotoInput = document.getElementById('modalEditFoto');
        const fotoPreview = document.getElementById('modalEditPreview');
        const defaultAvatar = "{{ asset('images/default-avatar.png') }}";

        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;

                const id = button.getAttribute('data-bs-id');
                const nama = button.getAttribute('data-bs-nama');
                const nrp = button.getAttribute('data-bs-nrp');
                const foto = button.getAttribute('data-bs-foto'); // Tambahkan data-bs-foto di pemicu
                const updateUrl = button.getAttribute('data-bs-url');

                // Fill form fields
                document.getElementById('modalEditNama').value = nama || '';
                document.getElementById('modalEditNrp').value = nrp || '';
                document.getElementById('modalEditPassword').value = ''; // Reset password field saat modal dibuka
                document.getElementById('editUser').action = updateUrl;

                // Set Foto Preview dari data-bs-foto
                if (foto) {
                    fotoPreview.src = foto;
                } else {
                    fotoPreview.src = defaultAvatar;
                }
            });
        }

        // Live Preview saat memilih file gambar baru
        if (fotoInput) {
            fotoInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        fotoPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
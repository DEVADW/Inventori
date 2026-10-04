@extends('layouts.app')

@section('judul', 'Pengaturan Akun')
@section('subjudul', 'Kelola Pengaturan Akun Anda')

@section('konten')
    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <!-- Sidebar Menu -->
            <div class="list-group sticky-top">
                <a href="#profile-tab" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                    <i class="bi bi-person-circle me-2"></i> Profil
                </a>
                <a href="#security-tab" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                    <i class="bi bi-shield-lock me-2"></i> Keamanan
                </a>
                <a href="#account-tab" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                    <i class="bi bi-gear me-2"></i> Informasi Akun
                </a>
            </div>
        </div>

        <div class="col-lg-9 col-md-8">
            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="bi bi-exclamation-circle me-2"></i>Ada kesalahan!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Profil Tab -->
                <div class="tab-pane fade show active" id="profile-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-person-circle me-2"></i>Data Profil
                            </h5>

                            <!-- Avatar Section -->
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block mb-3">
                                    <img id="avatarPreview"
                                        src="{{ auth()->user()->foto ? asset('storage/' . auth()->user()->foto) : asset('images/default-avatar.png') }}"
                                        alt="Avatar" class="rounded-circle img-thumbnail shadow-sm object-fit-cover"
                                        style="width: 120px; height: 120px;">

                                    <label for="avatarInput"
                                        class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 shadow"
                                        title="Ubah Foto" style="cursor: pointer;">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                </div>
                                <p class="text-muted small">Format: JPG, JPEG, PNG (Maks. 2MB)</p>
                            </div>

                            <!-- Avatar Upload Form -->
                            <form id="avatarForm" action="{{ route('account-settings.update-avatar') }}" method="POST"
                                enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                            </form>

                            <hr>

                            <!-- Profile Form -->
                            <form action="{{ route('account-settings.update-profile') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                            id="nama" name="nama" value="{{ $user->nama }}" required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nrp" class="form-label fw-semibold">NRP</label>
                                        <input type="text" class="form-control @error('nrp') is-invalid @enderror"
                                            id="nrp" name="nrp" value="{{ $user->nrp }}" required>
                                        @error('nrp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="jabatan" class="form-label fw-semibold">Jabatan</label>
                                    <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                        id="jabatan" name="jabatan" value="{{ $user->jabatan ?? '' }}">
                                    @error('jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Role</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                                    <small class="text-muted">Role tidak dapat diubah dari halaman ini</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-shield-lock me-2"></i>Keamanan Akun
                            </h5>

                            <!-- Password Change Form -->
                            <form action="{{ route('account-settings.update-password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="alert alert-info mb-4">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Tips Keamanan:</strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li>Gunakan password yang kuat dengan kombinasi huruf, angka, dan simbol</li>
                                        <li>Password minimal 8 karakter</li>
                                        <li>Jangan bagikan password Anda kepada siapapun</li>
                                        <li>Ubah password secara berkala untuk keamanan maksimal</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            id="current_password" name="current_password" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('current_password')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('current_password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label fw-semibold">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control @error('new_password') is-invalid @enderror"
                                            id="new_password" name="new_password" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('new_password')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('new_password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label fw-semibold">Konfirmasi
                                        Password Baru</label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                            id="new_password_confirmation" name="new_password_confirmation" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('new_password_confirmation')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('new_password_confirmation')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Ubah Password
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Account Information Tab -->
                <div class="tab-pane fade" id="account-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-gear me-2"></i>Informasi Akun
                            </h5>

                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold w-25">ID Pengguna</td>
                                            <td>
                                                <code>{{ $user->id }}</code>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Nama Lengkap</td>
                                            <td>{{ $user->nama }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">NRP</td>
                                            <td>{{ $user->nrp }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Jabatan</td>
                                            <td>{{ $user->jabatan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Role</td>
                                            <td>
                                                <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Akun Dibuat</td>
                                            <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Terakhir Diperbarui</td>
                                            <td>{{ $user->updated_at->format('d M Y H:i') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <hr>

                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Zona Berbahaya</strong>
                                <p class="mb-2 mt-2">Tindakan di bawah ini tidak dapat dibatalkan. Harap lakukan dengan hati-hati.</p>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDeleteAccount()">
                                    <i class="bi bi-trash me-1"></i>Hapus Akun Secara Permanen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = event.target.closest('button').querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Avatar upload
        document.getElementById('avatarInput').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);

                // Auto submit the form
                document.getElementById('avatarForm').submit();
            }
        });

        // Confirm delete account
        function confirmDeleteAccount() {
            Swal.fire({
                title: 'Hapus Akun?',
                text: 'Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Akun',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: Create delete account form and submit
                    Swal.fire({
                        title: 'Belum Tersedia',
                        text: 'Fitur ini masih dalam pengembangan.',
                        icon: 'info',
                    });
                }
            });
        }
    </script>
@endsection

{{-- Alert Messages --}}
@php
 $jabatan = [
       'SH' => 'Section Head', 
       'GL' => 'Group Leader', 
       'staff' => 'Staff', 
       'non_staff' => 'Non Staff', 
       'helper' => 'Helper ICT'
   ];
@endphp

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

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="nama" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama"
                value="{{ old('nama', $user->nama ?? '') }}" required>
            @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="nrp" class="form-label fw-semibold">NRP <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nrp') is-invalid @enderror" id="nrp" name="nrp"
                value="{{ old('nrp', $user->nrp ?? '') }}" required>
            @error('nrp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="jabatan" class="form-label fw-semibold">Jabatan</label>
            <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan"
                name="jabatan" value="{{ old('jabatan', $user->jabatan ?? '') }}">
            @error('jabatan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>
                    <i class="bi bi-shield-check"></i> Admin
                </option>
                <option value="user" {{ old('role', $user->role ?? '') === 'user' ? 'selected' : '' }}>
                    <i class="bi bi-person"></i> User
                </option>
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr>

<h6 class="fw-semibold mb-3">
    <i class="bi bi-lock me-2"></i>Keamanan
</h6>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">
                Password
                @if (isset($user))
                    <span class="text-muted small">(Kosongkan jika tidak ingin mengubah)</span>
                @else
                    <span class="text-danger">*</span>
                @endif
            </label>
            <div class="input-group">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" {{ !isset($user) ? 'required' : '' }}>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <small class="text-muted">Minimal 8 karakter</small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">
                Konfirmasi Password
                @if (!isset($user))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <div class="input-group">
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                    id="password_confirmation" name="password_confirmation" {{ !isset($user) ? 'required' : '' }}>
                <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePassword('password_confirmation')">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password_confirmation')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<script>
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
</script>

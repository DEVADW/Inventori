<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Inventaris, Inspeksi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('asset/css/login_costum.css') }}" rel="stylesheet">
</head>

@if ($errors->any())
    <div class="alert alert-danger py-2 mb-3">
        {{ $errors->first() }}
    </div>
@else
@endif

<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-center">
                <a href="#" class="" style="text-decoration: none;">
                    <img src="{{ asset('asset/images/logos/logo_ppa.png') }}" alt="Logo PT PPA">
                    <p class="text-light h3"
                        style="font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;">PPA</p>
                </a>
            </div>

            <div class="card-body login-card-body">
                <p class="login-box-msg">Masukkan NRP dan Password Untuk Melakukan Login</p>

                <form method="post">
                    @csrf
                    <div class="input-group mb-2">
                        <div class="form-floating">
                            <input id="loginNRP" value="{{ old('nrp') }}" name="nrp" type="text"
                                class="form-control" placeholder="NRP" autocomplete="username" />
                            <label for="loginNRP">NRP</label>

                        </div>
                        <div class="input-group-text">
                            <span class="bi bi-person-vcard-fill"></span>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input id="loginPassword" name="password" type="password" class="form-control"
                                placeholder="Password" autocomplete="current-password" />
                            <label for="loginPassword">Password</label>

                        </div>
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Sign In</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

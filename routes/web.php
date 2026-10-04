<?php


// import
use Illuminate\Support\Facades\Route;


// inspeksi

use App\Http\Controllers\inspeksi\UpsController;
use App\Http\Controllers\inspeksi\StavoltController;
use App\Http\Controllers\inspeksi\MonitorController;
use App\Http\Controllers\inspeksi\ProyektorController;

use App\Http\Controllers\BukuTamuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarisController;


// Auth Controller
use App\Http\Controllers\Auth\ProfileController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\inspeksi\Ss6Controller;
use App\Http\Controllers\inspeksi\OfaController;
use App\Http\Controllers\inspeksi\IccController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\UserManagementController;

// Route::get('/', function () {
//     return view('dashboard');
// });

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role == 'admin') {
            return redirect()->route('dashboard');
        }

        // return redirect()->route('security.dashboard');
    }
    return redirect()->route('login');
});

// Auntetikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

// Route buat aplikasi
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Route buku tamu
    Route::middleware('can:BukuTamu')->group(function () {
        Route::controller(BukuTamuController::class)->prefix('/tamu')->name('tamu.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store/', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/update/{id}', 'update')->name('update');
            Route::delete('/destroy/{id}', 'destroy')->name('destroy');
            Route::get('/export', 'export')->name('export');
        });
    });

    Route::middleware('jabatan:helper,admin,GL,ICT,hardware_enggineer,Hardware Engineer,ICT_technician,ICT Technician,non_staff')->group(function () {

    //Route Inventaris 
    Route::controller(InventarisController::class)->prefix('/inventaris')->name('inventaris.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
        Route::get('/export', 'export')->name('export');
        Route::put('/return/{id}', 'returnStatus')->name('returnStatus');
    });

    //Route Karyawan
    Route::controller(KaryawanController::class)->prefix('/karyawan')->name('karyawan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/{karyawan}/edit', 'edit')->name('edit');
        Route::put('/{karyawan}/update', 'update')->name('update');
        Route::delete('/{karyawan}/destroy', 'destroy')->name('destroy');
    });



    // Route Inspeksi
    // Route Stavolt
    Route::controller(StavoltController::class)
        ->prefix('/inspeksi/stavolt')
        ->name('inspeksi.stavolt.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::post('/approve-all', 'approveAll')->name('approve-all');
            Route::get('/download-approved', 'downloadApproved')->name('download-approved');
            Route::post('/{stavolt}/approve', 'approve')->name('approve');
            Route::get('/{stavolt}/edit', 'edit')->name('edit');
            Route::put('/{stavolt}', 'update')->name('update');
            Route::delete('/{stavolt}', 'destroy')->name('destroy');
            Route::get('/{stavolt}/pdf', 'pdf')->name('pdf');
            Route::post('/clone', 'clone')->name('clone');

        });

    // Route UPS
    Route::controller(UpsController::class)
        ->prefix('/inspeksi/ups')
        ->name('inspeksi.ups.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::post('/approve-all', 'approveAll')->name('approve-all');
            Route::get('/download-approved', 'downloadApproved')->name('download-approved');
            Route::post('/{ups}/approve', 'approve')->name('approve');
            Route::get('/{ups}/edit', 'edit')->name('edit');
            Route::put('/{ups}', 'update')->name('update');
            Route::delete('/{ups}', 'destroy')->name('destroy');
            Route::get('/{ups}/pdf', 'pdf')->name('pdf');
            Route::post('/clone', 'clone')->name('clone');
        });

    // Route Monitor
    Route::controller(MonitorController::class)
        ->prefix('/inspeksi/monitor')
        ->name('inspeksi.monitor.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::post('/approve-all', 'approveAll')->name('approve-all');
            Route::get('/download-approved', 'downloadApproved')->name('download-approved');
            Route::post('/{monitor}/approve', 'approve')->name('approve');
            Route::get('/{monitor}/edit', 'edit')->name('edit');
            Route::put('/{monitor}', 'update')->name('update');
            Route::delete('/{monitor}', 'destroy')->name('destroy');
            Route::get('/{monitor}/pdf', 'pdf')->name('pdf');
            Route::post('/clone', 'clone')->name('clone');

        });

    // Route Proyektor
    Route::controller(ProyektorController::class)
        ->prefix('/inspeksi/proyektor')
        ->name('inspeksi.proyektor.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::post('/approve-all', 'approveAll')->name('approve-all');
            Route::get('/download-approved', 'downloadApproved')->name('download-approved');
            Route::post('/{proyektor}/approve', 'approve')->name('approve');
            Route::get('/{proyektor}/edit', 'edit')->name('edit');
            Route::put('/{proyektor}', 'update')->name('update');
            Route::delete('/{proyektor}', 'destroy')->name('destroy');
            Route::get('/{proyektor}/pdf', 'pdf')->name('pdf');
            Route::post('/clone', 'clone')->name('clone');

        });

    // Route SS6
    Route::controller(Ss6Controller::class)
        ->prefix('/inspeksi/ss6')
        ->name('inspeksi.ss6.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::post('/approve-all', 'approveAll')->name('approve-all');
            Route::get('/download-approved', 'downloadApproved')->name('download-approved');
            Route::post('/{inspeksi}/approve', 'approve')->name('approve');
            Route::get('/{inspeksi}/edit', 'edit')->name('edit');
            Route::put('/{inspeksi}', 'update')->name('update');
            Route::delete('/{inspeksi}', 'destroy')->name('destroy');
            Route::get('/{inspeksi}/pdf', 'pdf')->name('pdf');
            Route::post('/clone', 'clone')->name('clone');

        });

    // Route Inspeksi Perangkat Onboard FleetSafe Assist (OFA)
    Route::controller(OfaController::class)
        ->prefix('/inspeksi/ofa')
        ->name('inspeksi.ofa.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::post('/approve-all', 'approveAll')->name('approve-all');
            Route::get('/download-approved', 'downloadApproved')->name('download-approved');
            Route::post('/{ofa}/approve', 'approve')->name('approve');
            Route::get('/{ofa}/edit', 'edit')->name('edit');
            Route::put('/{ofa}', 'update')->name('update');
            Route::delete('/{ofa}', 'destroy')->name('destroy');
            Route::get('/{ofa}/pdf', 'pdf')->name('pdf');
            Route::post('/clone', 'clone')->name('clone');

        });

    Route::controller(IccController::class)
        ->prefix('/inspeksi/icc')
        ->name('inspeksi.icc.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::post('/approve-all', 'approveAll')->name('approve-all');
            Route::get('/download-approved', 'downloadApproved')->name('download-approved');
            Route::post('/{icc}/approve', 'approve')->name('approve');
            Route::get('/{icc}/edit', 'edit')->name('edit');
            Route::put('/{icc}', 'update')->name('update');
            Route::delete('/{icc}', 'destroy')->name('destroy');
            Route::get('/{icc}/pdf', 'pdf')->name('pdf');
            Route::post('/clone', 'clone')->name('clone');

        });
    });


    // Account Settings Route
    Route::controller(AccountSettingsController::class)->prefix('/account-settings')->name('account-settings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/profile', 'updateProfile')->name('update-profile');
        Route::put('/password', 'updatePassword')->name('update-password');
        Route::post('/avatar', 'updateAvatar')->name('update-avatar');
    });
    

    // User Management Route (Admin Only)
    Route::middleware('can:isAdmin')->controller(UserManagementController::class)->prefix('/users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{user}/edit', 'edit')->name('edit');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

    // Old Profile Modal Route (kept for backward compatibility)
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

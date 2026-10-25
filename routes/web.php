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
use App\Http\Controllers\SimCardController;
use App\Http\Controllers\UnitOperasiController;
use App\Http\Controllers\InventoriStavoltController;
use App\Http\Controllers\InventoriUpsController;
use App\Http\Controllers\InventoriTvController;
use App\Http\Controllers\InventoriProyektorController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\InspectionSettingController;
use App\Http\Controllers\AccountManagementController;

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

    //Route Data Aset - SIM Card
    Route::controller(SimCardController::class)->prefix('/aset/sim-card')->name('aset.simcard.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::post('/import', 'import')->name('import');
        Route::get('/{simcard}/edit', 'edit')->name('edit');
        Route::put('/{simcard}/update', 'update')->name('update');
        Route::delete('/{simcard}/destroy', 'destroy')->name('destroy');
    });

    //Route Data Aset - Unit Operasi
    Route::controller(UnitOperasiController::class)->prefix('/aset/unit-operasi')->name('aset.unitoperasi.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::post('/import', 'import')->name('import');
        Route::get('/{unitoperasi}/edit', 'edit')->name('edit');
        Route::put('/{unitoperasi}/update', 'update')->name('update');
        Route::delete('/{unitoperasi}/destroy', 'destroy')->name('destroy');
    });

    //Route Data Aset - Inventori Stavolt
    Route::controller(InventoriStavoltController::class)->prefix('/aset/inventori-stavolt')->name('aset.inventoristavolt.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::post('/import', 'import')->name('import');
        Route::get('/{inventoristavolt}/edit', 'edit')->name('edit');
        Route::put('/{inventoristavolt}/update', 'update')->name('update');
        Route::delete('/{inventoristavolt}/destroy', 'destroy')->name('destroy');
    });

    //Route Data Aset - Inventori UPS
    Route::controller(InventoriUpsController::class)->prefix('/aset/inventori-ups')->name('aset.inventoriups.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::post('/import', 'import')->name('import');
        Route::get('/{inventoriups}/edit', 'edit')->name('edit');
        Route::put('/{inventoriups}/update', 'update')->name('update');
        Route::delete('/{inventoriups}/destroy', 'destroy')->name('destroy');
    });

    //Route Data Aset - Inventori TV
    Route::controller(InventoriTvController::class)->prefix('/aset/inventori-tv')->name('aset.inventoritv.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::post('/import', 'import')->name('import');
        Route::get('/{inventoritv}/edit', 'edit')->name('edit');
        Route::put('/{inventoritv}/update', 'update')->name('update');
        Route::delete('/{inventoritv}/destroy', 'destroy')->name('destroy');
    });

    //Route Data Aset - Inventori Proyektor
    Route::controller(InventoriProyektorController::class)->prefix('/aset/inventori-proyektor')->name('aset.inventoriproyektor.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::post('/import', 'import')->name('import');
        Route::get('/{inventoriproyektor}/edit', 'edit')->name('edit');
        Route::put('/{inventoriproyektor}/update', 'update')->name('update');
        Route::delete('/{inventoriproyektor}/destroy', 'destroy')->name('destroy');
    });



    // Route Inspeksi
    // Route Stavolt
    Route::controller(StavoltController::class)
        ->middleware('inspeksi.access:stavolt')
        ->prefix('/inspeksi/stavolt')
        ->name('inspeksi.stavolt.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/export', 'export')->name('export');
            Route::get('/create/{asset}', 'create')->name('create');
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
        ->middleware('inspeksi.access:ups')
        ->prefix('/inspeksi/ups')
        ->name('inspeksi.ups.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/export', 'export')->name('export');
            Route::get('/create/{asset}', 'create')->name('create');
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
        ->middleware('inspeksi.access:monitor')
        ->prefix('/inspeksi/monitor')
        ->name('inspeksi.monitor.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/export', 'export')->name('export');
            Route::get('/create/{asset}', 'create')->name('create');
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
        ->middleware('inspeksi.access:proyektor')
        ->prefix('/inspeksi/proyektor')
        ->name('inspeksi.proyektor.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/export', 'export')->name('export');
            Route::get('/create/{asset}', 'create')->name('create');
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
        ->middleware('inspeksi.access:ss6')
        ->prefix('/inspeksi/ss6')
        ->name('inspeksi.ss6.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/export', 'export')->name('export');
            Route::get('/create/{asset}', 'create')->name('create');
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
        ->middleware('inspeksi.access:ofa')
        ->prefix('/inspeksi/ofa')
        ->name('inspeksi.ofa.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/export', 'export')->name('export');
            Route::get('/create/{asset}', 'create')->name('create');
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
        ->middleware('inspeksi.access:icc')
        ->prefix('/inspeksi/icc')
        ->name('inspeksi.icc.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/export', 'export')->name('export');
            Route::get('/create/{asset}', 'create')->name('create');
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

    // Account Management Route (Admin Only)
    Route::middleware('can:isAdmin')->controller(AccountManagementController::class)->prefix('/manajemen-akun')->name('manajemen-akun.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{user}/edit', 'edit')->name('edit');
        Route::put('/{user}', 'update')->name('update');
    });

    // Inspection Period Settings Route (Admin Only)
    Route::middleware('can:isAdmin')->controller(InspectionSettingController::class)->prefix('/pengaturan/inspeksi')->name('settings.inspeksi.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/{module}', 'update')->name('update');
    });

    // Old Profile Modal Route (kept for backward compatibility)
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

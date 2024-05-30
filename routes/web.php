    <?php

    use App\Http\Controllers\Admin\AdminController;
    use App\Http\Controllers\Admin\DashboardController;
    use App\Http\Controllers\Admin\LaporanController;
    use App\Http\Controllers\Admin\MasyarakatController;
    use App\Http\Controllers\Admin\PengaduanController;
    use App\Http\Controllers\Admin\PetugasController;
    use App\Http\Controllers\Admin\TanggapanController;
    use App\Http\Controllers\User\EmailController;
    use App\Http\Controllers\User\SocialController;
    use App\Http\Controllers\User\UserController;
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    Route::get('/', [UserController::class, 'index'])->name('pema.index');

    Route::post('/masyarakat/sendverification', [EmailController::class, 'sendVerification'])->name('pema.sendVerification');
    Route::get('/masyarakat/verify/{nik}', [EmailController::class, 'verify'])->name('pema.verify');

    Route::middleware(['isMasyarakat'])->group(function () {
        // Pengaduan
        Route::post('/store', [UserController::class, 'storePengaduan'])->name('pema.store');
        Route::get('/laporan/{siapa?}', [UserController::class, 'laporan'])->name('pema.laporan');

        Route::get('/profil', [UserController::class, 'profil'])->name('pema.profil');
        Route::post('/profil/update', [UserController::class, 'updateProfile'])->name('pema.update.profile');
        Route::post('/upload-photo', [UserController::class, 'uploadPhoto'])->name('pema.upload.photo');

        Route::post('/profil/change-password', [UserController::class, 'changePassword'])->name('pema.change.password');
        // Logout Masyarakat
        Route::get('/logout', [UserController::class, 'logout'])->name('pema.logout');
    });

    Route::middleware(['guest'])->group(function () {
        // Login Masyarakat
        Route::post('/login/auth', [UserController::class, 'login'])->name('pema.login');

        // Register
        Route::get('/register', [UserController::class, 'formRegister'])->name('pema.formRegister');
        Route::post('/register/auth', [UserController::class, 'register'])->name('pema.register');

        // Media Sosial
        Route::get('auth/{provider}', [SocialController::class, 'redirectToProvider'])->name('pema.auth');
        Route::get('auth/{provider}/callback', [SocialController::class, 'handleProviderCallback']);
    });

    Route::prefix('admin')->group(function () {

        Route::middleware(['isAdmin'])->group(function () {
            // Petugas
            Route::resource('petugas', PetugasController::class);

            // Masyarakat
            Route::resource('masyarakat', MasyarakatController::class);

            // Laporan
            Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
            Route::post('getLaporan', [LaporanController::class, 'getLaporan'])->name('laporan.getLaporan');
            Route::get('laporan/cetak/{from}/{to}', [LaporanController::class, 'cetakLaporan'])->name('laporan.cetakLaporan');
        });

        Route::middleware(['isPetugas'])->group(function () {
            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

            // Pengaduan
            Route::resource('pengaduan', PengaduanController::class);

            // Taanggapan
            Route::post('tanggapan/createOrUpdate', [TanggapanController::class, 'createOrUpdate'])->name('tanggapan.createOrUpdate');

            // Logout
            Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        });

        Route::middleware(['isGuest'])->group(function () {
            Route::get('/', [AdminController::class, 'formLogin'])->name('admin.formLogin');
            Route::post('/login', [AdminController::class, 'login'])->name('admin.login');
        });
        
        Route::delete('/delete/pengaduan/{id}', [UserController::class, 'deletePengaduan'])->name('pema.deletePengaduan');

    });

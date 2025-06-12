<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnggotaController;
use App\Http\Controllers\Api\AnggotaForumController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\ProyekController;
use App\Http\Controllers\Api\KaryaController;
use App\Http\Controllers\Api\KomenProyekController;
use App\Http\Controllers\Api\KomenForumController;
use App\Http\Controllers\Api\KategoriSeniController;
use App\Http\Controllers\Api\PenilaianKaryaController;
use App\Http\Controllers\Api\PenilaiController;
use App\Http\Controllers\Api\PortofolioController;
use App\Http\Controllers\Api\RegisterIndividuController;
use App\Http\Controllers\Api\RegisterKelompokController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RubrikController;
use App\Http\Controllers\Api\SeniController;
use App\Http\Controllers\Api\SenimanController;
use App\Http\Controllers\Api\TingkatanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\ChartController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\InfoController;
use App\Http\Controllers\Api\NotificationController;
use Laravel\Passport\Passport;

Passport::routes();
Route::get('hash', [AuthController::class,'hash']);
Route::post('register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/roles', [AuthController::class, 'getRoles']);

Route::post('seniman', [SenimanController::class, 'store']);
Route::get('/map-sanggar', [MapController::class, 'indexAll']);

Route::middleware('cors', 'auth:api')->group(function () {
    Route::apiResource('penilai', PenilaiController::class);
    Route::apiResource('forum', ForumController::class);
    Route::apiResource('proyek', ProyekController::class);
    Route::apiResource('seni', SeniController::class);
    Route::apiResource('penilaianKarya', PenilaianKaryaController::class);
    Route::apiResource('kategoriSeni', KategoriSeniController::class);
    Route::apiResource('registerIndividu', RegisterIndividuController::class);
    Route::apiResource('registerKelompok', RegisterKelompokController::class);
    Route::apiResource('rubrik', RubrikController::class);
    Route::apiResource('tingkatan', TingkatanController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('role', RoleController::class);
    Route::apiResource('karya', KaryaController::class);
    Route::apiResource('komenProyek', KomenProyekController::class);
    Route::apiResource('komenForum', KomenForumController::class);
    Route::apiResource('portofolio', PortofolioController::class);
    Route::apiResource('anggota', AnggotaController::class);
    Route::apiResource('map', MapController::class);
    Route::apiResource('info', InfoController::class);

    Route::post('/user/store-byAdmin', [UserController::class, 'storeByAdmin']);
    Route::post('/get-kuota-id', [PenilaianKaryaController::class, 'getKuotaId']);
    Route::get('/karyas/{id}', [KaryaController::class, 'index']);
    Route::get('/seniman', [SenimanController::class, 'index']);
    Route::get('/total-seniman', [SenimanController::class, 'getTotalSeniman']);
    Route::get('/total-penilai', [PenilaiController::class, 'getTotalPenilai']);
    Route::get('/total-user', [UserController::class, 'getTotalUser']);
    Route::get('/total-registrasi-individu', [RegisterIndividuController::class, 'getTotalIndividu']);
    Route::get('/total-registrasi-kelompok', [RegisterKelompokController::class, 'getTotalKelompok']);
    Route::get('/rubrikPenilai', [RubrikController::class, 'indexByUser']);
    Route::get('/myForum', [ForumController::class, 'indexByUser']);
    Route::get('/followForum', [ForumController::class, 'indexFollowForum']);
    Route::put('/seniman/{id}', [SenimanController::class, 'update']);
    Route::get('/seniman/{id}', [SenimanController::class, 'show']);
    Route::delete('/seniman/{id}', [SenimanController::class, 'destroy']);
    Route::get('/nama-kategori', [KategoriSeniController::class, 'indexKategori']);
    Route::get('/userbypenilai', [UserController::class,'indexByPenilai']);
    Route::get('/userbyseniman', [UserController::class,'indexBySeniman']);
    Route::get('/index-proyek', [ProyekController::class, 'indexProyekUser']);
    Route::get('/portofolio/data/{id}', [PortofolioController::class, 'showData']);
    Route::get('/penilai/laporan', [PenilaiController::class, 'showLaporan']);
    Route::get('/penilai/downloadLaporan', [PenilaiController::class, 'downloadPenilaiLaporan']);
    Route::post('/seniman/storeByAdmin', [SenimanController::class, 'storebyAdmin']);
    Route::get('/seni-by-kategori/{kategoriNama}', [SeniController::class, 'getSeniByKategori']);
    Route::get('/registerIndividu/showByAdmin/{id}', [RegisterIndividuController::class, 'showByAdmin']);
    Route::get('/registerKelompok/showByAdmin/{id}', [RegisterKelompokController::class, 'showByAdmin']);
    Route::post('/registerIndividu/storeByAdmin', [RegisterIndividuController::class, 'storebyAdmin']);
    Route::post('/registerKelompok/storeByAdmin', [RegisterKelompokController::class, 'storebyAdmin']);
    Route::put('/registerIndividu/updateByAdmin/{id}', [RegisterIndividuController::class, 'updateByAdmin']);
    Route::put('/registerKelompok/storeByAdmin/{id}', [RegisterKelompokController::class, 'updateByAdmin']);
    Route::get('/registerIndividuUser', [RegisterIndividuController::class, 'getRegistrasiIndividu']);
    Route::get('/registerKelompokUser', [RegisterKelompokController::class, 'getRegistrasiKelompok']);
    Route::get('/registerIndividuPenilai/{penilai_id}', [RegisterIndividuController::class, 'indexForPenilai']);
    Route::get('/registerKelompokPenilai/{penilai_id}', [RegisterKelompokController::class, 'indexForPenilai']);
    Route::get('/registrasi-portofolio/individu', [PortofolioController::class, 'filterByIndividu']);
    Route::get('/registrasi-portofolio/kelompok', [PortofolioController::class, 'filterByKelompok']);
    Route::post('/proyek/{id}/like', [ProyekController::class, 'likeProyek']);
    Route::post('/proyek/{id}/unlike', [ProyekController::class, 'unlikeProyek']);
    Route::get('/anggota/kelompok/{kelompok_id}', [AnggotaController::class, 'indexByKelompok']);
    Route::get('/portofolio/kelompok', [RegisterKelompokController::class, 'getPortofolioKelompok']);
    Route::post('/join-forum', [AnggotaForumController::class, 'joinForum']);
    Route::delete('/out-forum/{forum_id}/anggota/{anggota_id}', [AnggotaForumController::class, 'destroy']);
    Route::get('/anggota-forum/{forum_id}', [AnggotaForumController::class, 'index']);
    Route::get('/user-count', [ChartController::class, 'userCountChart']);
    Route::get('/registrasi-count', [ChartController::class, 'registrasiCountChart']);
    Route::get('/laporan/download-pdf', [LaporanController::class, 'downloadLaporanData']);
    Route::get('/laporan/preview-pdf', [LaporanController::class, 'previewLaporanData']);
    Route::get('/show-byregis/{id}', [PenilaianKaryaController::class, 'showByRegistrationId']);
    Route::get('/notifikasi/unread', [NotificationController::class, 'unreadCount']);

    Route::get('/user-profile', function (Request $request) {
        return $request->user();
    });
});


?>

<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CertificateController;
use App\Http\Controllers\api\CtemplateController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/ctemplates', [CtemplateController::class, 'index'])->middleware('app.token');
Route::delete('/ctemplates/delete/{id}', [CtemplateController::class, 'delete'])->middleware('app.token');

Route::get('/certificates', [CertificateController::class, 'index'])->middleware('app.token');
Route::get('/certificates/{id}', [CertificateController::class, 'zipDetails'])->middleware('app.token');
Route::post('/certificates/store', [CertificateController::class, 'store'])->middleware('app.token');
Route::post('/certificates/render', [CertificateController::class, 'renderForPuppeteer']);
Route::delete('/certificates/delete/{id}', [CertificateController::class, 'delete'])->middleware('app.token');
Route::get('/certificates/download-zip/{id}', [CertificateController::class, 'downloadZip'])->middleware('app.token');
Route::get('/certificates/download/{id}', [CertificateController::class, 'downloadCertificate'])->middleware('app.token');

Route::post('/certificates/save-image', [CertificateController::class, 'saveCertificateCanvas']);

Route::middleware('jwt.cookie', 'auth:api', 'app.token')->group(function() {
    Route::get('/certificates', [CertificateController::class, 'index']);
});
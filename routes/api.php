<?php

use App\Http\Controllers\admin\AdminCtemplateController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CertificateController;
use App\Http\Controllers\api\CtemplateController;
use App\Http\Controllers\api\GetTokenController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::post('/get-token', [GetTokenController::class, 'getToken']);
Route::post('/get-temp-token', [GetTokenController::class, 'getTempToken']);

Route::get('/ctemplates', [CtemplateController::class, 'index'])->middleware('app.token');
Route::post('/ctemplate/store', [AdminCtemplateController::class, 'store'])->middleware('iframe.jwt');
Route::put('/ctemplate/update/{id}', [CtemplateController::class, 'update'])->middleware('iframe.jwt');
Route::delete('/ctemplates/delete/{id}', [CtemplateController::class, 'delete'])->middleware('app.token');

Route::get('/certificates', [CertificateController::class, 'index'])->middleware('app.token');
Route::get('/certificates/{id}', [CertificateController::class, 'zipDetails'])->middleware('app.token');
Route::post('/certificates/store', [CertificateController::class, 'store'])->middleware('app.token');
Route::post('/certificates/render', [CertificateController::class, 'renderForPuppeteer']);
Route::delete('/certificates/delete/{id}', [CertificateController::class, 'delete'])->middleware('app.token');
Route::get('/certificates/download-zip/{id}', [CertificateController::class, 'downloadZip'])->middleware('app.token');
Route::get('/certificates/download/{id}', [CertificateController::class, 'downloadCertificate'])->middleware('app.token');
Route::post('/upload-image', [AdminCtemplateController::class, 'uploadImage'])->middleware('iframe.jwt');

Route::post('/certificates/save-image', [CertificateController::class, 'saveCertificateCanvas']);
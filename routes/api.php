<?php

use App\Http\Controllers\api\CertificateController;
use App\Http\Controllers\api\CtemplateController;
use App\Models\Ctemplates;
use Illuminate\Support\Facades\Route;

Route::get('/ctemplates', [CtemplateController::class, 'index'])->middleware('api.auth');
Route::delete('/ctemplates/delete/{id}', [CtemplateController::class, 'delete'])->middleware('api.auth');

Route::get('/certificates', [CertificateController::class, 'index'])->middleware('api.auth');
Route::get('/certificates/{id}', [CertificateController::class, 'zipDetails']);
Route::post('/certificates/store', [CertificateController::class, 'store'])->middleware('api.auth');
Route::post('/certificates/render', [CertificateController::class, 'renderForPuppeteer']);
Route::post('/certificates/save-puppeteer', [CertificateController::class, 'saveCertificatePuppeteer']);
Route::delete('/certificates/delete/{id}', [CertificateController::class, 'delete']);

Route::post('/certificates/save-image', [CertificateController::class, 'saveCertificateCanvas']);
<?php

use App\Http\Controllers\admin\AdminCertificateController;
use App\Http\Controllers\admin\AdminCtemplateController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminReceiverController;
use App\Http\Controllers\admin\AdminUserController;
use App\Http\Controllers\AuthController;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/admin', [AdminDashboardController::class, 'index']);

Route::get('/admin/users', [AdminUserController::class, 'index']);

Route::get('/admin/receiver', [AdminReceiverController::class, 'index']);

Route::get('/admin/ctemplate', [AdminCtemplateController::class, 'index']);
Route::get('/admin/ctemplate/create', [AdminCtemplateController::class, 'create']);
Route::post('/admin/ctemplate/store', [AdminCtemplateController::class, 'store']);
Route::post('/upload-image', [AdminCtemplateController::class, 'uploadImage']);

Route::get('/admin/certificate', [AdminCertificateController::class, 'index']);
Route::get('/admin/certificate/create/{id}', [AdminCertificateController::class, 'create']);
Route::post('/admin/certificate/store', [AdminCertificateController::class, 'store']);
Route::post('/admin/certificate/save', [AdminCertificateController::class, 'saveCertificate']);
Route::get('/admin/certificate/detail/{id}', [AdminCertificateController::class, 'zipDetails']);
Route::get('/download-certificate/{id}', [AdminCertificateController::class, 'downlaodCertificate']);
Route::get('/downlaod-certificate-zip/{id}', [AdminCertificateController::class, 'downloadZip']);
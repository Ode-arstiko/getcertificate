<?php

use App\Http\Controllers\admin\AdminAppClientsController;
use App\Http\Controllers\admin\AdminCertificateController;
use App\Http\Controllers\admin\AdminCtemplateController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminMakeTokenController;
use App\Http\Controllers\admin\AdminReceiverController;
use App\Http\Controllers\admin\AdminUserController;
use App\Http\Controllers\api\CertificateController;
use App\Http\Controllers\api\CtemplateController;
use App\Http\Controllers\api\GetTokenController;
use App\Http\Controllers\AuthController;
use App\Models\Ctemplates;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/admin', [AdminDashboardController::class, 'index']);

Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.user');
Route::post('/admin/user/store', [AdminUserController::class, 'store'])->name('admin.user.store');
Route::delete('/admin/user/delete/{id}', [AdminUserController::class, 'destroy'])->name('admin.user.delete');
Route::put('/admin/user/update/{id}', [AdminUserController::class, 'update'])->name('admin.user.update');

Route::get('/admin/clients-app', [AdminAppClientsController::class, 'index']);
Route::post('/admin/clients-app/store', [AdminAppClientsController::class, 'store']);
Route::delete('/admin/clients-app/delete/{id}', [AdminAppClientsController::class, 'delete']);

Route::get('/admin/receiver', [AdminReceiverController::class, 'index']);

Route::get('/admin/ctemplate', [AdminCtemplateController::class, 'index']);
Route::get('/admin/ctemplate/create', [AdminCtemplateController::class, 'create']);
Route::post('/admin/ctemplate/store', [AdminCtemplateController::class, 'store']);
Route::get('/admin/ctemplate/edit/{id}', [AdminCtemplateController::class, 'edit']);
Route::put('/admin/ctemplate/update/{id}', [AdminCtemplateController::class, 'update']);
Route::delete('/admin/ctemplate/delete/{id}', [AdminCtemplateController::class, 'delete']);
Route::post('/upload-image', [AdminCtemplateController::class, 'uploadImage']);

Route::get('/admin/certificate', [AdminCertificateController::class, 'index']);
Route::get('/admin/certificate/create/{id}', [AdminCertificateController::class, 'create']);
Route::post('/admin/certificate/store', [AdminCertificateController::class, 'store']);
Route::post('/admin/certificate/save', [AdminCertificateController::class, 'saveCertificate']);
Route::get('/admin/certificate/detail/{id}', [AdminCertificateController::class, 'zipDetails']);
Route::get('/download-certificate/{id}', [AdminCertificateController::class, 'downlaodCertificate']);
Route::get('/downlaod-certificate-zip/{id}', [AdminCertificateController::class, 'downloadZip']);

Route::get('/canvas-editor', [CtemplateController::class, 'create']);

Route::get('/canvas-editor-edit/{id}', [CtemplateController::class, 'edit']);

Route::post('/certificates/save-image', [CertificateController::class, 'saveCertificateCanvas']);
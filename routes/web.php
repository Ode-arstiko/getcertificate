<?php

use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminReceiverController;
use App\Http\Controllers\admin\AdminUserController;
use App\Http\Controllers\AuthController;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\Route;

// Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/admin', [AdminDashboardController::class, 'index']);

Route::get('/admin/users', [AdminUserController::class, 'index']);

Route::get('/admin/receiver', [AdminReceiverController::class, 'index']);
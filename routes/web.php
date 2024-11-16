<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::get('/', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']); // Untuk menambah customer
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']); // Untuk menghapus customer
Route::get('/customers/{id}/edit', [CustomerController::class, 'edit']); // Untuk menampilkan form edit
Route::put('/customers/{id}', [CustomerController::class, 'update']);    // Untuk proses update data

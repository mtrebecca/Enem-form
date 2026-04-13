<?php

use App\Modules\Users\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [UsersController::class, 'dashboard']);
Route::get('/minha-conta', [UsersController::class, 'minhaConta']);
Route::get('/minha-conta/historico', [UsersController::class, 'historico']);

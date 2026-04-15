<?php

use App\Modules\Resultados\Http\Controllers\ResultadosController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function (): void {
    Route::get('/resultados/{prova_id}', [ResultadosController::class, 'show']);
});

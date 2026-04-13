<?php

use App\Modules\Treino\Http\Controllers\TreinoController;
use Illuminate\Support\Facades\Route;

Route::prefix('treino')->group(function (): void {
    Route::get('/disciplinas', [TreinoController::class, 'disciplinas']);
    Route::get('/questao-aleatoria', [TreinoController::class, 'questaoAleatoria']);
    Route::post('/responder', [TreinoController::class, 'responder']);
});

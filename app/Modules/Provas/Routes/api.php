<?php

use App\Modules\Provas\Http\Controllers\ProvasController;
use Illuminate\Support\Facades\Route;

Route::prefix('provas')->group(function (): void {
    Route::get('/', [ProvasController::class, 'index']);
    Route::get('/{id}', [ProvasController::class, 'show']);
    Route::post('/{id}/iniciar', [ProvasController::class, 'iniciar']);
    Route::get('/{id}/questoes', [ProvasController::class, 'questoes']);
    Route::put('/{id}/questoes/{questao}/resposta', [ProvasController::class, 'definirResposta']);
    Route::post('/{id}/finalizar', [ProvasController::class, 'finalizar']);
});

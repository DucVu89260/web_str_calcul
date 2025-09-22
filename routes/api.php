<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\DebugAnalysisController;

Route::prefix('analysis')->group(function () {
    Route::post('connect', [AnalysisController::class, 'connect']);
    Route::get('loads', [AnalysisController::class, 'loads']);
    Route::get('selection', [AnalysisController::class, 'selection']);
    Route::get('results', [AnalysisController::class, 'results']);
    Route::post('run', [AnalysisController::class, 'run']);

    // Kết quả lưu DB
    Route::get('{model_id}/results', [ResultController::class, 'index']);
    Route::get('results/{result}', [ResultController::class, 'show']);
    Route::post('results', [ResultController::class, 'store']);
    Route::delete('results/{result}', [ResultController::class, 'destroy']);
});

// Debug
Route::post('debug/analysis', [DebugAnalysisController::class, 'run']);
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StrcModelController;
use App\Http\Controllers\LoadCaseController;
use App\Http\Controllers\LoadCombinationController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\SteelDesignController;
use App\Http\Controllers\PreliminaryController;

use App\Http\Controllers\SiteParameterController;
use App\Http\Controllers\SiteWindParameterController;
use App\Http\Controllers\SiteSeismicParameterController;

use App\Http\Controllers\FireResistanceController;

use App\Http\Controllers\SectionController;

use App\Http\Controllers\WindLoadController;

use App\Http\Controllers\ConnectionController;

Route::get('/', fn() => redirect()->route('analysis.index'));

Route::get('/load-combinations', [LoadCombinationController::class, 'index'])->name('load_combinations.index');
Route::post('/load-combinations/generate', [LoadCombinationController::class, 'generate'])->name('load_combinations.generate');
Route::get('/load-combinations/export', [LoadCombinationController::class, 'export'])->name('load_combinations.export');
Route::post('/load-combinations/push', [LoadCombinationController::class, 'pushToSap'])->name('load_combinations.push');

Route::prefix('analysis')->group(function(){
    Route::get('/', [AnalysisController::class,'index'])->name('analysis.index'); // dashboard
});

Route::get('/steel-design', [SteelDesignController::class, 'index'])->name('steel_design.index');
Route::post('/steel-design/run', [SteelDesignController::class, 'run'])->name('steel_design.run');

Route::get('/debug/run', function () {
    return view('api_test.debug'); 
});

Route::prefix('projects/{project}')->group(function () {
    Route::get('preliminary', [PreliminaryController::class, 'index'])->name('preliminary.index');
    
    Route::get('preliminary/create', [PreliminaryController::class, 'create'])->name('preliminary.create');
    
    Route::post('preliminary/store', [PreliminaryController::class, 'store'])->name('preliminary.store');
    
    Route::get('preliminary/show', [PreliminaryController::class, 'show'])->name('preliminary.show');
    
    Route::get('preliminary/edit', [PreliminaryController::class, 'edit'])->name('preliminary.edit');
    
    Route::put('preliminary/update', [PreliminaryController::class, 'update'])->name('preliminary.update');
    
    Route::delete('preliminary/destroy', [PreliminaryController::class, 'destroy'])->name('preliminary.destroy');
});


Route::prefix('sites')->group(function () {
    Route::resource('parameters', SiteParameterController::class);
    Route::resource('wind', SiteWindParameterController::class);
    Route::resource('seismic', SiteSeismicParameterController::class);
});


Route::prefix('fire')->group(function () {
    Route::get('/', [FireResistanceController::class, 'index'])->name('fire_resistance.index');
});

Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');

Route::prefix('windload')->group(function () {
    Route::get('/tcvn-2737-2023', [WindLoadController::class, 'tcvn27372023'])
        ->name('windload.tcvn27372023');
    Route::get('/asce-7-10', [WindLoadController::class, 'asce710'])
        ->name('windload.asce710');
});

Route::prefix('connection')->group(function () {
    Route::get('/tcvn-2737-2023', [WindLoadController::class, 'tcvn27372023'])
        ->name('windload.tcvn27372023');
    Route::get('/asce-7-10', [WindLoadController::class, 'asce710'])
        ->name('windload.asce710');
});

Route::prefix('connections')->name('connections.')->group(function () {
    // Trang danh sách
    Route::get('/', [ConnectionController::class, 'index'])->name('index');

    // Form nhập dữ liệu
    Route::get('/{type}/{standard}', [ConnectionController::class, 'create'])
        ->where(['type' => '[a-z]+', 'standard' => '[a-z0-9\-]+'])
        ->name('create');

    // Xử lý tính toán
    Route::post('/{type}/{standard}', [ConnectionController::class, 'calculate'])
        ->where(['type' => '[a-z]+', 'standard' => '[a-z0-9\-]+'])
        ->name('calculate');
});
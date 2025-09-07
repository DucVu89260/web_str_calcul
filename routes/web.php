<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StrcModelController;
use App\Http\Controllers\LoadCaseController;
use App\Http\Controllers\LoadCombinationController;

Route::get('/', fn() => redirect()->route('projects.index'));

Route::resources([
   'projects' => ProjectController::class,
   'models' => StrcModelController::class,
   'load-cases' => LoadCaseController::class,
   'load-combinations' => LoadCombinationController::class,
]);


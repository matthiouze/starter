<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::name('toto.')
    ->prefix('toto')
    ->group(function () {
        Route::get('', [\App\Http\Controllers\TotoController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\TotoController::class, 'create'])->name('create');
        Route::post('', [\App\Http\Controllers\TotoController::class, 'store'])->name('store');
        Route::get('{model}', [\App\Http\Controllers\TotoController::class, 'show'])->name('show');
        Route::get('{model}/edit', [\App\Http\Controllers\TotoController::class, 'edit'])->name('edit');
        Route::put('{model}', [\App\Http\Controllers\TotoController::class, 'update'])->name('update');
        Route::delete('{model}', [\App\Http\Controllers\TotoController::class, 'delete'])->name('delete');
    });

Route::name('toto.')
    ->prefix('toto')
    ->group(function () {
        Route::get('', [\App\Http\Controllers\TotoController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\TotoController::class, 'create'])->name('create');
        Route::post('', [\App\Http\Controllers\TotoController::class, 'store'])->name('store');
        Route::get('{model}', [\App\Http\Controllers\TotoController::class, 'show'])->name('show');
        Route::get('{model}/edit', [\App\Http\Controllers\TotoController::class, 'edit'])->name('edit');
        Route::put('{model}', [\App\Http\Controllers\TotoController::class, 'update'])->name('update');
        Route::delete('{model}', [\App\Http\Controllers\TotoController::class, 'delete'])->name('delete');
    });

Route::name('toto.')
    ->prefix('toto')
    ->group(function () {
        Route::get('', [\App\Http\Controllers\TotoController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\TotoController::class, 'create'])->name('create');
        Route::post('', [\App\Http\Controllers\TotoController::class, 'store'])->name('store');
        Route::get('{model}', [\App\Http\Controllers\TotoController::class, 'show'])->name('show');
        Route::get('{model}/edit', [\App\Http\Controllers\TotoController::class, 'edit'])->name('edit');
        Route::put('{model}', [\App\Http\Controllers\TotoController::class, 'update'])->name('update');
        Route::delete('{model}', [\App\Http\Controllers\TotoController::class, 'delete'])->name('delete');
    });

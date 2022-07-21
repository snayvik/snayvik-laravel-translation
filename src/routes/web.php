<?php

use Illuminate\Support\Facades\Route;
use Snayvik\Translation\Controllers\TranslationController;

Route::prefix(config('SnayvikTranslation.route_prefix'))->middleware(config('SnayvikTranslation.route_middleware'))->group(function(){
    
    Route::get('/', [TranslationController::class,'index'])->name('translations');
    Route::post('/import-in-db', [TranslationController::class,'importInDb'])->name('translations.importInDB');
    Route::get('/view/{group}', [TranslationController::class,'showGroup'])->name('translations.show_group');
    Route::post('/store', [TranslationController::class,'store'])->name('translations.store');
    Route::post('/import-in-files', [TranslationController::class,'importInFiles'])->name('translations.importInFiles');

    Route::post('/locale/store', [TranslationController::class,'localeStore'])->name('translations.locale.store');
    Route::get('/locale/delete/{locale}', [TranslationController::class,'localeDelete'])->name('translations.locale.delete');
    
    Route::get('/delete/{group}/{key}', [TranslationController::class,'deleteTranslation'])->name('translations.delete');
});
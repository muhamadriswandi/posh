<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WelcomeController;

Route::get('/', WelcomeController::class)->name('home');
Route::get('/download-report/{bank}', [WelcomeController::class, 'downloadCsv'])->name('report.download');

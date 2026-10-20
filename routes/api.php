<?php

use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/portfolio', PortfolioController::class)->name('api.portfolio');

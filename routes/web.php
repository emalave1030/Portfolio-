<?php

use App\Support\PortfolioPayload;
use Illuminate\Support\Facades\Route;

Route::get('/', function (PortfolioPayload $payload) {
    // The document title and meta description come from the same cached
    // payload the page renders, so they can never drift from the content.
    return view('app', ['profile' => $payload->cached()['profile']]);
})->name('home');

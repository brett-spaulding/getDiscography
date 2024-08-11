<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.main');
});

Route::get('/artist/{artist}', [SearchController::class, 'search_artist'])->name('api.search.artist');

Route::get('api/artists/', [ApiController::class, 'get_artists'])->name('api.artist');

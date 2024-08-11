<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.main');
});

Route::get('/artist/{artist}', [ApiController::class, 'search_artist'])->name('api.search.artist');

// Get all artists
Route::get('api/artists/', [ApiController::class, 'get_artists'])->name('api.artist');

// Queue single artist
Route::get('api/queue/artist/{id}', [ApiController::class, 'queue_artist'])->name('api.artist.queue');

<?php

use \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\DisableCsrf;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.main');
});
//  User submitted input to scrape for artist data
Route::get('/artist/{artist}', [ApiController::class, 'search_artist'])->name('api.search.artist');
//  Return list of all artists in database
Route::get('api/artists/', [ApiController::class, 'get_artists'])->name('api.artist');
//  Add artist to queue
Route::get('api/queue/artist/{id}', [ApiController::class, 'queue_artist'])->name('api.artist.queue');
//  Returns a single album that is ready for download
Route::get('api/album/queue', [ApiController::class, 'queue_waiting'])->name('api.queue.waiting');
// Update the queue from handler
Route::post('api/album/queue/update/{id}', [ApiController::class, 'queue_update'])->name('api.queue.update')->withoutMiddleware(VerifyCsrfToken::class);
// Prompt the artist queue remotely
Route::get('api/queue/artists/run', [ApiController::class, 'queue_artist_run'])->name('api.queue.run');
// Client side queue data
Route::get('/api/queue/albums', [ApiController::class, 'get_album_queue'])->name('api.queue.albums');

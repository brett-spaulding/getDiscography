<?php

namespace App\Http\Controllers;

use App\Jobs\RunArtistQueue;
use App\Models\AlbumQueue;
use App\Models\Artist;
use App\Models\WebDriver;
use App\Models\WebScraper;
use Illuminate\Http\Request;
use App\Models\ArtistQueue;

class ApiController extends Controller
{
    public function get_artists(Request $request)
    {

        $artists = Artist::all();
        $data = array();

        foreach ($artists as $artist) {
            $data[] = [
                'id' => $artist->id,
                'name' => $artist->name,
                'url_remote' => $artist->url_remote,
                'state' => $artist->state,
                'thumbnail' => $artist->thumbnail,
            ];
        }
        $response = json_encode(array('data' => $data));
        return $response;
    }

    public function get_album_queue()
    {
        $album_queue = AlbumQueue::where('state', '!=', 'done')->get();
        $response = array();
        foreach ($album_queue as $queue) {
            $album = $queue->album;
            $artist = $album->artist;
            if ($album && $artist) {
                $response[] = [
                    'name' => $album->name,
                    'artist_id' => $artist->toArray(),
                    'url_remote' => $album->url_remote,
                    'thumbnail' => $album->thumbnail,
                    'image' => $album->image,
                    'state' => $queue->state,
                ];
            }
        }
        return json_encode($response);
    }

    public function queue_artist($id, ArtistQueue $artistQueue): bool
    {
        return $artistQueue->enqueue($id);
    }

    public function queue_artist_run()
    {
        ArtistQueue::run_queue();
    }

    public function search_artist(string $artist)
    {
        $url = 'https://music.youtube.com/search?q=' . str_replace(' ', '+', $artist);
        $driver = WebDriver::setUp();
        $driver->get($url);

        // Add handling for no artist button; Some artists searches don't have this option (Ex The Black Dahlia Murder)
        try {
            $response = WebScraper::scrapeArtists($driver);
        } catch (\Exception) {
            \Log::warning('Could not get list of artists, attempting to get single artist card..');
            $response = WebScraper::scrapeArtist($driver);
        } finally {
            $driver->quit();
        }
        return response()->json($response);
    }

    public function queue_waiting()
    {
        $data = array('queue' => false);
        $queue = AlbumQueue::where('state', 'pending')->first();
        if (!is_null($queue)) {
            $album = $queue->album;
            $artist = $album->artist;
            $queue->state = 'in_progress';
            $queue->save();
            $data = array('queue' => $queue->toArray(), 'album' => $album->toArray(), 'artist' => $artist->toArray());

        }
        return json_encode($data);
    }

    public function queue_update(Request $request, $id)
    {
        $queue = AlbumQueue::where('id', $id)->first();
        $album = $queue->album;
        $artist = $album->artist;

        if ($queue->exists()) {

            if (isset($request['album']) || isset($request['artist'])) {
                $album_local_url = $request['album']['url_local'] ?? '';
                $artist_local_url = $request['artist']['url_local'] ?? '';

                if ($album_local_url || $artist_local_url) {
                    if ($artist_local_url && is_string($artist_local_url)) {
                        $artist->url_local = $artist_local_url;
                        $artist->save();
                    }
                    if ($album_local_url && is_string($album_local_url)) {
                        $album->url_local = $album_local_url;
                        $album->save();
                    }
                    $queue->state = 'done';
                    $queue->save();
                }

            }
        }

    }

}

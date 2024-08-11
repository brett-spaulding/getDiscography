<?php

namespace App\Http\Controllers;

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

    public function queue_artist($id, ArtistQueue $artistQueue): bool
    {
        return $artistQueue->enqueue($id);
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

}

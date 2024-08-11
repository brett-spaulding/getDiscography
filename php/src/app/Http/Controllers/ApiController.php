<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

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

        \Log::info('=======================');
        $response = json_encode( array('data' => $data));
        \Log::info($response);
        return $response;
    }
}

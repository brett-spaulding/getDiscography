<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;

class ArtistQueue extends Model
{
    use HasFactory;

    public function enqueue($id): bool
    {
        $result = false;
        $artist_id = Artist::findById($id)->first();
        // Artists that are 'done' can be run through the queue again.  Prevent in progress, though.
        if ($artist_id->count() > 0 && $artist_id->state !== 'in_progress') {
            $this->artist_id = $artist_id->id;
            $this->save();
            $artist_id->change_state('in_progress');
            $result = true;
        }
        return $result;
    }


    public function process_artist()
    {
        // Scrape the artist page for image, and album data (image, url, name)
        $driver = WebDriver::setUp();
        $artist_id = Artist::where('id', $this->artist_id)->get();
        if ($artist_id->count() > 0) {

        } else {
            throw new Exception('The Artist ID provided to the queue does not exist.');
        }
    }


}

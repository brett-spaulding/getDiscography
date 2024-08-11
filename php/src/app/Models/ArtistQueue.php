<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistQueue extends Model
{
    use HasFactory;

    public function enqueue($artist_id)
    {
        $this->artist_id = $artist_id;
        $this->save();
    }


    public function process_queue()
    {
        // Scrape the artist page for image, and album data (image, url, name)
    }


}

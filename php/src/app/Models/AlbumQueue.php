<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlbumQueue extends Model
{
    use HasFactory;

    public function enqueue($album_id): bool
    {
        $result = false;
        $album_queued = AlbumQueue::where('album_id', $album_id->id)->first();
        if (is_null($album_queued) && $album_id->state === 'pending') {
            $this->album_id = $album_id->id;
            $this->save();
            $result = true;
        }
        return $result;
    }

    public function process_album()
    {
        // Either python pings to process the queue or laravel will send the data to python for processing
    }
}

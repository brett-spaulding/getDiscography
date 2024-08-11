<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlbumQueue extends Model
{
    use HasFactory;

    public function enqueue(int $id): bool
    {
        $result = false;
        $album_id = Album::findById($id)->first();
        if ($album_id->count() > 0 && $album_id->state === 'pending') {
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

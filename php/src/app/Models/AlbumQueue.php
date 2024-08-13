<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlbumQueue extends Model
{
    use HasFactory;

    public function enqueue($album): bool
    {
        $result = false;
        $album_queued = AlbumQueue::where('album_id', $album->id)->first();
        if (is_null($album_queued) && $album->state === 'pending') {
            $this->album_id = $album->id;
            $this->save();
            $result = true;
        }
        return $result;
    }

    public static function addQueue($album_id): bool
    {
        $queue = new AlbumQueue();
        $queue->enqueue($album_id);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

}

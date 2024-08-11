<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlbumQueue extends Model
{
    use HasFactory;

    public function enqueue()
    {
        // Add albums to queue for download
    }

    public function process_queue()
    {
        // Either python pings to process the queue or laravel will send the data to python for processing
    }
}

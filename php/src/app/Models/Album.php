<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;

class Album extends Model
{
    use HasFactory;

    public function change_state(string $state)
    {
        $available_states = array("pending", "in_progress", "done");
        if (!in_array($state, $available_states)){
            throw new Exception('Invalid state');
        }
        $this->state = $state;
        $this->save();
    }

    public static function findByName($name)
    {
        return self::where('name', '=', $name)->get();
    }

    public static function findById($id)
    {
        return self::where('id', '=', $id)->get();
    }

    public static function addAlbum(string $name, string $thumbnail, string $url_remote, string $image, $artist_id)
    {
        $album = new Album();
        $album->name = $name;
        $album->artist_id = $artist_id;
        $album->url_remote = $url_remote;
        $album->thumbnail = $thumbnail;
        $album->image = $image;
        $album->save();
        return $album;
    }

    public static function findOrCreateByName(string $name, array $data = [])
    {
        $album = self::findByName($name)->first();
        if (!$album && $data) {
            $album = self::addAlbum($data['name'], $data['thumbnail'], $data['url_remote'], $data['image'], $data['artist_id']);
        }
        return $album;
    }

}

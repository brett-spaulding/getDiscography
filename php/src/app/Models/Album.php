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

    public static function findByArtistTitle(Artist $artist, string $name)
    {
        return self::where('name', '=', $name)->where('artist_id', '=', $artist->id)->first();
    }

    public static function findById(int $id)
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

    public static function findOrCreateByName($artist_id, string $name, array $data = [])
    {
        $album = self::findByArtistTitle($artist_id, $name);
        if ($album->exists() && $data) {
            $album = self::addAlbum($data['name'], $data['thumbnail'], $data['url_remote'], $data['image'], $data['artist_id']);
        }
        return $album;
    }

}

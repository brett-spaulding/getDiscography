<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;

class Artist extends Model
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

    public function getArtistImageLocation()
    {
        return str_replace('/var/www/html', '', $this->image);
    }

    public static function addArtist(string $name, string $thumbnail, string $url_remote, string $image)
    {
        $artist = new Artist();
        $artist->name = str_replace('/', '-', $name);
        $artist->url_remote = $url_remote;
        $artist->thumbnail = $thumbnail;
        $artist->image = $image;
        $artist->save();
        return $artist;
    }

    public static function findOrCreateByName(string $name, array $data = [])
    {
        $artist = self::findByName($name)->first();
        if (!$artist && $data) {
            $artist = self::addArtist($data['name'], $data['thumbnail'], $data['url_remote'], $data['image']);
        }
        return $artist;
    }

    public function read(array $fields = []): array
    {
        // TODO: Add filter for fields if provided
        return $this->toArray();;
    }

}

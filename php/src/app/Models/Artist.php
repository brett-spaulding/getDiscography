<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    public static function findByName($name)
    {
        return self::where('name', '=', $name)->get();
    }

    public static function addArtist(string $name, string $thumbnail, string $url_remote)
    {
        $artist = new Artist();
        $artist->name = $name;
        $artist->url_remote = $url_remote;
        $artist->thumbnail = $thumbnail;
        $artist->save();
        return $artist;
    }

    // TODO: discuss why I can't return Artist or Collection, because it failes either way but says its returning Artist
    public static function findOrCreateByName(string $name, array $data = [])
    {
        $artist = self::findByName($name)->first();
        if (!$artist && $data) {
            $artist = self::addArtist($data['name'], $data['thumbnail'], $data['url_remote']);
        }
        return $artist;
    }

    public function read(array $fields = []): array
    {
        $data = $this->toArray();
        // Filter
        if ($fields) {
            $data = array_intersect_key($data, array_flip(array_keys($fields)));
        }
        return $data;
    }

}

<?php

namespace App\Console\Commands;

use App\Models\Album;
use App\Utils\ImageUrl;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class UpdateAlbumImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-album-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $albums = Album::all();
        $bar = new ProgressBar($this->output, count($albums));
        $bar->start();
        foreach ($albums as $album) {
            $image_url = ImageUrl::modifyGoogleImageUrl($album->thumbnail);
            $image_file = ImageUrl::save_img_url($image_url, 'album');
            echo '-------------------';
            echo $image_url . '\n';
            echo $image_file . '\n';
            echo '-------------------';
            $album->image = $image_file;
            $album->save();
            $bar->advance();
        }
    }
}

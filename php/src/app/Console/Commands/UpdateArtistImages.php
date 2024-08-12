<?php

namespace App\Console\Commands;

use App\Models\Artist;
use Illuminate\Console\Command;
use App\Utils\ImageUrl;
use Symfony\Component\Console\Helper\ProgressBar;

class UpdateArtistImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-artist-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update artist images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $artists = Artist::all();
        $bar = new ProgressBar($this->output, count($artists));
        $bar->start();
        foreach ($artists as $artist) {
            $image_url = ImageUrl::modifyGoogleImageUrl($artist->thumbnail);
            $image_file = ImageUrl::save_img_url($image_url, 'artist');
            $artist->image = $image_file;
            $artist->save();
            $bar->advance();
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\ArtistQueue;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class ProcessArtistQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-artist-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs queue responsible for scraping artist pages for album data, then queuing the albums';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // This queue will prompt the scraping of all artist albums, mark done when complete
        $artist_queue = ArtistQueue::where('state', 'pending')->get();
        $bar = new ProgressBar($this->output, count($artist_queue));
        $bar->start();
        foreach ($artist_queue as $artist) {
            $artist->state = 'in_progress';
            $artist->save();
            $artist->process_artist();
            $artist->state = 'done';
            $artist->save();
            $bar->advance();
        }
    }
}

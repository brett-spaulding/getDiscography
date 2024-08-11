<?php

namespace App\Console\Commands;

use App\Models\ArtistQueue;
use Illuminate\Console\Command;

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
        $records = ArtistQueue::where('state', 'in_progress')->get();
        foreach ($records as $record) {

        }
    }
}

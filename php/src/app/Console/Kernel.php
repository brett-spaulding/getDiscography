<?php

namespace App\Console;

use App\Console\Commands\ProcessArtistQueue;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
      protected $commands = [
        ProcessArtistQueue::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:process-artist-queue')->everyMinute()->withoutOverlapping();
    }
}

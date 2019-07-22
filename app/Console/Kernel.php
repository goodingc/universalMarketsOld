<?php

namespace App\Console;

use App\Console\Commands\Scripts;
use App\Console\Commands\StockFeed;
use App\Console\Commands\StockTest;
use App\Console\Commands\UpdateStockLevels;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        StockFeed::class,
        UpdateStockLevels::class,
        StockTest::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command("stock:feed UNN42")->daily();
        $schedule->command("stock:feed L4R3I")->daily();
        $schedule->command("stock:feed 5C8M0")->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

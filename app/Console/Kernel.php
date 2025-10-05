<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Cleanup SK temporary files setiap hari pada jam 2 pagi
        $schedule->command('sk:cleanup-temporary --hours=24')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Cleanup Mutasi nota temporary files setiap hari pada jam 2:30 pagi
        $schedule->command('mutasi:cleanup-temporary-files')
                 ->dailyAt('02:30')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Backup karyawan data setiap awal bulan pada jam 1 pagi
        // Backup dilakukan untuk bulan sebelumnya
        $schedule->command('backup:karyawan')
                 ->monthlyOn(1, '01:00')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/backup.log'));
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

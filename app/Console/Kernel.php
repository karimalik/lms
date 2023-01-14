<?php

namespace App\Console;

use App\Console\Commands\BackupDatabaseCommand;
use App\Console\Commands\BackupFileCommand;
use App\Console\Commands\InstructorPayout;
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
        BackupDatabaseCommand::class,
        BackupFileCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('backup:backup_file')->dailyAt('01:30');
        $schedule->command('backup:clean')->dailyAt('01:30');
        $schedule->command('backup:run --only-db')->dailyAt('01:35');


        if (isModuleActive('Subscription')) {
            $schedule->command('check:subscription');
            $schedule->command('apply:commission');
        }

        if (isModuleActive('OrgSubscription')) {
            $schedule->command('alert:orgSubscription')->everyMinute();
//            $schedule->command('alert:orgSubscription')->dailyAt('00:00');
        }

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

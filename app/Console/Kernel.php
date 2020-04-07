<?php

namespace Acelle\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Acelle\Model\Automation;
use Acelle\Model\Setting;
use Laravel\Tinker\Console\TinkerCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        Commands\TestCampaign::class,
        Commands\UpgradeTranslation::class,
        Commands\RunHandler::class,
        Commands\ImportList::class,
        Commands\VerifySender::class,
        Commands\SystemCleanup::class,
        Commands\GeoIpCheck::class,
        TinkerCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        // Log last execution time
        // Move the event into a schedule::call to prevent it from triggering every time "php artisan" command is executed
        $schedule->call(function () {
            event(new \Acelle\Events\CronJobExecuted());
        })->name('cronjob_event:log')->everyMinute();
        
        // Automation
        $schedule->call(function () {
            Automation::run();
        })->name('automation:run')->everyFiveMinutes();

        // Bounce/feedback handler
        $schedule->command('handler:run')->everyFiveMinutes();

        // Queued import/export/campaign
        $schedule->command('queue:work --once --tries=3')->everyMinute();
        
        // Sender verifying
        $schedule->command('sender:verify')->everyFiveMinutes();
        
        // System clean up
        $schedule->command('system:cleanup')->daily();
        
        // GeoIp database check
        $schedule->command('geoip:check')->everyMinute()->withoutOverlapping();
    }
    
    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}

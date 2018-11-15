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
        Commands\Openexchangerates\Sync::class,
        Commands\Reservations\Room\ScheduleReminder::class,
        Commands\Subscriptions\Complimentary::class,
        Commands\Subscriptions\Invoice::class,
        Commands\Subscriptions\Payment::class,
        Commands\Broadcasts\Activity::class,
        Commands\Clear\Group::class,
        Commands\Database\NotificationSettingActivation::class,
        Commands\Database\DataMigrateFromJobToBusinessOpportunity::class,
        Commands\Recommendations\Job::class,
        Commands\Recommendations\BusinessOpportunity::class

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->command('openexchangerates:sync')->dailyAt('16:00')->withoutOverlapping();
        $schedule->command('reservations:room:schedule-reminder')->everyMinute()->withoutOverlapping();
        $schedule->command('subscriptions:reset-complimentary')->dailyAt('16:00')->between('16:00', '18:00')->everyMinute()->withoutOverlapping();
        
        if(config('features.subscription.invoice')) {
        	
	        $schedule->command('subscriptions:generate-invoice')->dailyAt('17:00')->between('17:00', '19:00')->everyFiveMinutes()->withoutOverlapping();
	        $schedule->command('subscriptions:pay-invoice')->dailyAt('20:00')->between('20:00', '23:00')->everyTenMinutes()->withoutOverlapping();
	        
        }

        //$schedule->command('recommendations:job')->hourly()->withoutOverlapping();

        $schedule->command('recommendations:business-opportunity')->cron('*/15 * * * *')->withoutOverlapping();

        $schedule->command('broadcasts:activity')->everyMinute()->withoutOverlapping();
        $schedule->command('clear:group')->dailyAt('16:00')->everyFiveMinutes()->withoutOverlapping();

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

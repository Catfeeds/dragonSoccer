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
        // $schedule->command('inspire')
        //          ->hourly();

        //组队 over 1108
        /*$schedule->call('App\Http\Controllers\Shell\TeamController@maybeMatch')->everyMinute();
        $schedule->call('App\Http\Controllers\Shell\TeamController@maybeMatchArea')->everyMinute();
        $schedule->call('App\Http\Controllers\Shell\TeamController@maybeMatchPosition')->everyMinute();
        $schedule->call('App\Http\Controllers\Shell\TeamController@maketeam')->everyMinute();
        $schedule->call('App\Http\Controllers\Shell\TeamController@makeMatchOver')->everyMinute();
        $schedule->call('App\Http\Controllers\Shell\TeamController@makeMatchFailed')->everyMinute();*/
        
        //奖金池
        $schedule->call('App\Http\Controllers\Shell\CashController@applyToCash')->everyMinute();
        $schedule->call('App\Http\Controllers\Shell\CashController@cashToApply')->everyMinute();

        //赛程安排 1108
        /*$schedule->call('App\Http\Controllers\Shell\MatchlogController@makelog')->everyMinute();
        $schedule->call('App\Http\Controllers\Shell\MatchlogController@makeMatchLog')->everyMinute();*/

        //注册送现金
        //$schedule->call('App\Http\Controllers\Shell\MoneyController@applyToMoney')->everyMinute();

        //组队2.0
        $schedule->call('App\Http\Controllers\Shell\GteamController@maketeamfor7')->everyMinute();
        $schedule->call('App\Http\Controllers\Shell\GteamController@maketeam')->everyMinute();

        //同步数据
        $schedule->call('App\Http\Controllers\Shell\GroupController@makeGroupData')->everyMinute();
        
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

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
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\IssueOrder::class,
        \App\Console\Commands\IssueComsume::class,
        \App\Console\Commands\CheckBillMerchant2Usercenter::class,
        \App\Console\Commands\CheckBillGetHeepayBill::class,
        \App\Console\Commands\CheckBillUsercenter2Channel::class,
        \App\Console\Commands\CheckBillSettlement::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
    }
}

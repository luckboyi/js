<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		\App\Console\Commands\Inspire::class,
		\App\Console\Commands\SyncRecommend::class,
		\App\Console\Commands\DataCenterStat::class,
		\App\Console\Commands\DataAddSpell::class,
		\App\Console\Commands\DbToSolr::class,
		\App\Console\Commands\SolrCommand::class,
		\App\Console\Commands\updateMerchantPlay::class,
		\App\Console\Commands\Stat2bBill::class,
		\App\Console\Commands\Stat2bUserData::class,
		\App\Console\Commands\StatPlayTimes::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule) {
		$schedule->command('inspire')
			->hourly();
	}
}

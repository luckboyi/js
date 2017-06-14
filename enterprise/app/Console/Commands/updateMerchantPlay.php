<?php

namespace App\Console\Commands;

use App\Helper\Vredis;
use DB;
use Illuminate\Console\Command;

class updateMerchantPlay extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'db:merchantPlay';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$this->output->progressStart(2);
		$affectedNum = 0;
		$allPlay = Vredis::zrevrange('merchantplay', 'all', 0, -1, true);
		foreach ($allPlay as $appid => $play) {
			$ret = DB::connection("db_webgame")->table('t_webgame')->where(['appid' => $appid])->update(['tob_play' => $play]);
			if ($ret) {
				$affectedNum++;
			}
		}
		$this->output->progressAdvance();
		$merchants = DB::connection("db_2b_store")->table("t_2b_merchant")->select('merchantid')->get();
		foreach ($merchants as $merchant) {
			$id = $merchant['merchantid'];
			$mplay = Vredis::zrevrange('merchantplay', $id, 0, -1, true);
			if ($mplay) {
				foreach ($mplay as $appid => $play) {
					$ret = DB::connection("db_2b_store")->table('t_2b_terminal_games')->where(['merchantid' => $id, 'appid' => $appid])->update(['play' => $play]);
					if ($ret) {
						$affectedNum++;
					}
				}
			}
		}
		$this->output->progressAdvance();
		$this->output->progressFinish();
		$this->info('affected ' . $affectedNum);
	}
}

<?php

namespace App\Console\Commands;

use DB;
use Helper\Library;
use Illuminate\Console\Command;
use Overtrue\Pinyin\Pinyin;

class DataAddSpell extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'laravel:dbspell';

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
		$this->output->progressStart(4);

		$pinyin = new Pinyin();
		$videos = DB::connection("db_dev")->table("t_video")->get();
		foreach ($videos as $key => $value) {
			$spell = strtolower($pinyin->sentence($value['video_name']));
			if (!$spell) {
				$this->error('video ' . $value['video_id']);
				break;
			}
			$spell = substr($spell, 0, 1);
            if(is_numeric($spell)) {
                $spell = Library::num2Pinyin($spell);
                $spell = substr($spell, 0, 1);
            }
			$ret = DB::connection("db_dev")->table("t_video")->where('video_id', $value['video_id'])->update(['video_spell' => $spell]);
		}
		$this->output->progressAdvance();

		$videos = DB::connection("db_operate")->table("t_video")->get();
		foreach ($videos as $key => $value) {
			$spell = strtolower($pinyin->sentence($value['video_name']));
			if (!$spell) {
				$this->error('video ' . $value['video_id']);
				break;
			}
			$spell = substr($spell, 0, 1);
            if(is_numeric($spell)) {
                $spell = Library::num2Pinyin($spell);
                $spell = substr($spell, 0, 1);
            }
			$ret = DB::connection("db_operate")->table("t_video")->where('video_id', $value['video_id'])->update(['video_spell' => $spell]);
		}
		$this->output->progressAdvance();

		$games = DB::connection("db_dev")->table("t_webgame")->get();
		foreach ($games as $key => $value) {
			$spell = strtolower($pinyin->sentence($value['name']));
			if (!$spell) {
				$this->error('game ' . $value['appid']);
				break;
			}
			$spell = substr($spell, 0, 1);
            if(is_numeric($spell)) {
                $spell = Library::num2Pinyin($spell);
                $spell = substr($spell, 0, 1);
            }
			$ret = DB::connection("db_dev")->table("t_webgame")->where('appid', $value['appid'])->update(['spell_name' => $spell]);
		}
		$this->output->progressAdvance();

		$games = DB::connection("db_webgame")->table("t_webgame")->get();
		foreach ($games as $key => $value) {
			$spell = strtolower($pinyin->sentence($value['name']));
			if (!$spell) {
				$this->error('game ' . $value['appid']);
				break;
			}
			$spell = substr($spell, 0, 1);
            if(is_numeric($spell)) {
                $spell = Library::num2Pinyin($spell);
                $spell = substr($spell, 0, 1);
            }
			$ret = DB::connection("db_webgame")->table("t_webgame")->where('appid', $value['appid'])->update(['spell_name' => $spell]);
		}
		$this->output->progressAdvance();
		$this->output->progressFinish();
	}
}

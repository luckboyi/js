<?php

namespace App\Console\Commands;

use Config;
use Helper\HttpRequest;
use Illuminate\Console\Command;

class SolrCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'solr:command {core?} {tp?} {json?}';

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
		$arguments = $this->argument();
		$core = isset($arguments['core']) ? $arguments['core'] : '';
		$tp = isset($arguments['tp']) ? $arguments['tp'] : '';
		$json = isset($arguments['json']) ? $arguments['json'] : '';
		switch ($tp) {
		case "schema":
			$this->schema($core, $json);
			break;
		case "clear":
			$this->clearData($core, $json);
			break;
		case "cleanTop":
		
			$this->cleanStatTop();
		break;
		default:
			$this->comment('useage');
			$this->comment('add field');
			$this->comment('solr:command top schema ' . "'" . '{"add-field":{"stored":"true","indexed":"false","name":"publish_date","type":"int"}}' . "'");
			$this->comment('clear');
			$this->comment('solr:command top clear ' . "'" . '<delete><query>*:*</query></delete>' . "'");
			$this->comment('cleanTop');
			$this->comment('solr:command cleanTop');
			break;
		}
	}
	private function cleanStatTop() {
		$updateJson = '<delete><query>stat:9</query></delete>';
		$cfg = Config::get('server.solr.top');
		$time = time();
		$url = $cfg['url'] . $cfg['core'] . "/update?_=" . $time . "&boost=1.0&commitWithin=1000&overwrite=true&wt=json";

		$headers = array(
			'Content-Type: text/xml',
			'Content-Length: ' . strlen($updateJson),
		);
		$result = HttpRequest::url('post', $url, $headers, $updateJson);
		var_dump($result);
	}
	private function clearData($core, $xml) {
		$updateJson = $xml;

		$cfg = Config::get('server.solr.' . $core);
		$time = time();
		$url = $cfg['url'] . $cfg['core'] . "/update?_=" . $time . "&boost=1.0&commitWithin=1000&overwrite=true&wt=json";

		$headers = array(
			'Content-Type: text/xml',
			'Content-Length: ' . strlen($updateJson),
		);

		$result = HttpRequest::url('post', $url, $headers, $updateJson);
		var_dump($result);
	}

	private function schema($core, $json) {
		$updateJson = $json;

		$cfg = Config::get('server.solr.' . $core);
		$time = time();
		$url = $cfg['url'] . $cfg['core'] . "/schema?_=" . $time . "&wt=json";

		$headers = array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($updateJson),
		);

		$result = HttpRequest::url('post', $url, $headers, $updateJson);
		var_dump($result);
	}
}

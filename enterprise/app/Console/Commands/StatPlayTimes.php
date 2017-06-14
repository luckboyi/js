<?php
/**
 * 统计启动次数
 * 每日0点跑
 */

namespace App\Console\Commands;

use Helper\Library;
use App\Models\ToBStoreModel;
use Illuminate\Console\Command;

class StatPlayTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stat:playTimes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计启动次数';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $num = Library::osProcessNum($this->signature);
        if($num > 1) {
            return false;
        }

        echo date("Y-m-d H:i:s")." start\n";

        $toBStoreModel = new ToBStoreModel;
        $ret = $toBStoreModel->statPlayTimes2DB();
        echo date("Y-m-d H:i:s")." end\n";
    }
}

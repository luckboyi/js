<?php
/**
 *结算
 * 每天凌晨4点开始跑，每小时跑一次
 */

namespace App\Console\Commands;

use Helper\Library;
use App\Models\ToBCheckBillModel;
use Illuminate\Console\Command;

class CheckBillSettlement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckBill:Settlement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '结算';

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

        $h = date("G");
        if($h < 4 || $h > 12) {
            return false;
        }
        echo date("Y-m-d H:i:s")." start\n";

        $nowstamp = time();
        $toBCheckBillModel = new ToBCheckBillModel;
        $ret = $toBCheckBillModel->settlement($nowstamp);
        if(!$ret) {
            // 报警
        }
        echo date("Y-m-d H:i:s")." end\n";
    }
}

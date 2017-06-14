<?php
/**
 * 商家与usercenter对账
 * 每天凌晨1点开始跑，每小时跑一次
 */

namespace App\Console\Commands;

use Helper\Library;
use App\Models\ToBCheckBillModel;
use Illuminate\Console\Command;

class CheckBillMerchant2Usercenter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckBill:Merchant2Usercenter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '商家与usercenter对账';

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
        if($h < 1 || $h > 12) {
            return false;
        }
        echo date("Y-m-d H:i:s")." start\n";

        $nowstamp = time();
        $toBCheckBillModel = new ToBCheckBillModel;
        $ret = $toBCheckBillModel->check2BBill($nowstamp);
        if(!$ret) {
            // 报警
        }
        echo date("Y-m-d H:i:s")." end\n";
    }
}

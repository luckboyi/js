<?php
/**
 * 统计2b商户月账单
 * 每月1号凌晨跑
 */

namespace App\Console\Commands;

use Helper\Library;
use App\Models\ToBDBModel;
use Illuminate\Console\Command;

class Stat2bBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stat:2bBill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计2b商户月账单';

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

        $start = date("Ym", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
        $start .= "00";
        $end = date("Ym");
        $end .= "00";

        $m = date("Ym", mktime(0, 0, 0, date("m") - 1));
        $toBDBModel = new ToBDBModel;
        $row = $toBDBModel->statMonthBill($start, $end);
        $merchants = $toBDBModel->getMerchatids();
        if(!$merchants || !is_array($merchants)) {
            echo "no merchant\n";
            return false;
        }
        if(!is_array($row)) {
            echo "no day bill\n";
            return false;
        }
        for($i = 0; $i < count($merchants); $i++) {
            $merchantid = $merchants[$i]['merchantid'];
            if(isset($row[$merchantid])) {
                $info = ['total_amount' => $row[$merchantid]['total_amount'], 'pay_amount' => $row[$merchantid]['pay_amount'], 'net_income' => $row[$merchantid]['net_income'], 'type' => 1];
            }else {
                $info = ['total_amount' => 0, 'pay_amount' => 0, 'net_income' => 0, 'type' => 1];
            }
            $ret = $toBDBModel->addOneDayBill($merchantid, $m, $info);
        }
        echo date("Y-m-d H:i:s")." end\n";
    }
}

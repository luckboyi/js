<?php
/**
 * 统计2b商户用户行为数据
 * 每月1号凌晨跑
 */

namespace App\Console\Commands;

use Helper\Library;
use App\Models\ToBDBModel;
use Illuminate\Console\Command;

class Stat2bUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stat:userData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计2b商户用户行为数据';

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
        $row = $toBDBModel->statMonthData($start, $end);

        if(!is_array($row)) {
            echo "no day bill\n";
            return false;
        }
        for($i = 0; $i < count($row); $i++) {
            $ret = $toBDBModel->setMonthAppStat($row[$i]['merchantid'], $m, $row[$i]['appid'], $row[$i]['start'], $row[$i]['playlong']);
        }
        echo date("Y-m-d H:i:s")." end\n";
    }
}

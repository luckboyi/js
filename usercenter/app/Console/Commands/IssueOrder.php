<?php
/**
 * 补单脚本
 */

namespace App\Console\Commands;

use Helper\Library;
use App\Models\PayModel;
use Illuminate\Console\Command;

class IssueOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Issue:Order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '充值补单';

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
        $nowstamp = time();
        $yestoday = $nowstamp - 86400;

        $this->retryOrder($yestoday);
        $this->retryOrder($nowstamp);
        echo date("Y-m-d H:i:s")." end\n";
    }

    /**
     *
     */
    public function retryOrder($stamp)
    {
        $payModel = new PayModel;
        $payModel->retryOrder($stamp);
    }
}

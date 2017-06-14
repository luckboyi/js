<?php

namespace App\Console\Commands;

use App\Models\OperateModel;
use App\Models\RecommendModel;
use Illuminate\Console\Command;

class SyncRecommend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:recommend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync recommend db to cache';

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
        // $operateModel = new OperateModel;
        // $posids = $operateModel->getSchedulePosidsByTime();
        // if(!$posids || !is_array($posids)) {
        //     return false;
        // }
        // $recommendModel = new RecommendModel;
        // for($i = 0; $i < count($posids); $i++) {
        //     $posid = isset($posids[$i]['posid']) ? $posids[$i]['posid'] : 0;
        //     if(!$posid) {
        //         continue;
        //     }
        //     $ret = $recommendModel->publish($posid);
        // }

    }
}

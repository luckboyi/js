<?php
/**
 * 发送统计日志给数据中心
 */

namespace App\Console\Commands;

use App\Helper\Vredis;
use Helper\UdpLog;
use Helper\Library;
use \App\Models\DataCenterStatModel;
use Illuminate\Console\Command;

class DataCenterStat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DataCenter:sendStat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送行为统计日志';

    /**
     * 脚本运行最大数量
     *
     * @var int
     */
    protected $maxrunnum = 3;

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
        if($num > $this->maxrunnum) {
            return false;
        }
        $this->sendStat();
    }

    /**
     *
     */
    public function sendStat()
    {
        while(true)
        {
            try{
                $val = Vredis::blpop("datacenterstat", "", 50);
            } catch (\Exception $e) {
                $val = null;
            }
            if(!$val) {
                break;
            }

            if(!isset($val[0])) {
                continue;
            }
            $log = isset($val[1]) ? $val[1] : "";

            $info = json_decode($log, true);
            if(!$info || !is_array($info)) {
                continue;
            }
            $type = "vrplat";
            if(isset($info['project']) && $info['project']) {
                $type = $info['project'];
            }
            $id = isset($info['properties']['id']) ? $info['properties']['id'] : 0;
            /**
             * 日志数据不全
             * 根据日志类型，从数据库中读
             */
            if(isset($info['properties']['isall']) && $info['properties']['isall'] != 1) {

                // todo

            }
            if(isset($info['properties']['isall'])) {
                unset($info['properties']['isall']);
            }
            $data = json_encode($info);
            $ret = DataCenterStatModel::send($data, $type);
            UdpLog::save2("stat.vronline.com/send", array("id" => $id, "ret" => $ret), null, false);
        }
        Vredis::close();
    }
}

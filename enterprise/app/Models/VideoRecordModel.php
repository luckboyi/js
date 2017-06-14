<?php
namespace App\Models;

use App\Helper\ImageHelper;
use App\Models\GameModel;
use App\Models\VideoModel;
use DB;
use Illuminate\Database\Eloquent\Model;

class VideoRecordModel extends Model
{
    public function addRecord($uid,$vid){
      $where = ['uid'=>$uid,'vid'=>$vid];
      $res = DB::connection("db_operate")->table("v_video_record")->where($where)->first();
      if($res){
          $ret = DB::connection("db_operate")->table("v_video_record")->where('id',$res['id'])->update(['updated_at'=>time()]);
      }
      else{
          $data = [
            'uid' => $uid,
            'vid' => $vid,
            'created_at' => time(),
            'updated_at' => time(),
          ];
          $ret = DB::connection("db_operate")->table("v_video_record")->insert($data);
      }
      return $ret;
    }
}

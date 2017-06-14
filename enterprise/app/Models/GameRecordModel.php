<?php
namespace App\Models;

use App\Helper\ImageHelper;
use App\Models\GameModel;
use App\Models\VideoModel;
use DB;
use Illuminate\Database\Eloquent\Model;

class GameRecordModel extends Model
{
    public function addRecord($uid,$vid,$type){
      $where = ['uid'=>$uid,'gid'=>$vid,'type'=>$type];
      $res = DB::connection("db_operate")->table("v_game_record")->where($where)->first();
      if($res){
          $ret = DB::connection("db_operate")->table("v_game_record")->where('id',$res['id'])->update(['updated_at'=>time()]);
      }
      else{
          $data = [
            'uid' => $uid,
            'gid' => $vid,
            'type' => $type,
            'created_at' => time(),
            'updated_at' => time(),
          ];
          $ret = DB::connection("db_operate")->table("v_game_record")->insert($data);
      }
      return $ret;
    }
}

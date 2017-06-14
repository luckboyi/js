<?php
namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class NewsModel extends Model {

	private $newsDevSize = 18;
	private $newsPositionSize = 18;

	//page start
	public function getNewsByCode($code, $startNum = 0, $pagenum = 6) {

		$row = DB::connection('db_operate')->table('news_position')->select('posid', 'content_tp')->where('code', $code)->first();
		if ($row) {
			$posid = $row['posid'];
			$arr = DB::connection('db_operate')->table('news_recommend')->where('posid', $posid)->orderBy('weight', 'desc')->skip($startNum)->take($pagenum)->get();
			$out = [];
			if ($arr) {
				if ($row['content_tp'] == "article") {
					$articleIds = [];
					$posInfo = [];

					foreach ($arr as $key => $value) {
						$articleIds = $value['itemid'];
						//$posInfo[$value['itemid']] = $value;
						$articles = $this->getArticleById($articleIds);

						$cover = $value['cover'] ? $value['cover'] : $articles['cover'];
						$title = $value['title'] ? $value['title'] : $articles['title'];
						$desc = $value['intro'] ? $value['intro'] : htmlSubStr($articles['content'], 80);
						$link = $value['target_url'] ? $value['target_url'] : '/news/detail/' . $articles['id'] . '.html';
						$out[] = [
							'itemid' => $articleIds,
							'tp' => 'article',
							'cover' => $cover,
							'title' => $title,
							'desc' => $desc,
							'link' => $link,
							'class' => $articles['tp'],
						];
					}
					return $out;
				} else {
					foreach ($arr as $value) {
						$out[] = [
							'itemid' => 0,
							'tp' => 'banner',
							'cover' => $value['cover'],
							'title' => $value['title'],
							'desc' => $value['intro'],
							'link' => $value['target_url'],
							'class' => 0,
						];
					}
					return $out;
				}
			}
		}

		return false;
	}

	public function getArticleById($id) {
		$row = DB::connection("db_operate")->table("t_news")->where("id", $id)->first();
		return $row;
	}

	public function getArticleByCategory($tp, $startNum = 0, $pagenum = 3) {
		if (is_array($tp)) {
			$row = DB::connection("db_operate")->table("t_news")->where('stat', 0)->whereIn("tp", $tp)->orderBy('vtime', 'desc')->skip($startNum)->take($pagenum)->get();
		} else if ($tp == 0) {
			$row = DB::connection("db_operate")->table("t_news")->where('stat', 0)->orderBy('vtime', 'desc')->skip($startNum)->take($pagenum)->get();
		} else {
			$row = DB::connection("db_operate")->table("t_news")->where('stat', 0)->where("tp", $tp)->orderBy('vtime', 'desc')->skip($startNum)->take($pagenum)->get();

		}
		return $row;
	}

	public function getArticlesByIds($ids) {
		$row = DB::connection("db_operate")->table("t_news")->select('id', 'title', 'cover', 'content', 'tp')->whereIn("id", $ids)->get();
		return $row;
	}

	//page end

	//dev news start
	public function getDevNews($class_id = 0, $search = '') {

		$res = DB::connection('db_dev')->table('t_news');
		if ($class_id > 0) {
			$res->where("tp", $class_id);
		}

		if ($search) {
			if (is_numeric($search)) {
				$res->where("id", $search);
			} else {
				$res->where("title", "LIKE", '%' . $search . '%');
			}
		}

		$row = $res->orderBy('ctime', 'desc')->paginate($this->newsDevSize);

		return $row;
	}

	public function getDevAuditNews($class_id) {
		if ($class_id > 0) {
			$row = DB::connection('db_dev')->table('t_news')->where('stat', 1)->where('tp', $class_id)->orderBy('ltime', 'desc')->paginate($this->newsDevSize);
		} else {
			$row = DB::connection('db_dev')->table('t_news')->where('stat', 1)->orderBy('ltime', 'desc')->paginate($this->newsDevSize);
		}

		return $row;
	}

	public function getDevNewsById($id) {
		$row = DB::connection('db_dev')->table('t_news')->where('id', $id)->first();
		return $row;
	}

	public function updateDevNews($id, $info) {
		if (!$info) {
			return false;
		}
		if ($id > 0) {
			$row = DB::connection('db_dev')->table('t_news')->select('stat')->where('id', $id)->first();
			if ($row['stat'] == 1) {
				return false;
			}
			$ret = DB::connection('db_dev')->table('t_news')->where('id', $id)->update($info);
		} else {
			$ret = DB::connection('db_dev')->table('t_news')->insertGetId($info);
		}

		return $ret;
	}

	public function passDevNews($id) {
		$row = DB::connection('db_dev')->table('t_news')->where('id', $id)->first();
		if ($row) {
			$row['stat'] = 0;
			unset($row['ctime']);
			unset($row['ltime']);
			$vtime = date("Y-m-d H:i:s");
			$row['vtime'] = $vtime;
			$ret = DB::connection('db_operate')->table('t_news')->replace($row);
			if ($ret) {
				$ret = DB::connection('db_dev')->table('t_news')->where('id', $id)->update(['stat' => 0, 'vtime' => $vtime]);
			}
		}

		return $ret;
	}

	public function delDevNews($id) {
		$row = DB::connection('db_operate')->table('t_news')->where('id', $id)->first();
		if ($row) {
			$ret1 = DB::connection('db_operate')->table('t_news')->where('id', $id)->update(['stat' => 9]);
			$ret2 = DB::connection('db_dev')->table('t_news')->where('id', $id)->update(['stat' => 9]);
			return $ret1 && $ret2;
		} else {
			$ret = DB::connection('db_dev')->table('t_news')->where('id', $id)->delete();
			return $ret;
		}
	}

	// dev news end

	//pos start
	public function getNewsAllTopPos() {
		$row = DB::connection('db_operate')->table('news_position')->orderBy('posid', 'asc')->get();
		return $row;
	}

	public function postionData($id) {
		$row = DB::connection('db_operate')->table('news_recommend')->where('posid', $id)->orderBy('weight', 'desc')->get();
		return $row;
	}

	public function getNewsTopPos() {
		$row = DB::connection('db_operate')->table('news_position')->orderBy('ltime', 'desc')->paginate($this->newsPositionSize);
		return $row;
	}

	public function updateNewsPosition($id, $info) {
		if (!$info) {
			return false;
		}
		if ($id > 0) {
			$ret = DB::connection('db_operate')->table('news_position')->where('posid', $id)->update($info);
		} else {
			$ret = DB::connection('db_operate')->table('news_position')->insert($info);
		}

		return $ret;
	}

	public function delNewsPosition($id) {
		$ret = DB::connection('db_operate')->table('news_position')->where('posid', $id)->delete();
		return $ret;
	}
	//pos end

	//admin recommend
	public function searchTitle($content) {
		if (is_numeric($content) && $content > 0) {
			$row = DB::connection("db_operate")->table("t_news")->select('id', 'title')->where('id', $content)->get();
		} else {
			$row = DB::connection("db_operate")->table("t_news")->select('id', 'title')->where('title', 'like', '%' . $content . '%')->get();
		}

		return $row;
	}

	public function topPostionWeight($drag, $drop) {
		$rowDrag = DB::connection("db_operate")->table("news_recommend")->select('weight')->where('id', $drag)->first();
		$rowDrop = DB::connection("db_operate")->table("news_recommend")->select('weight')->where('id', $drop)->first();

		if (!$rowDrag || !$rowDrop) {
			return false;
		}
		$ret1 = DB::connection("db_operate")->table("news_recommend")->where("id", $drag)->update(['weight' => $rowDrop['weight']]);
		$ret2 = DB::connection("db_operate")->table("news_recommend")->where("id", $drop)->update(['weight' => $rowDrag['weight']]);

		return $ret1 && $ret2;
	}

	public function updateNewsRecommend($id, $info) {
		if (!is_array($info)) {
			return false;
		}
		if ($id > 0) {
			$ret = DB::connection("db_operate")->table("news_recommend")->where("id", $id)->update($info);
		} else {
			$retId = DB::connection("db_operate")->table("news_recommend")->insertGetId($info);
			if ($retId) {
				$ret = DB::connection("db_operate")->table("news_recommend")->where("id", $retId)->update(['weight' => $retId]);
			} else {
				$ret = false;
			}
		}
		return $ret;
	}

	public function delNewsRecommend($id) {
		$ret = DB::connection('db_operate')->table('news_recommend')->where('id', $id)->delete();
		return $ret;
	}

	public function getPosInfoByCodes($codes) {
		$ret = DB::connection('db_operate')->table('news_position')->whereIn('code', $codes)->get();
		return $ret;
	}

	public function updateNewsSupport($id, $support) {
		$support = (int) $support;
		$id = (int) $id;

		if (!in_array($support, [0, 1]) || $id <= 0) {
			return false;
		}

		$field = "support";
		if ($support == 0) {
			$field = "unsupport";
		}

		$ret = DB::connection('db_operate')->table('t_news')->where("id", $id)->increment($field);

		return $ret;
	}
	//admin recommend end
}

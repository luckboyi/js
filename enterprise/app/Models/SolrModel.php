<?php
namespace App\Models;

use App\Helper\ImageHelper;
use Config;
use DB;
use Helper\HttpRequest;
use Helper\Library;
use Illuminate\Database\Eloquent\Model;

class SolrModel extends Model
{
    private $updateNum = 100;
    private $maxNum    = 10000;

    /*
     * 批量更新文章
     */
    public function updateVronlineGame()
    {
        $rows = DB::connection("db_vronline")->table("t_game")->get();
        $num  = 0;
        if ($rows) {
            $solrDatas = [];
            foreach ($rows as $row) {
                $solrData = [
                    'tp'           => 'game',
                    'itemid'       => $row['game_id'],
                    'title'        => $row['game_name'],
                    'alias'        => $row['game_alias'],
                    'keywords'     => $row['game_search_name'],
                    'tag'          => strToArr($row['game_tag']),
                    'category'     => strToArrInt($row['game_category']),
                    'author'       => 0,
                    'intro'        => $row['game_desc'],
                    'cover'        => $row['game_image'],
                    'score'        => floatval($row['game_mark']),
                    'device'       => strToArrInt($row['game_device']),
                    'price'        => floatval($row['game_price']),
                    'platform'     => strToArrInt($row['game_platform']),
                    'stat'         => intval($row['game_status']),
                    'view'         => intval($row['game_view_num']),
                    'sell_date'    => intval($row['game_sell_date']),
                    'weight_score' => 0,
                    'time'         => strtotime($row['ltime']),
                ];
                $solrData['messageId'] = 'game' . '-' . $row['game_id'];
                $solrDatas[]           = $solrData;
                $num++;
            }
        }

        $ret = $this->updateSolr('vronline', $solrDatas);
        if ($ret) {
            return $num;
        }
        return $num;
    }

    /*
     * 批量更新文章
     */
    public function updateVronlineArticle()
    {
        $rows = DB::connection("db_vronline")->table("t_article")->get();

        $num = 0;
        if ($rows) {
            $solrDatas = [];
            foreach ($rows as $row) {
                $solrData = [
                    'tp'       => $row['article_tp'],
                    'itemid'   => $row['article_id'],
                    'title'    => $row['article_title'],
                    'alias'    => $row['article_alias'],
                    'keywords' => $row['article_keywords'],
                    'tag'      => strToArr($row['article_tag']),
                    'category' => strToArrInt($row['article_category']),
                    'intro'    => $row['article_content'],
                    'cover'    => $row['article_cover'],
                    'score'    => 0,
                    'device'   => [],
                    'price'    => 0,
                    'stat'     => intval($row['article_stat']),
                    'time'     => strtotime($row['ltime']),
                ];
                if ($row['article_author_id']) {
                    $solrData['author'] = $row['article_author_id'];
                }
                $solrData['messageId'] = $row['article_tp'] . '-' . $row['article_id'];
                $solrDatas[]           = $solrData;
                $num++;
            }
        }

        $ret = $this->updateSolr('vronline', $solrDatas);
        if ($ret) {
            return $num;
        }
        return $num;
    }

    public function searchVronline($params, $needHandle = true, &$numFound = 0)
    {
        $add = "&q=stat:0";
        if (isset($params['tp'])) {
            $add .= '&fq=tp:' . $params['tp'];
        }
        if (isset($params['itemid'])) {
            $add .= '&fq=-itemid:' . $params['itemid'];
        }
        if (isset($params['author'])) {
            $add .= '&fq=author:' . $params['author'];
        }
        if (isset($params['platform'])) {
            $add .= '&fq=platform:' . $params['platform'];
        }
        if (isset($params['category'])) {
            $add .= '&fq=category:' . intval($params['category']);
        }
        if (isset($params['device'])) {
            $add .= '&fq=device:' . intval($params['device']);
        }
        if (isset($params['tag'])) {
            $add .= '&fq=tag:' . urlencode($params['tag']);
        }
        if (isset($params['price'])) {
            $add .= '&fq=price:' . urlencode('[' . $params['price'][0] . ' TO ' . $params['price'][1] . ']');
        }

        if (isset($params['title'])) {
            if (is_array($params['title'])) {
                foreach ($params['title'] as $value) {
                    $add .= '&fq=title:' . urlencode(strtolower($value));
                }
            } else {
                $params['title'] = strtolower($params['title']);
                $add .= '&fq=title:*' . urlencode($params['title']) . '*';
            }
        }
        if (isset($params['limit']) && is_array($params['limit'])) {
            $add .= '&start=' . intval($params['limit'][0]) . '&rows=' . intval($params['limit'][1]);
        } else {
            $add .= '&start=0&rows=20';
        }

        if (isset($params['orderBy'])) {
            $add .= '&sort=' . urlencode($params['orderBy']);
        } else {
            $add .= '&sort=' . urlencode("time desc");
        }

        $cfg = Config::get('server.solr.vronline');
        $url = $cfg['url'] . $cfg['core'] . "/select?indent=on{$add}&wt=json";

        $arr = HttpRequest::url('get', $url);
        if (isset($arr['response'])) {
            if (!empty($arr['response']['docs'])) {
                $numFound = $arr['response']['numFound'];
                if (!$needHandle) {
                    return $arr['response']['docs'];
                }
                $out = [];
                foreach ($arr['response']['docs'] as $key => $value) {
                    $out[] = $value;
                }
                return $out;
            }
            return [];
        }
        return [];
    }

    public function getTop($code, $num = false, $target = false)
    {
        $cfg = Config::get('server.solr.top');
        $add = "&fq=stat:0";
        if ($num && is_numeric($num)) {
            $add .= '&start=0&rows=' . $num;
        }
        $add .= '&sort=' . urlencode("weight desc");
        $url = $cfg['url'] . $cfg['core'] . "/select?indent=on&q=code:{$code}{$add}&wt=json";
        $arr = HttpRequest::url('get', $url);

        if (isset($arr['response'])) {
            if (!empty($arr['response']['docs'])) {
                $out = [];
                foreach ($arr['response']['docs'] as $key => $value) {
                    if (isset($value['image'])) {
                        $value['image'] = json_decode($value['image'], true);
                    }
                    if (!isset($value['name'])) {
                        $value['name'] = '';
                    }
                    if (!isset($value['desc'])) {
                        $value['desc'] = '';
                    }
                    if ($target == "vrhelp") {
                        if (isset($value['link'])) {
                            $value['target_url'] = $value['link'];
                        } else {
                            if ($value['tp'] == 'vrgame') {
                                $value['target_url'] = '#tp=game&id=' . $value['id'];
                            } else if ($value['tp'] == 'video') {
                                $value['target_url'] = '#tp=video&id=' . $value['id'];
                            } else {
                                $value['target_url'] = '';
                            }
                        }
                    }
                    $out[] = $value;
                }
                return ['data' => $out];
            }
            return ['data' => []];
        }
        return ['data' => []];
    }

    public function search($tp, $params, $needHandle = true, &$numFound = 0)
    {
        $add = "&fq=stat:0";
        if (isset($params['category'])) {
            $add .= '&fq=category:' . intval($params['category']);
        }
        if (isset($params['merchantid'])) {
            $add .= '&fq=merchantid:' . $params['merchantid'];
        }
        if (isset($params['terminal_sn'])) {
            $add .= '&fq=terminal_sn:' . $params['terminal_sn'];
        }
        if (isset($params['support'])) {
            $add .= '&fq=support:' . intval($params['support']);
        }
        if (isset($params['sell'])) {
            $add .= '&fq=sell:' . urlencode('[' . $params['sell'] . ' TO *]');
        }
        if (isset($params['tob_in'])) {
            $add .= '&fq=tob_in:' . urlencode('[' . $params['tob_in'] . ' TO *]');
        }
        if (isset($params['spell'])) {
            if ($params['spell']) {
                $spells   = explode(" ", $params['spell']);
                $strSpell = "";
                for ($i = 0; $i < count($spells); $i++) {
                    if ($strSpell) {
                        $strSpell .= " or ";
                    }
                    $strSpell .= "spell:{$spells[$i]}";
                }
                $add .= '&fq=' . rawurlencode(strtolower($strSpell));
            }
        }

        if (isset($params['name'])) {
            $params['name'] = strtolower($params['name']);
            $params['name'] = str_replace(
                [":", "+", "-", "&&", "||", "!", "(", ")", "{", "}", "[", "]", "^", "~", "*", "?"],
                ["\:", "\+", "\-", "\&&", "\||", "\!", "\(", "\)", "\{", "\}", "\[", "\]", "\^", "\~", "\*", "\?"], $params['name']);
            if (isset($params['suggest'])) {
                $add .= '&fq=' . urlencode('name:' . $params['name'] . ' or name:' . $params['name'] . '*');
            } else {
                $add .= '&fq=' . urlencode('name:' . $params['name']);
            }

        }

        if (isset($params['gameids'])) {
            foreach ($params['gameids'] as $gameid) {
                $addGameIds[] = 'id:' . $gameid;
            }
            $add .= '&fq=' . urlencode(implode(' or ', $addGameIds));
        }

        if (isset($params['limit']) && is_array($params['limit'])) {
            $add .= '&start=' . intval($params['limit'][0]) . '&rows=' . intval($params['limit'][1]);
        } else {
            $add .= '&start=0&rows=20';
        }

        if (isset($params['orderBy'])) {
            $add .= '&sort=' . urlencode($params['orderBy']);
        } else {
            $add .= '&sort=' . urlencode("time desc");
        }
        if (!isset($params['suggest']) && !isset($params['spell'])) {
            $add .= '&q.op=AND';
        }
        if ($tp == 'tob') {
            $cfg = Config::get('server.solr.tob');
            $url = $cfg['url'] . $cfg['core'] . "/select?indent=on&q=*:*{$add}&wt=json";
        } else {
            $cfg = Config::get('server.solr.common');
            $url = $cfg['url'] . $cfg['core'] . "/select?indent=on&q=tp:{$tp}{$add}&wt=json";
        }

        $arr = HttpRequest::url('get', $url);
        if (isset($arr['response'])) {
            if (!empty($arr['response']['docs'])) {
                //    if (isset($numFound)) {
                $numFound = $arr['response']['numFound'];
                //}
                if (!$needHandle) {
                    return $arr['response']['docs'];
                }
                $out = [];
                foreach ($arr['response']['docs'] as $key => $value) {
                    if (isset($value['image'])) {
                        $value['image'] = json_decode($value['image'], true);
                    }
                    $out[] = $value;
                }
                return $out;
            }
            return [];
        }
        return [];
    }

    public function suggest($name)
    {
    }

    public function updateGame($id = 0)
    {
        $num = 0;
        if ($id > 0) {
        } else {
            $start = 0;
            for ($start = 0; $start <= $this->maxNum; $start = $start + $this->updateNum) {
                $solrDatas = [];
                $games     = DB::connection("db_webgame")->table("t_webgame")->skip($start)->take($this->updateNum)->get();
                if (!$games) {
                    break;
                }
                foreach ($games as $game) {
                    $tp               = $game['game_type'] == 0 ? 'webgame' : 'vrgame';
                    $resInfo          = ImageHelper::url($tp, $game['appid'], $game['img_version'], $game['img_slider'], false);
                    $resInfo['cover'] = $resInfo['rank'];
                    $class            = $this->arrayToInt(explode(",", $game['first_class']));
                    $support          = $this->arrayToInt(explode(",", $game['support']));
                    $mountings        = $this->arrayToInt(explode(",", $game['mountings']));
                    $spell            = $game['spell_name'];
                    $solrData         = [
                        'id'             => $game['appid'],
                        'tp'             => $tp,
                        'tags'           => $game['tags'],
                        'name'           => $game['name'],
                        'spell'          => $spell,
                        'desc'           => $game['content'],
                        'category'       => $class,
                        'support'        => $support,
                        'mountings'      => $mountings,
                        'original_sell'  => floatval($game['original_sell']),
                        'sell'           => floatval($game['sell']),
                        'play'           => intval($game['play']),
                        'tob_play'       => intval($game['tob_play']),
                        'tob_in'         => intval($game['tob_in']),
                        'client_size'    => $game['client_size'],
                        'client_version' => $game['version_code'],
                        'score'          => floatval($game['score']),
                        'image'          => json_encode($resInfo),
                        'time'           => strtotime($game['ltime']),
                        'publish_date'   => strtotime($game['send_time']),
                        'stat'           => intval($game['stat']),
                    ];
                    $solrData['messageId'] = $tp . $solrData['id'];
                    $solrDatas[]           = $solrData;
                }
                $ret = $this->updateSolr('common', $solrDatas);
                if ($ret) {
                    $num += count($solrDatas);
                }
            }
        }
        return $num;
    }

    public function updateVideo($id = 0)
    {
        $num = 0;
        if ($id > 0) {
        } else {
            $start = 0;
            for ($start = 0; $start <= $this->maxNum; $start = $start + $this->updateNum) {
                $videos = DB::connection("db_operate")->table("t_video")->skip($start)->take($this->updateNum)->get();
                if (!$videos) {
                    break;
                }
                $solrDatas = [];
                foreach ($videos as $video) {
                    $tp       = 'video';
                    $resInfo  = ['cover' => $video['video_cover'], 'rank' => $video['video_rank']];
                    $class    = $this->arrayToInt(explode(",", $video['video_class']));
                    $support  = $this->arrayToInt(explode(",", $video['video_upfacility']));
                    $spell    = $video['video_spell'];
                    $solrData = [
                        'id'           => $video['video_id'],
                        'tp'           => $tp,
                        'name'         => $video['video_name'],
                        'spell'        => $spell,
                        'tags'         => $video['video_keywords'],
                        'desc'         => $video['video_intro'],
                        'category'     => $class,
                        'support'      => $support,
                        'show_time'    => $video['video_times'],
                        'play'         => intval($video['video_view']),
                        'score'        => intval($video['agreenum']),
                        'image'        => json_encode($resInfo),
                        'time'         => strtotime($video['ltime']),
                        'publish_date' => time(),
                        'stat'         => intval($video['video_stat']),
                    ];
                    $solrData['messageId'] = $tp . $solrData['id'];
                    $solrDatas[]           = $solrData;
                }
                $ret = $this->updateSolr('common', $solrDatas);
                if ($ret) {
                    $num += count($solrDatas);
                }
            }
        }

        return $num;
    }

    public function delTerminalGame($merchantid, $terminal_sn, $appid)
    {
        if (!$merchantid || !$terminal_sn || !$appid) {
            return false;
        }
        $updateJson = '<delete><query>merchantid:' . $merchantid . ' AND terminal_sn:' . $terminal_sn . ' AND id:' . $appid . '</query></delete>';
        $cfg        = Config::get('server.solr.tob');
        $time       = time();
        $url        = $cfg['url'] . $cfg['core'] . "/update?_=" . $time . "&boost=1.0&commitWithin=1000&overwrite=true&wt=json";

        $headers = array(
            'Content-Type: text/xml',
            'Content-Length: ' . strlen($updateJson),
        );
        $result = HttpRequest::url('post', $url, $headers, $updateJson);
        if (isset($result["responseHeader"])) {
            if ($result["responseHeader"]["status"] == 0) {
                return true;
            }
        }
        return false;
    }

    /*
     * 更新终端游戏
     */
    public function updateTerminalGame($merchantid, $terminal_sn, $appids = [])
    {
        $num = 0;
        if (!$merchantid || !$terminal_sn) {
            return $num;
        }
        $plays = [];
        if (!is_array($appids) || empty($appids)) {
            $terminalGames = DB::connection("db_2b_store")->table("t_2b_terminal_games")->select('appid', 'play')->where('merchantid', $merchantid)->where('terminal_sn', $terminal_sn)->get();
            if (!is_array($terminalGames) || empty($terminalGames)) {
                return $num;
            }
            $appids = [];
            foreach ($terminalGames as $value) {
                $appids[] = $value['appid'];
                if ($terminal_sn == 'master') {
                    $plays[$value['appid']] = $value['play'];
                }

            }
        }

        $games = DB::connection("db_webgame")->table("t_webgame")->whereIn('appid', $appids)->get();
        if (!is_array($games) || empty($games)) {
            return $num;
        }

        $solrDatas = [];
        foreach ($games as $game) {
            $resInfo  = ImageHelper::url('vrgame', $game['appid'], $game['img_version'], $game['img_slider'], false);
            $class    = $this->arrayToInt(explode(",", $game['first_class']));
            $support  = $this->arrayToInt(explode(",", $game['support']));
            $spell    = $game['spell_name'];
            $solrData = [
                'merchantid'     => $merchantid,
                'terminal_sn'    => $terminal_sn,
                'id'             => $game['appid'],
                'tags'           => $game['tags'],
                'name'           => $game['name'],
                'spell'          => $spell,
                'desc'           => $game['content'],
                'category'       => $class,
                'support'        => $support,
                'original_sell'  => floatval($game['original_sell']),
                'sell'           => floatval($game['sell']),
                'play'           => intval($game['play']),
                'tob_play'       => isset($plays[$game['appid']]) ? $plays[$game['appid']] : 0,
                'tob_in'         => 0,
                'client_size'    => $game['client_size'],
                'client_version' => $game['version_code'],
                'score'          => floatval($game['score']),
                'image'          => json_encode($resInfo),
                'time'           => strtotime($game['ltime']),
                'publish_date'   => strtotime($game['send_time']),
                'stat'           => intval($game['stat']),
            ];
            $solrData['messageId'] = $merchantid . '-' . $terminal_sn . '-' . $game['appid'];
            $solrDatas[]           = $solrData;
        }
        $ret = $this->updateSolr('tob', $solrDatas);
        if ($ret) {
            $num += count($solrDatas);
        }
        return $num;
    }

    /*
     *  批量 更新终端游戏
     */
    public function updateTerminalsGame($merchantid, $terminals, $appids = [])
    {
        $num = 0;
        if (!$merchantid || !is_array($terminals)) {
            return false;
        }

        $games = DB::connection("db_webgame")->table("t_webgame")->whereIn('appid', $appids)->get();
        if (!is_array($games) || empty($games)) {
            return false;
        }

        $solrDatas = [];
        foreach ($terminals as $terminal) {
            foreach ($games as $game) {
                $resInfo  = ImageHelper::url('vrgame', $game['appid'], $game['img_version'], $game['img_slider'], false);
                $class    = $this->arrayToInt(explode(",", $game['first_class']));
                $support  = $this->arrayToInt(explode(",", $game['support']));
                $spell    = $game['spell_name'];
                $solrData = [
                    'merchantid'     => $merchantid,
                    'terminal_sn'    => $terminal,
                    'id'             => $game['appid'],
                    'tags'           => $game['tags'],
                    'name'           => $game['name'],
                    'spell'          => $spell,
                    'desc'           => $game['content'],
                    'category'       => $class,
                    'support'        => $support,
                    'original_sell'  => floatval($game['original_sell']),
                    'sell'           => floatval($game['sell']),
                    'tob_play'       => 0,
                    'tob_in'         => 0,
                    'client_size'    => $game['client_size'],
                    'client_version' => $game['version_code'],
                    'play'           => intval($game['play']),
                    'score'          => floatval($game['score']),
                    'image'          => json_encode($resInfo),
                    'time'           => strtotime($game['ltime']),
                    'publish_date'   => strtotime($game['send_time']),
                    'stat'           => intval($game['stat']),
                ];
                //'terminal_sn'   => $terminal_sn,
                $solrData['messageId'] = $merchantid . '-' . $terminal . '-' . $game['appid'];
                $solrDatas[]           = $solrData;
            }
        }

        $ret = $this->updateSolr('tob', $solrDatas);
        if ($ret) {
            return true;
        }
        return false;
    }

    public function updateMerchantGame()
    {
        $num  = 0;
        $rows = DB::connection("db_2b_store")->table("t_2b_terminal")->orderBy("merchantid", "desc")->get();
        if (is_array($rows) && !empty($rows)) {
            foreach ($rows as $value) {
                $ret = $this->updateTerminalGame($value['merchantid'], $value['terminal_sn']);
                if ($ret) {
                    $num += $ret;
                }

                $merchantids[$value['merchantid']] = 1;
            }

            if ($merchantids) {
                foreach ($merchantids as $merchantid => $val) {
                    $ret = $this->updateTerminalGame($merchantid, 'master');
                    if ($ret) {
                        $num += $ret;
                    }
                }
            }
        }
        return $num;
    }

    public function updateTop()
    {
        $num   = 0;
        $row   = DB::connection("db_operate")->table("top_postion")->get();
        $codes = [];
        foreach ($row as $key => $value) {
            $codes[] = $value['code'];
        }
        $recommendModel = new RecommendModel;
        $tops           = [];
        foreach ($codes as $code) {
            $recommends = $recommendModel->solrRecommendContentByCode($code);
            foreach ($recommends as $key => $value) {
                $value['code']      = $code;
                $value['messageId'] = $code . "-" . $value['unid'];
                $tops[]             = $value;
            }
        }
        $ret = $this->updateSolr('top', $tops);
        if ($ret) {
            $num = count($tops);
        }
        return $num;
    }

    public function updateVronlineTop()
    {

    }

    private function updateSolr($tp, $datas)
    {
        if (!$tp) {
            return false;
        }
        $updateJson = json_encode($datas, JSON_UNESCAPED_UNICODE);

        $cfg  = Config::get('server.solr.' . $tp);
        $time = time();
        $url  = $cfg['url'] . $cfg['core'] . "/update?_=" . $time . "&boost=1.0&commitWithin=1000&overwrite=true&wt=json";

        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($updateJson),
        );

        $result = HttpRequest::url('post', $url, $headers, $updateJson);

        if (isset($result["responseHeader"])) {
            if ($result["responseHeader"]["status"] == 0) {
                return true;
            }
        }
        return false;
    }

    private function arrayToInt($arr)
    {
        $out = [];
        foreach ($arr as $value) {
            $v = intval($value);
            if ($v) {
                $out[] = $v;
            }
        }
        return $out;
    }
}

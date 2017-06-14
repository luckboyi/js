<?php
function static_image($path, $style = "")
{
    $base = "https://image.vronline.com/";
    if ($path) {
        if ($style) {
            switch ($style) {
                case '133':
                    $url = $base . $path . "?imageView2/5/w/133/h/99";
                    break;
                case '384':
                    $url = $base . $path . "?imageView2/5/w/384/h/216";
                    break;
                case '280':
                    $url = $base . $path . "?imageView2/5/w/280/h/116";
                    break;
                default:
                    if (strstr($style, '-')) {
                        list($t, $w, $h) = explode("-", $style);
                        $url             = $base . $path . "?imageView2/" . $t . "/w/" . $w . "/h/" . $h;
                    } else {
                        $url = $base . $path . "/" . "ty" . $style;
                    }

                    break;
            }
        } else {
            $url = $base . $path;
        }
    } else {
        $url = '';
    }
    return $url;
}

function static_res($path)
{
    if ($path) {
        $url = "//pic.vronline.com" . $path . "?" . Config::get("staticfiles.file_version");
    } else {
        $url = '';
    }
    return $url;
}

function static_public($path)
{
    if ($path) {
        $url = $path . "?" . Config::get("staticfiles.file_version");
    } else {
        $url = '';
    }
    return $url;
}

function static_game()
{

}

function arrayToInt($arr)
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

function strToArrInt($str)
{
    if (strstr($str, ',')) {
        $arr = explode(",", $str);
    } else if (strstr($str, ' ')) {
        $arr = explode(" ", $str);
    } else if (strstr($str, '，')) {
        $arr = explode("，", $str);
    } else {
        $arr = explode(",", $str);
    }
    $out = [];
    foreach ($arr as $value) {
        $v = intval($value);
        if ($v) {
            $out[] = $v;
        }
    }
    return $out;
}

function strToArr($str)
{
    if (strstr($str, ',')) {
        $arr = explode(",", $str);
    } else if (strstr($str, ' ')) {
        $arr = explode(" ", $str);
    } else if (strstr($str, '，')) {
        $arr = explode("，", $str);
    } else {
        $arr = explode(",", $str);
    }
    return $arr;
}

function roundFloat($str, $num = 2)
{
    return round(floatval($str), $num);
}

function htmlSubStr($html, $length = 40)
{
    $html = preg_replace("/^[\s]+|&nbsp;+|　+/", "", $html);
    $html = strip_tags($html);
    $html = preg_replace("/^[\s]+/", '', $html);
    $html = str_replace(['　', '【VRonline讯】', '&nbsp;'], ['', '', ''], $html);
    return mb_substr($html, 0, $length, "utf-8");
}

function questionCode()
{
    list($usec, $sec) = explode(" ", microtime());
    $hash             = md5($usec . $sec);
    $a                = substr($hash, 0, 4);
    $b                = mt_rand(1000, 9999);
    $c                = substr($hash, 8, 4);
    $d                = mt_rand(1000, 9999);
    return strtoupper($a . $b . $c . $d);
}

function timeFormat($tmp)
{
    if ($tmp > 3600) {
        $hours  = floor($tmp / 3600);
        $hasTmp = $tmp - $hours * 3600;
        $min    = ceil($hasTmp / 60);
        $format = $hours . '小时' . $min . '分钟';
        return $format;
    }
    if ($tmp > 60) {
        $min    = ceil($tmp / 60);
        $format = $min . '分钟';
        return $format;
    }
    return $tmp . '秒';
}

function gameSize($m)
{
    if ($m == 0) {
        return "";
    } elseif ($m >= 1000) {
        return round($m / 1000, 2) . "G";
    } else {
        return $m . "M";
    }
}

function base64Urlsafeencode($data)
{
    $find    = array('+', '/');
    $replace = array('-', '_');
    return str_replace($find, $replace, base64_encode($data));
}

function base64Urlsafedecode($str)
{
    $find    = array('-', '_');
    $replace = array('+', '/');
    return base64_decode(str_replace($find, $replace, $str));
}

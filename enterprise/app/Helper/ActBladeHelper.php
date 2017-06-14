<?php

namespace App\Helper;

class ActBladeHelper
{
    public static function outputInfo($info)
    {
        $html = "";
        if (!isset($info["position"])) {
            return $html;
        }
        switch ($info["position"]) {
            case "3dbb_index":
                $html = "<img data-val=\"" . $info["detail"]["video"] . "\" src=\"" . static_image($info["detail"]["img"]) . "\" />
						<p class=\"text ells\">{$info["detail"]["title"]}</p>
						<div class=\"mask\"></div>";
            default:
                break;
        }
        return $html;
    }
}

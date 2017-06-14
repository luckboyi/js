<?php

namespace Helper;

use Config;

class UserHelper {

	/**
	 * 获取用户头像地址
	 *
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public static function getUserFace($uid) {

		$path1 = $uid % 100;
		$path2 = $uid % 100000;

		$host = Config::get("resource.face_host");

		return $host . "/" . $path1 . "/" . $path2 . "/" . $uid;
	}

}
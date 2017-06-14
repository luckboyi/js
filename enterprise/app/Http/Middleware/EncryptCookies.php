<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncrypter;

class EncryptCookies extends BaseEncrypter {
	/**
	 * The names of the cookies that should be encrypted.
	 *
	 * @var array
	 */
	protected $except = [
		'uid',
		'admin_uid',
	];
}

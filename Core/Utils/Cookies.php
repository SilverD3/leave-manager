<?php
declare(strict_types=1);

namespace Core\Utils;

/**
 * Cookies Core Class
 */
class Cookies
{
	private static $_default_config = [
		'expires' => 60*60*24, // 1 day
		'domain' => '/',
		'secure' => true,
		'httpOnly' => true,
	];

	static function write(string $name, mixed $value, array $config = [])
	{
		$config = array_merge(self::$_default_config, $config);

		setcookie($name, $value, ...$config);
	}

	static function read(string $name)
	{
		if (isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}

		return null;
	}

}

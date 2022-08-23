<?php
declare(strict_types=1);

namespace Core\Utils;

/**
 * Cookies Core Class
 */
class Cookies
{
	/**
	 * Default cookies configuration
	 *
	 * @var array
	 */
	private static $_default_config = [
		'expires' => 60*60*24, // 1 day
		'path' => '/',
		'domain' => '',
		'secure' => true,
		'httpOnly' => true,
	];

	/**
	 * Set cookies
	 *
	 * @param string $name Cookies name
	 * @param mixed $value Cookies value
	 * @param array $config Config. Possible keys are the same as those of the default config
	 * @return void
	 */
	static function write(string $name, mixed $value, array $config = [])
	{
		$config = array_merge(self::$_default_config, $config);

		setcookie(sha1($name), $value, ...array_values($config));
	}

	/**
	 * Unset a cookies
	 *
	 * @param string $name Cookie's name
	 * @return void
	 */
	static function expire(string $name)
	{
		if (isset($_COOKIE[sha1($name)])) {
			$config = array_merge(self::$_default_config, ['expires' => -60*60]);

			setcookie(sha1($name), '', ...array_values($config));
		}
	}

	/**
	 * Read the value of a cookie
	 *
	 * @param string $name Cookie name
	 * @return mixed|null Value of the cookie
	 */
	static function read(string $name)
	{
		return isset($_COOKIE[sha1($name)]) ? $_COOKIE[sha1($name)] : null;
	}

	/**
	 * Read the value of a cookie and unset it
	 *
	 * @param string $name Cookie's name
	 * @return mixed|null The cookie value
	 */
	static function consume(string $name)
	{
		if (isset($_COOKIE[sha1($name)])) {
			$cookie = $_COOKIE[sha1($name)];

			self::expire(sha1($name));
			
			return $cookie;
		}

		return null;
	}

}

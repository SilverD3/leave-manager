<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace Core\Utils;

/**
 * Utils class for storing and retrieving data from $_SESSION variable.
 */
class Session
{
	/**
	 * Store information in $_SESSION variable
	 *
	 * @param string $name Name of the variable
	 * @param mixed $value Variable value
	 * @return void
	 */
	static function write(string $name, mixed $value)
	{
		$_SESSION[sha1($name)] = $value;
	}

	/**
	 * Delete information from the session
	 *
	 * @param string $name Variable name
	 * @return void
	 */
	static function delete(string $name)
	{
		if (isset($_SESSION[sha1($name)])) {
			$_SESSION[sha1($name)] = null;
			unset($_SESSION[sha1($name)]);
		}
	}

	/**
	 * Read the value of a variable store in $_SESSION
	 *
	 * @param string $name Variable name
	 * @return mixed|null Value of the variable
	 */
	static function read(string $name)
	{
		return isset($_SESSION[sha1($name)]) ? $_SESSION[sha1($name)] : null;
	}

	/**
	 * Read the value of a variable from the session and unset it
	 *
	 * @param string $name Variable's name
	 * @return mixed|null The Variable value
	 */
	static function consume(string $name)
	{
		$var = isset($_SESSION[sha1($name)]) ? $_SESSION[sha1($name)] : null;

		if ($var) {
			self::delete($name);
		}

		return $var;
	}
}

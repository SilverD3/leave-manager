<?php
declare(strict_types=1);

namespace Core\Logger;

/**
 * JavaScript Logger
 */
class JsLogger
{
	
	static function __log($log)
	{
		$_SESSION['__logger__'][] = $log;
	}

	static function __clear()
	{
		$_SESSION['__logger__'] = [];
	}

	public static function toConsole(): void
	{
		if (isset($_SESSION['__logger__']) && !empty($_SESSION['__logger__'])) {
			echo "<div style='display:none'><script type='text/javascript'>";

			foreach ($_SESSION['__logger__'] as $log) {
				echo $log;
			}

			echo "</script></div>";
		}

		self::__clear();
	}

	public static function log(string $message): void
	{
		$log = "console.log('". $message ."')";

		self::__log($log);
	}

	public static function error(string $message): void
	{
		$log = "console.error('". $message ."')";

		self::__log($log);
	}

	public static function warn(string $message): void
	{
		$log = "console.warn('". $message ."')";

		self::__log($log);
	}

	public static function info(string $message): void
	{
		$log = "console.info('". $message ."')";

		self::__log($log);
	}
}
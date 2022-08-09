<?php
declare(strict_types=1);

namespace Core;

/**
 * Configure Class
 */
class Configure
{
	private $_default_config = [];
	private $_config = [];

	function __construct()
	{
		$this->_config = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
		$this->_default_config = $this->_config;
	}

	public function read(string $configKey, $default = null)
	{
		if (isset($this->_config[$configKey])) { 
			return $this->_config[$configKey];
		} else if (isset($this->_default_config[$configKey])) {
			return $this->_default_config[$configKey];
		} else {
			return $default;
		}
	}

	public function write(string $configKey, $value)
	{
		$this->_config[$configKey] = $value;

		return $this;
	}
}
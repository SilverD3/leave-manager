<?php
declare(strict_types=1);

namespace Core\FlashMessages;

use Core\FlashMessages\Message;

/**
 * Flash Core Class
 */
class Flash
{
	const MSG_TEMPLATE_PATH = VIEW_PATH . 'Elements' . DS . 'Flash' . DS;

	/**
	 * Get Flash Messages
	 * @return array Array of FlashMessages or empty array
	 */
	private static function getMessages(): array
	{
		if (isset($_SESSION['__flash__']) && !empty($_SESSION['__flash__'])) {
			return unserialize($_SESSION['__flash__']);
		}

		$_SESSION['__flash__'] = null;

		return [];
	}

	/**
	 * Clear all Flash Messages
	 */
	public static function clearMessages(): void
	{
		$_SESSION['__flash__'] = null;
	}

	/**
	 * Render Flash Messages
	 */
	public static function render(): void
	{
		$messages = self::getMessages();
		if (!empty($messages)) {
			foreach ($messages as $message) {
				if ($message->getType() == Message::ALERT) {
					require self::MSG_TEMPLATE_PATH . Message::ALERT . '.php';
				} elseif ($message->getType() == Message::SUCCESS) {
					require self::MSG_TEMPLATE_PATH . Message::SUCCESS . '.php';
				} elseif ($message->getType() == Message::ERROR) {
					require self::MSG_TEMPLATE_PATH . Message::ERROR . '.php';
				} else {
					require self::MSG_TEMPLATE_PATH . Message::ALERT . '.php';
				}
				
			}
		}

		self::clearMessages();
	}

	/**
	 * Add Flash message
	 */
	public static function message(string $message, string $type = Message::ALERT): void
	{
		$message = new Message($message, $type);

		$flash = self::getMessages();

		$flash[] = $message;

		$_SESSION['__flash__'] = serialize($flash);
	}

	/**
	 * Add Flash error message
	 * @param  string $message Message to be displayed
	 */
	public static function success(string $message): void
	{
		self::message($message, Message::SUCCESS);
	}

	/**
	 * Add Flash success message
	 * @param  string $message Message to be displayed
	 */
	public static function error(string $message): void
	{
		self::message($message, Message::ERROR);
	}
}

<?php
declare(strict_types=1);

namespace App\Controller;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Service\AuthServices;
use Core\Auth\Auth;
use Core\FlashMessages\Flash;

/**
 * Authentication Controller
 */
class AuthController
{
	const UNAUTHORIZED_REDIRECT = VIEWS . 'Auth/login.php';
	const AUTHORIZED_REDIRECT = BASE_URL;
	
	public function login()
	{
		if (Auth::isConnected()) {
			header("Location: " . self::AUTHORIZED_REDIRECT);
			exit;
		}

		if (isset($_POST['login'])) {
			$username = htmlentities($_POST['username']);
			$password = htmlentities($_POST['password']);

			$employee = (new AuthServices())->login($username, $password);

			if (empty($employee)) {
				Flash::error("Le nom d'utilisateur ou le mot de passe est incorrect.");
			} else {

				Auth::setUser($employee);

				Flash::success("Connexion reussie");

				if (isset($_GET['redirect'])) {
					header("Location: " . $_GET['redirect']);
					exit;
				}
				
				header("Location: " . self::AUTHORIZED_REDIRECT);
			}
		}
	}

	public static function logout()
	{
		Auth::unsetUser();

		Flash::success("A bientôt!");

		header('Location: ' . self::UNAUTHORIZED_REDIRECT);
		exit;
	}

	public static function require_auth()
	{
		if (!Auth::isConnected()) {
			Flash::error("Veuillez vous connecter avant de continuer.");

			header('Location: ' . self::UNAUTHORIZED_REDIRECT . '?redirect=' . $_SERVER['REQUEST_URI']);
			exit;
		}

		$GLOBALS['auth_user'] = (new Auth())->getAuthUser();
	}
}
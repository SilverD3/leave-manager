<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace Core\Auth;

/**
 * Password Hasher Class
 */
class PasswordHasher
{
	/**
	 * Hash password
	 * 
	 * @param string $password Password to hash
	 * @return string Hashed password
	 */
	public function hash($password): string
	{
		return hash('sha256', '$@LVM' . $password . '@#');
	}

	/**
	 * Check if password string matches hashed password
	 *
	 * @param string $password Password string
	 * @param string $hashedPassword Hashed password
	 * @return bool Returns true if password string matches hashed password, false otherwise.
	 */
	public function check($password, $hashedPassword): bool
	{
		return $this->hash($password) === $hashedPassword;
	}
}

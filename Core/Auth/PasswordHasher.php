<?php
declare(strict_types=1);

namespace Core\Auth;

/**
 * Password Hasher Class
 */
class PasswordHasher
{
	
	public function hash($password): string
	{
		return hash('sha256', '$@LVM' . $password . '@#');
	}

	public function check($password, $hashedPassword): bool
	{
		return hash('sha256', '$@LVM' . $password . '@#') === $hashedPassword;
	}
}
<?php

declare(strict_types=1);
namespace App\Model\User\Entity\Service;


class PasswordHasher
{
	public function hash(string $passeord):string
	{
		$hash=password_hash($passeord,PASSWORD_ARGON2I);
		if ($hash===false) {
			throw new \RuntimeException('Unavle to generate hash.');
		}
		return $hash;

}

}
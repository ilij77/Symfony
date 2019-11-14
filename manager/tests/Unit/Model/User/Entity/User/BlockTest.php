<?php

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
	public function testSuccess()
	{
		$user=(new UserBuilder())->build();
		$user->block();
		self::assertFalse($user->isActive());
		self::assertTrue($user->isBlocked());

	}

	public function testAlready()
	{
		$user=(new UserBuilder())->build();
		$user->block();
		$this->expectExceptionMessage('User is already blocked.');
		$user->block();

	}

}

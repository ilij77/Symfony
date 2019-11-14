<?php

namespace App\Tests\Unit\Model\User\Entity\User;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ActivateTest extends TestCase
{
	public function testSuccess()
	{
		$user=(new UserBuilder())->viaEmail()->build();
		$user->block();
		$user->activate();
		self::assertTrue($user->isActive());
		self::assertFalse($user->isBlocked());
	}

	public function testAlready()
	{
		$user=(new UserBuilder())->viaEmail()->build();
		$user->activate();
		$this->expectExceptionMessage('User is already active.');
		$user->activate();

	}

}

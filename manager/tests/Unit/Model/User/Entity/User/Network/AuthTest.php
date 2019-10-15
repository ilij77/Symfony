<?php

namespace App\Tests\Unit\Model\User\Entity\User\Network;



use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
	public function testSuccess():void
	{
		$user=new User(
			Id::next(),
			new \DateTimeImmutable()
		);
		$user=User::signUpByNetwork(
			Id::next(),
			new \DateTimeImmutable(),
			$network='vk',
			$identity='000001'
		);
//		$user=(new UserBuilder())->viaEmail()->confirmed()->build();
		self::assertTrue($user->isActive());
		self::assertCount(1,$networks=$user->getNetworks());
		self::assertInstanceOf(Network::class,$first=reset($networks));
		self::assertEquals($network,$first->getNetwork());
		self::assertEquals($identity,$first->getIdentity());
		self::assertTrue($user->getRole()->isUser());

	}


}

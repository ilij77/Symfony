<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Reset;


use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;



class ResetTest extends TestCase
{
	public function testSuccess():void
	{
		$now=new \DateTimeImmutable();
		$token=new ResetToken('token',$now->modify('+1 day'));
		$user=(new UserBuilder())->viaEmail()->confirmed()->build();
		$user->requestPasswordReset($token,$now);
		self::assertNotNull($user->getResetToken());
		$user->passwordReset($now, $hash='hash');
		self::assertNull($user->getResetToken());
		self::assertEquals($hash,$user->getPasswordHash());
				}

	public function testExpiredToken():void
	{
		$user=(new UserBuilder())->viaEmail()->confirmed()->build();
		$now=new \DateTimeImmutable();
		$token=new ResetToken('token',$now);
		$user->requestPasswordReset($token,$now);
		self::assertNotNull($user->getResetToken());

	}




}
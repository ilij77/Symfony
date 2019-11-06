<?php


namespace App\Tests\Unit\Model\User\Entity\User\Email;


use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
	public function testSuccess()
	{
		$user=(new UserBuilder())->viaEmail()->confirmed()->build();
		$user->requestEmailChanging($email=new Email('new@app.test'),$token='token');
		$user->confirmEmailChanging($token);

		self::assertEquals($email,$user->getEmail());
		self::assertNull($user->getNewEmailToken());
		self::assertNull($user->getNewEmail());


	}

	public function testNotRequest()
	{
		$user=(new UserBuilder())->viaEmail()->confirmed()->build();
		$this->expectExceptionMessage('Changing is not requested.');
		$user->confirmEmailChanging('token');

	}

	public function testIncorrect()
	{
		$user=(new UserBuilder())->viaEmail()->confirmed()->build();

		$user->requestEmailChanging($email=new Email('new@app.test'),'token');
		$this->expectExceptionMessage('Incorrect changing token.');
		$user->confirmEmailChanging('incorrect_token');


	}

}
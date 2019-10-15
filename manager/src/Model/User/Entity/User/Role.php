<?php


namespace App\Model\User\Entity\User;


use phpDocumentor\Reflection\Types\Boolean;
use Webmozart\Assert\Assert;

class Role
{
	private const  USER='ROLE_USER';
	private const  ADMIN='ROLE_ADMIN';
	private  $name;

	public function __construct(string  $name)
	{
		Assert::oneOf($name,[
			self::USER,
			self::ADMIN,
		]);
		$this->name=$name;


	}

	public static function user ():self
	{
		return new self(self::USER);

	}

	public static function admin ():self
	{
		return new self(self::ADMIN);

	}

	public function isUser():Bool
	{
		return $this->name===self::USER;

	}
	public function isAdmin():Bool
	{
		return $this->name===self::ADMIN;

	}

	public function isEqual(self $role):bool
	{
		return  $this->getName()===$role->getName();

	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}
}
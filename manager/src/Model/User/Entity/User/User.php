<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;



use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\Boolean;

class User
{

	private const  STATUS_NEW='new';
	private const  STATUS_WAIT='wait';
	private const  STATUS_ACTIVE='active';
	/**
	 * @var string
	 */
	private $id;
	/**
	 * @var \DateTimeImmutable
	 */
	private $date;
	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $passwordHash;

	/**
	 * @var string
	 */
	private $status;
	/**
	 * @var string
	 */
	private $confirmToken;
	/**
	 * @var Network[]|ArrayCollection
	 */
	private $networks;
	/**
	 * @var ResetToken|null
	 */
	private $resetToken;



	public function __construct(Id $id,\DateTimeImmutable $date)
	{
		$this->id = $id;
		$this->date = $date;
		$this->status=self::STATUS_NEW;
		$this->networks=new ArrayCollection();
	}

	public function signUpByEmail(Email $email,string  $hash,string $token):void
	{
		if (!$this->isNew()){
			throw new \DomainException('User is already signed up.');
		}
		$this->email=$email;
		$this->passwordHash=$hash;
		$this->confirmToken=$token;
		$this->status=self::STATUS_WAIT;


	}

	public function signUpByNetwork(string  $network, string  $identity):void
	{

		if (!$this->isNew()){
			throw new \DomainException('User is already signed up.');
		}
		$this->attachNetwork($network,$identity);
		$this->status=self::STATUS_ACTIVE;
	}

	private function attachNetwork(string $network, string $identity):void
	{
		foreach ($this->networks as $existing){
			if ($existing->isForNetwork($network)){
				throw new \DomainException('Network is already attached.');
			}
		}
		$this->networks->add(new Network($this,$network,$identity));
	}
	public function confirmSignUp():void
	{
		if (!$this->isWait()){
			throw new \DomainException('User is already confirmed.');
		}
		$this->status=self::STATUS_ACTIVE;
		$this->confirmToken=null;

	}

	public function isNew():bool
	{
		return $this->status===self::STATUS_NEW;

	}

	public function isWait():bool
	{
		return $this->status===self::STATUS_WAIT;

	}

	public function isActive():bool
	{
		return $this->status===self::STATUS_ACTIVE;

	}

	public function getId():Id
	{
		return $this->id;

	}

	/**
	 * @return Network[]|ArrayCollection
	 */
	public function getNetworks()
	{
		return $this->networks->toArray();
	}

	public function getDate():\DateTimeImmutable
	{
		return $this->date;

	}

	public function getEmail():Email
	{
		return $this->email;

	}

	public function getPasswordHash():?string
	{
		return $this->passwordHash;

	}

	/**
	 * @return string
	 */
	public function getConfirmToken(): ?string
	{
		return $this->confirmToken;
	}

	public function requestPasswordReset(ResetToken $token,\DateTimeImmutable $date):void
	{
		if (!$this->isActive()){
			throw new \DomainException('User is not active.');
		}
		if (!$this->email){
			throw new \DomainException('Email is not specified.');
		}
		if ($this->resetToken && !$this->resetToken->isExpiredTo($date)){
			throw new \DomainException('Resetting is already requested.');
		}
		$this->resetToken=$token;

	}

	/**
	 * @return ResetToken|null
	 */
	public function getResetToken(): ?ResetToken
	{
		return $this->resetToken;
	}

	public function passwordReset(\DateTimeImmutable $date, string  $hash):void
	{
		if (!$this->resetToken){
			throw new \DomainException('Resetting is not requested.');
		}
		if ($this->resetToken->isExpiredTo($date)){
			throw new \DomainException('Reset token is expired.');
		}
		$this->passwordHash=$hash;
		$this->resetToken=null;


	}


}
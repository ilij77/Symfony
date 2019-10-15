<?php


namespace App\Security;


use App\Model\User\Entity\User\User;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, EquatableInterface
{
	private $id;
	private $username;
	private $password;
	private $role;
	private $status;


	public function __construct(
		string $id,
	string $username,
	string $password,
	string $role,
	string $status
	)
	{

		$this->id = $id;
		$this->username = $username;
		$this->password = $password;
		$this->role = $role;
		$this->role = $status;
	}

	public function getId():string
	{
		return $this->id;

	}

	public function isActive():bool
	{
		return  $this->status ===User::STATUS_ACTIVE;

	}
	public function getUsername()
	{
		return $this->username;
	}
	public function getPassword()
	{
		return $this->password;
	}
	public function getRoles()
	{
		return $this->role;
	}

	public function getSalt()
	{
		return null;
		
	}

	public function eraseCredentials()
	{
		
	}

	public function isEqualTo(UserInterface $user):bool
	{
		if (!$user instanceof  self){
			return false;
		}
		return
		$this->id === $user->id &&
		$this->username === $user->username &&
		$this->password === $user->password &&
		$this->role === $user->role &&
		$this->status === $user->status;

	}

}
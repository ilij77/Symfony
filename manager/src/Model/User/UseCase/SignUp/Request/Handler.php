<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\User\Entity\Service\ConfirmTokenizer;
use App\Model\User\Entity\Service\ConfirmTokenSender;
use App\Model\User\Entity\Service\PasswordHasher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Flusher;


class Handler
{

	private $users;
	private $hasher;
	/**
	 * @var Flusher
	 */
	private $flusher;
	/**
	 * @var ConfirmTokenizer
	 */
	private $tokenizer;
	/**
	 * @var ConfirmTokenSender
	 */
	private $sender;

	public function __construct(UserRepository $users, PasswordHasher $hasher,Flusher $flusher,
								ConfirmTokenizer $tokenizer,ConfirmTokenSender $sender)
	{
		$this->users = $users;
		$this->hasher=$hasher;

		$this->flusher = $flusher;
		$this->tokenizer = $tokenizer;
		$this->sender = $sender;
	}
	public function handle(Command $command):void
	{
		$email=new Email($command->email);
		if ($this->users->hasByEmail($email)){
			throw new \DomainException('User already exists.');
		}
		$user=new User(
			Id::next(),
			new \DateTimeImmutable()
		);

		$user->signUpByEmail($email,
			$this->hasher->hash($command->password),
			$token=$this->tokenizer->generate()
		);
		$this->users->add($user);
		$this->sender->send($email,$token);
		$this->flusher->flush();





//		$email=mb_strtolower($command->email);
//		if ($this->em->getRepository(User::class)->findOneBy(['email'=>$email])){
//			throw new  \DomainException('User already exists.');
//		}
//		$user=new User(
//			Uuid::uuid4()->toString(),
//			new \DateTimeImmutable(),
//			$email,
//			password_hash($command->password,PASSWORD_ARGON2I)
//		);
//		$this->em->persist($user);
//		$this->em->flush();
//

	}

}
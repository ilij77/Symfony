<?php


namespace App\Model\User\UseCase\Reset\Request;


use App\Model\User\Entity\Service\ResetTokenizer;
use App\Model\User\Entity\Service\ResetTokenSender;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Flusher;
use App\Model\User\Entity\User\User;

class Handler
{
	/**
	 * @var UserRepository
	 */
	private $users;
	/**
	 * @var ResetTokenizer
	 */
	private $tokenizer;
	/**
	 * @var Flusher
	 */
	private $flusher;
	/**
	 * @var ResetTokenSender
	 */
	private $sender;

	public function __construct(
	UserRepository $users,
	ResetTokenizer $tokenizer,
	Flusher $flusher,
	ResetTokenSender $sender
	)
	{

		$this->users = $users;
		$this->tokenizer = $tokenizer;
		$this->flusher = $flusher;
		$this->sender = $sender;
	}

	public function handle(Command $command):void
	{

		$user=$this->users->getByEmail(new Email($command->email));
		$user->requestPasswordReset(
			$this->tokenizer->generate(),
			new \DateTimeImmutable()
		);
		$this->flusher->flush();
		$this->sender->send($user->getEmail(),$user->getResetToken());

	}

}
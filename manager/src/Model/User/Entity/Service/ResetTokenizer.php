<?php


namespace App\Model\User\Entity\Service;


use App\Model\User\Entity\User\ResetToken;
use Ramsey\Uuid\Uuid;
use function Sodium\add;

class ResetTokenizer
{
	/**
	 * @var \DateTimeImmutable
	 */
	private $interval;

	public function __construct(\DateTimeImmutable $interval)
	{

		$this->interval = $interval;
	}

	public function generate():ResetToken
	{
		return new ResetToken(
			Uuid::uuid4()->toString(),
			(new \DateTimeImmutable())->add($this->interval)
		);

	}

}
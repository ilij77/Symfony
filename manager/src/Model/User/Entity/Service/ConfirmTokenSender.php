<?php


namespace App\Model\User\Entity\Service;


use App\Model\User\Entity\User\Email;

interface ConfirmTokenSender
{
	public function send(Email $email, string $token):void ;

}
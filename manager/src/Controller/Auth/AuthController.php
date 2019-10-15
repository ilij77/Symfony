<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('app/auth/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

	/**
	 * @Route("/logout", name="app_logout", methods={"GET"})
	 */
	public function logout(): Response
	{
		// controller can be blank: it will never be executed!
		throw new \Exception('Don\'t forget to activate logout in security.yaml');
	}
}

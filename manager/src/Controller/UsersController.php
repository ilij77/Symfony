<?php


namespace App\Controller;

use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\Create\Command;
use App\Model\User\UseCase\Create\Form;
use App\Model\User\UseCase\Create\Handler;
use App\ReadModel\Filter\Filter;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{
	private const PER_PAGE=2;

	private $logger;

	public function __construct(LoggerInterface $logger)
	{

		$this->logger = $logger;
	}


	/**
	 * @Route("", name="users")
	 * @param  Request $request
	 * @param UserFetcher $fetcher
	 * @return Response
	 */
	public function index(Request $request,UserFetcher $fetcher):Response
	{
		$filter=new Filter();
		$form=$this->createForm(\App\ReadModel\Filter\Form::class,$filter);
		$form->handleRequest($request);
		$pagination=$fetcher->all(
			$filter,
			$request->query->getInt('page',1),
			self::PER_PAGE,
			$request->query->get('sort','date'),
			$request->query->get('direction','desc'));
		return  $this->render('app/users/index.html.twig', [
		'pagination'=>$pagination,
		'form'=>$form->createView(),
		]);
		
	}

	/**
	 * @Route("/create",name="users.create")
	 * @param Request $request
	 * @param Handler $handler
	 * @return Response
	 */
	public function create(Request $request, Handler $handler):Response
	{
		$command=new Command();
		$form=$this->createForm(Form::class,$command);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			try{
				$handler->handle($command);
				return $this->redirectToRoute('users');
			}catch (\DomainException $e){
				$this->logger->error($e->getMessage(),['exception'=>$e]);
				$this->addFlash('error',$e->getMessage());
			}
		}
		return $this->render('app/users/create.html.twig',['form'=>$form->createView()]);

	}

	/**
	 * @Route("/{id}/edit",name="users.edit")
	 * @param User $user
	 * @param Request $request
	 * @param \App\Model\User\UseCase\Edit\Handler $handler
	 * @return Response
	 */
	public function edit(User $user,Request $request, \App\Model\User\UseCase\Edit\Handler $handler):Response
	{
		$command=\App\Model\User\UseCase\Edit\Command::fromUser($user);
		$form=$this->createForm(\App\Model\User\UseCase\Edit\Form::class,$command);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			try{
				$handler->handle($command);
				return $this->redirectToRoute('users.show',['id'=>$user->getId()]);
			}catch (\DomainException $e){
				$this->logger->error($e->getMessage(),['exception'=>$e]);
				$this->addFlash('error',$e->getMessage());
			}
		}
		return $this->render('app/users/edit.html.twig',['user'=>$user,'form'=>$form->createView()]);

	}

		/**
	 * @Route("/{id}/role",name="users.role")
	 * @param User $user
	 * @param Request $request
	 * @param \App\Model\User\UseCase\Role\Handler $handler
	 * @return  Response
	 */

	public function role(User $user,Request $request,\App\Model\User\UseCase\Role\Handler $handler):Response
	{
		if ($user->getId()->getValue() === $this->getUser()->getId()){
			$this->addFlash('error','Unable to change role yourself.');
			return $this->redirectToRoute('users.show',['id'=>$user->getId()]);
		}

		$command=\App\Model\User\UseCase\Role\Command::fromUser($user);
		$form=$this->createForm(\App\Model\User\UseCase\Role\Form::class,$command);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			try{
				$handler->handle($command);
				return $this->redirectToRoute('users.show',['id'=>$user->getId()]);
			}catch (\DomainException $e){
				$this->logger->error($e->getMessage(),['exception'=>$e]);
				$this->addFlash('error',$e->getMessage());
			}
		}
		return $this->render('app/users/role.html.twig',[
			'user'=>$user,
			'form'=>$form->createView(),
		]);

	}
	/**
	 * @Route("/{id}/confirm",name="users.confirm",methods={"POST"})
	 * @param User $user
	 * @param Request $request
	 * @param \App\Model\User\UseCase\SignUp\Confirm\Manual\Handler $handler
	 * @return Response
	 */
	public function confirm(User $user, Request $request, \App\Model\User\UseCase\SignUp\Confirm\Manual\Handler $handler):Response
	{
		if (!$this->isCsrfTokenValid('confirm',$request->request->get('token'))){
			return $this->redirectToRoute('users.show',['id'=>$user->getId()]);
		}
		$command=new \App\Model\User\UseCase\SignUp\Confirm\Manual\Command($user->getId()->getValue());


		try{
			$handler->handle($command);
		}catch (\DomainException $e){
			$this->logger->error($e->getMessage(),['exception'=>$e]);
			$this->addFlash('error',$e->getMessage());
	}
	return $this->redirectToRoute('users.show',['id'=>$user->getId()]);


	}
	/**
	 * @Route("/{id}/activate", name="users.activate",methods={"POST"})
	 * @param User $user
	 * @param Request $request
	 * @param \App\Model\User\UseCase\Activate\Handler $handler
	 * @return Response
	 */
	public function activate(User $user, Request $request,\App\Model\User\UseCase\Activate\Handler $handler):Response
	{
		if (!$this->isCsrfTokenValid('activate',$request->get('token'))){
			return $this->redirectToRoute('users.show',['id'=>$user->getId()]);
		}
		$command=new \App\Model\User\UseCase\Activate\Command($user->getId()->getValue());
		try{
			$handler->handle($command);
		}catch (\DomainException $e){
			$this->logger->error($e->getMessage(),['exception'=>$e]);
			$this->addFlash('error',$e->getMessage());
		}
		return $this->redirectToRoute('users.show',['id'=>$user->getId()]);

	}

	/**
	 * @Route("/{id}/block",name="users.block",methods={"POST"})
	 * @param User $user
	 * @param Request $request
	 * @param \App\Model\User\UseCase\Block\Handler $handler
	 * @return Response
	 */
	public function block(User $user, Request $request,\App\Model\User\UseCase\Block\Handler $handler):Response
	{
		if (!$this->isCsrfTokenValid('block',$request->get('token'))){
			return $this->redirectToRoute('users.show',['id'=>$user->getId()]);
		}
		if ($user->getId()->getValue()===$this->getUser()->getId()){
			$this->addFlash('error','Unable to block yourself.');
			return $this->redirectToRoute('users.show',['id'=>$user->getId()]);
		}
		$command=new \App\Model\User\UseCase\Block\Command($user->getId()->getValue());

		try{
			$handler->handle($command);
		}catch (\DomainException $e){
			$this->logger->error($e->getMessage(),['exception'=>$e]);
			$this->addFlash('error',$e->getMessage());
		}
		return $this->redirectToRoute('users.show',['id'=>$user->getId()]);

	}

	/**
	 * @Route("/{id}", name="users.show")
	 * @param  User $user

	 * @return Response
	 */
	public function show(User $user):Response
	{

		return  $this->render('app/users/show.html.twig', compact('user'));

	}

}
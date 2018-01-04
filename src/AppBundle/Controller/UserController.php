<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserController extends BaseController
{
	protected $username;
	
	protected $password;
	
	protected $email;
	
	
	/**
	 * @param Request $request
	 * @return token
	 * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 * @ApiDoc(
     *  description="Register new user",
	 *  parameters = {
	 *      { "name" = "username", "dataType"="string", "required"=true, "description"="user username" },
	 *		{ "name" = "password", "dataType"="string", "required"=true, "description"="user password" },
	 *		{ "name" = "email", "dataType"="string", "required"=true, "description"="useremail" }
	 *  },
	 *	statusCode={
	 *		200 = "Returned when successful",
	 *      400 = "Returned when parameter is missing"
	 * 	}
     * )
	 * @Rest\Post("api/register")
	 */
    public function registerAction(Request $request)
    {
		$data = $request->request->all();
		
		$this->username = (isset($data['username']))?$data['username']:null;
		$this->password = (isset($data['password']))?$data['password']:null;
		$this->email = (isset($data['email']))?$data['email']:null;
		
		if(!$this->username || !$this->password || !$this->email){
			throw new BadRequestHttpException('username, password and email is required');
		}
		
		$userManager = $this->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($this->username)||$userManager->findUserByEmail($this->email);
		
		if($user){
			throw new BadRequestHttpException('username or email allready exist');
		}
		
		$user = $userManager->createUser();
		$user->setUsername($this->username);
		$user->setEmail($this->email);
		$user->setPlainPassword($this->password);
		$user->setEnabled(true);
		$userManager->updateUser($user);
		
		return $this->generateToken();
    }
	
	/**
	 * @param Request $request
	 * @return token
	 * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 * @throws Symfony\Component\Security\Core\Exception\BadCredentialsException
	 * @ApiDoc(
     *  description="Login existing user",
	 *  parameters = {
	 *      { "name" = "username", "dataType"="string", "required"=true, "description"="user username" },
	 *		{ "name" = "password", "dataType"="string", "required"=true, "description"="user password" }
	 *  },
	 *	statusCode={
	 *		200 = "Returned when successful",
	 *      400 = "Returned when parameter is missing"
	 * 	}
     * )
	 * @Rest\Post("api/login")
	 */
	public function loginAction(Request $request)
	{
		$data = $request->request->all();
		
		$this->username = (isset($data['username']))?$data['username']:null;
		$this->password = (isset($data['password']))?$data['password']:null;
		
        if(!$this->username || !$this->password){
			throw new BadRequestHttpException('username and password is required');
		}
		
		$userManager = $this->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($this->username);

        if (!$user) {
            throw new BadRequestHttpException('username doesn\'t exist');
        }
 
        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $this->password);
 
        if (!$isValid) {
            throw new BadCredentialsException('Wrong username or password');
        }
		
		return $this->generateToken();
	}
	
	/*
	 * @return token
	 */
	protected function generateToken()
	{
		$grantRequest = new Request(array(
			'client_id'  => $this->container->getParameter('client_id'),
			'client_secret' => $this->container->getParameter('client_secret'),
			'grant_type' => 'password',
			'username' => $this->username,
			'password' => $this->password
		));

		$tokenResponse = $this->get('fos_oauth_server.server')->grantAccessToken($grantRequest);
		
		return $tokenResponse;
	}
}
<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $username = null;
	
	private $password = null;
	
	private $email = null; 

	public function __construct()
    {
        $this->username = static::generateRandomString(7);
		$this->email = $this->username."@mail.com";
		$this->password = $this->username;
		
		$parameters = ['username'=>"testuser1",'password'=>"testuser1",'email'=>"testuser1@mail.com"];
        $response = $this->post("POST", "/api/register", $parameters, [], [], []);
    }

	private static function generateRandomString($length){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	private function post($method , $uri, $parameters = [], $files = [], $server = [], $content = [])
    {
        $content = json_encode($content);
        $client = static::createClient();
        $client->request($method, $uri, $parameters, $files, $server, $content);

        return $client->getResponse();
    }
	
	// Register new user with right parameters
	public function testRegisterNewUserWithRightParameters()
	{
		$parameters = ['username'=>$this->username,'password'=>$this->password,'email'=>$this->email];
        $response = $this->post("POST", "/api/register", $parameters, [], [], []);
		
		$res_array = (array)json_decode($response->getContent());
		
		$this->assertArrayHasKey('access_token', $res_array);
	}
	
	// Register new user with existed username
	public function testRegisterNewUserWithExistedUsername()
	{
		$parameters = ['username'=>"testuser1",'password'=>$this->password,'email'=>"testmail1@mail.com"];
        $response = $this->post("POST", "/api/register", $parameters, [], [], []);
		
		$res_array = (array)json_decode($response->getContent());
		
		$this->assertEquals(400, $response->getStatusCode());
	}
	
	// Register new user with existed email
	public function testRegisterNewUserWithExistedEmail()
	{
		$parameters = ['username'=>"testuser",'password'=>$this->password,'email'=>"testuser1@mail.com"];
        $response = $this->post("POST", "/api/register", $parameters, [], [], []);
		
		$res_array = (array)json_decode($response->getContent());
		
		$this->assertEquals(400, $response->getStatusCode());
	}
	
	// Register new user with missing parameters
	public function testRegisterNewUserWithMissingParameters()
	{
		$parameters = ['username'=>'newuser','password'=>'123456'];
        $response = $this->post("POST", "/api/register", $parameters, [], [], []);
		
		$res_array = (array)json_decode($response->getContent());
		
		$this->assertEquals(400, $response->getStatusCode());
	}
	
	// User login with right credentials 
	public function testLoginWithRightCredentials()
    {
        $parameters = ['username'=>"testuser1",'password'=>"testuser1"];
        $response = $this->post("POST", "/api/login", $parameters, [], [], []);
		
		$res_array = (array)json_decode($response->getContent());

		$this->assertArrayHasKey('access_token', $res_array);
    }
	
	// User login with wrong credentials
	public function testLoginWithWrongCredentials()
    {
        $parameters = ['username'=>"testuser1",'password'=>'12345'];
        $response = $this->post("POST", "/api/login", $parameters, [], [], []);

        $this->assertEquals(500, $response->getStatusCode());
    }
	
	// User login with missing parameters
	public function testLoginWithMissingParameters()
	{
		$parameters = ['username'=>$this->username];
        $response = $this->post("POST", "/api/login", $parameters, [], [], []);

        $this->assertEquals(400, $response->getStatusCode());
	}
	
	
}
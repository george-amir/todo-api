<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListControllerTest extends WebTestCase
{
	private $token = null;
	
	private $listId = null;
	
	private $listItemId = null;
	
	public function __construct(){
		$parameters = ['username'=>"testuser1",'password'=>"testuser1",'email'=>"testuser1@mail.com"];
        $response = $this->post("POST", "/api/login", $parameters, [], [], []);
		$res_array = (array)json_decode($response->getContent());
		
		if(!array_key_exists("access_token",$res_array)){
			$response = $this->post("POST", "/api/register", $parameters, [], [], []);
			$res_array = (array)json_decode($response->getContent());
		}
		$this->token = $res_array['access_token'];
		
		$content = ['title'=>'test list record'];
        $response = $this->post("POST", "/api/list/create", $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		$res_array = (array)json_decode($response->getContent());
		
		if(array_key_exists("id",$res_array)){
			$this->listId = (string)$res_array['id'];
		}
		
		$content = ['description' => 'test list item'];
        $response = $this->post("POST", "/api/list/".$this->listId."/item", $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		$res_array = (array)json_decode($response->getContent());
		
		if(array_key_exists("items",$res_array)){
			$this->listItemId = end($res_array['items'])->id;
		}
	}
	
	private function post($method , $uri, $parameters = [], $files = [], $server = [], $content = [])
    {
        $content = json_encode($content);
        $client = static::createClient();
        $client->request($method, $uri, $parameters, $files, $server, $content);

        return $client->getResponse();
    }
	
	// Create new list with right parameters
	public function testCreateNewListWithRightParameter()
	{
		$content = ['title'=>'test list record'];
        $response = $this->post("POST", "/api/list/create", $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		$res_array = (array)json_decode($response->getContent());
		
		if(array_key_exists("id",$res_array)){
			$this->listId = (string)$res_array['id'];
		}
		
		$this->assertArrayHasKey('id', $res_array);
	}
	
	// Create new list with missing parameters
	public function testCreateNewListWithMissingParameter()
	{
		$content = [];
        $response = $this->post("POST", "/api/list/create", $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		$res_array = (array)json_decode($response->getContent());
		
		$this->assertEquals(400, $response->getStatusCode());
	}
	
	// Get all user lists
	public function testGetAllUserLists()
	{
		$content = [];
        $response = $this->post("GET", "/api/list", $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		
		$this->assertEquals(200, $response->getStatusCode());
	}
	
	// access spesific list owned by the user
	public function testGetSpesificListOwnedByUser()
	{
		$content = [];
        $response = $this->post("GET", "/api/list/".$this->listId, $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		$res_array = (array)json_decode($response->getContent());
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertArrayHasKey('id', $res_array);
	}
	
	// access spesific list doesn't owned by the user
	public function testGetSpesificListDoesntOwnedByUser()
	{
		$content = [];
        $response = $this->post("GET", "/api/list/1000", $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		
		$this->assertEquals(403, $response->getStatusCode());
	}
	
	// Edit existing list with right parameters
	public function testEditSpesificListWithRightParameters()
	{
		$content = ['title' => 'test edited list'];
        $response = $this->post("PATCH", "/api/list/".$this->listId."/edit", [], [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], $content);
		$res_array = (array)json_decode($response->getContent());
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals($res_array['title'], 'test edited list');
	}
	
	// Edit existing list with missing parameters
	public function testEditSpesificListWithMissingParameters()
	{
		$content = [];
        $response = $this->post("PATCH", "/api/list/".$this->listId."/edit", [], [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], $content);
		$res_array = (array)json_decode($response->getContent());
		
		$this->assertEquals(400, $response->getStatusCode());
	}
	
	// Create new item in an existing list with right parameters
	public function testCreateListItemWithRightParameters()
	{
		$content = ['description' => 'test list item'];
        $response = $this->post("POST", "/api/list/".$this->listId."/item", $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		$res_array = (array)json_decode($response->getContent());
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals($res_array['id'], $this->listId);
	}
	
	// Create new item in an existing list with missing parameters
	public function testCreateListItemWithMissingParameters()
	{
		$content = [];
        $response = $this->post("POST", "/api/list/".$this->listId."/item", $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		
		$this->assertEquals(400, $response->getStatusCode());
	}
	
	// Create an item in a list doesn't exist
	public function testCreateListItemInAListDoesntOwnedByTheUser()
	{
		$content = ['description' => 'test list item'];
        $response = $this->post("POST", "/api/list/1000/item", $content, [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], []);
		
		$this->assertEquals(403, $response->getStatusCode());
	}
	
	// Edit item in an existing list with right parameters
	public function testEditSpesificListItemWithRightParameters()
	{
		$content = ['description' => 'test edited list item'];
        $response = $this->post("PATCH", "/api/list/".$this->listId."/item/".$this->listItemId.'/edit', [], [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], $content);
		
		$this->assertEquals(200, $response->getStatusCode());
	}
	
	// Edit item doesn't owned by the user
	public function testEditSpesificListItemDoesntOwnedByTheUser()
	{
		$content = ['title' => 'test edited list'];
        $response = $this->post("PATCH", "/api/list/".$this->listId."/item/1000/edit", [], [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], $content);
		
		$this->assertEquals(404, $response->getStatusCode());
	}
	
	// Edit item in an existing list with missing parameters
	public function testEditSpesificListItemWithMissingParameters()
	{
		$content = [];
        $response = $this->post("PATCH", "/api/list/".$this->listId."/edit", [], [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], $content);
		
		$this->assertEquals(400, $response->getStatusCode());
	}
	
	// Delete item in an existing list with right parameters
	public function testDeleteSpesificListItemWithRightParameters()
	{
		$content = [];
        $response = $this->post("Delete", "/api/list/".$this->listId."/item/".$this->listItemId.'/delete', [], [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], $content);
		
		$this->assertEquals(204, $response->getStatusCode());
	}
	
	// Delete item doesn't owned by the user
	public function testDeleteSpesificListItemDoesntOwnedByTheUser()
	{
		$content = [];
        $response = $this->post("Delete", "/api/list/".$this->listId."/item/1000/delete", [], [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], $content);
		
		$this->assertEquals(404, $response->getStatusCode());
	}
		
	// Delete existing list owned by the user
	public function testDeleteSpesificListOwnedByTheUser()
	{
		$content = [];
        $response = $this->post("Delete", "/api/list/".$this->listId.'/delete', [], [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], $content);
		
		$this->assertEquals(204, $response->getStatusCode());
	}
	
	// Delete existing list doesn't owned by the user
	public function testDeleteSpesificListDoesntOwnedByTheUser()
	{
		$content = [];
        $response = $this->post("Delete", "/api/list/1000/delete", [], [], ['HTTP_Authorization' => 'Bearer '.$this->token, 'Content-Type' => 'application/json'], $content);
		
		$this->assertEquals(403, $response->getStatusCode());
	}
	
}
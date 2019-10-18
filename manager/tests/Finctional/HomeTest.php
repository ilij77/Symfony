<?php


namespace App\Tests\Finctional;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{


	public function testGuest()
	{
		$client=static::createClient();
		$client->request('GET','/');
		$this->assertSame(302,$client->getResponse()->getStatusCode());
		$this->assertSame('http://localhost/login',$client->getResponse()->headers->get('Location'));


	}

	public function testSuccess()
	{
		$client = static::createClient([], [
			'PHP_AUTH_USER' => 'admin@app.test',
			'PHP_AUTH_PW' => 'password',
		]);
		$crawler = $client->request('GET', '/');

		$this->assertSame(200, $client->getResponse()->getStatusCode());
		$this->assertContains('Hello', $crawler->filter('h1')->text());

	}

}
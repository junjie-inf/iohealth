<?php

class AuthTest extends TestCase {

	public function testLogin()
	{
		$this->action('POST', 'AuthController@login', array(
			'email' => 'admin@demo.com', 
			'password' => '123456'
		));
		
		$this->assertResponseOk();
		
		$this->assertTrue( Auth::check() );
	}
	
	public function testCheck()
	{
		$this->be(User::find(1));
		
		$this->action('POST', 'AuthController@check');
		
		$this->assertResponseOk();
	}
	
	public function testLogout()
	{
		$this->be(User::find(1));

		$this->action('GET', 'AuthController@logout');
		
		$this->assertFalse( Auth::check() );
	}

}
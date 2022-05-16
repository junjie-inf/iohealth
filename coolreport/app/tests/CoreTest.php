<?php

class CoreTest extends TestCase {

	public function testEnvironment()
	{
		$environment = App::environment();
		
		$this->assertEquals( 'testing', $environment );
	}

}
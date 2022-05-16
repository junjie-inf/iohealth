<?php

class CreateRoles {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles', function($table) 
		{
			$table->increments('id');
			
			$table->string('title', 128);
			
			$table->timestamps();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('roles');
	}

}
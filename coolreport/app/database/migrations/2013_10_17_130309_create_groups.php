<?php

class CreateGroups {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('groups', function($table) 
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
		Schema::drop('groups');
	}

}
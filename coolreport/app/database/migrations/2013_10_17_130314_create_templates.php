<?php

class CreateTemplates {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('templates', function($table) 
		{
			$table->increments('id');
			
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users');
			
			$table->string('title', 256);
			
			$table->text('fields');
			
			$table->timestamps();
			
			$table->softDeletes();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('templates');
	}

}
<?php

class CreateDashboards {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dashboards', function($table) 
		{
			// Keys
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

			// Title
			$table->string('title');
			
			// Content
			$table->text('datum');
			
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
		Schema::drop('dashboards');
	}

}
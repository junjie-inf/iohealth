<?php

class CreateComments {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function($table) 
		{
			$table->increments('id');
			$table->integer('report_id')->unsigned()->index();
			$table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			
			$table->text('content');
			
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
		Schema::drop('comments');
	}

}
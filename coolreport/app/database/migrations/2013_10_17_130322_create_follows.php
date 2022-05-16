<?php

class CreateFollows {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('follows', function($table) 
		{
			$table->increments('id');
			$table->enum('followable_type', array('Group', 'Report', 'Role', 'User'));
			$table->integer('followable_id')->unsigned()->index();
			$table->integer('user_id')->unsigned()->index();
			$table->timestamp('created_at')->default(DB::raw('now()::timestamp(0)'));
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('follows');
	}

}
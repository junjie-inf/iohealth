<?php

class CreateVisits {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('visits', function($table) 
		{
			$table->increments('id');
			$table->enum('visitable_type', array('Report', 'Attachment', 'User'));
			$table->string('visitable_id', 64)->index();
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
		Schema::drop('visits');
	}

}
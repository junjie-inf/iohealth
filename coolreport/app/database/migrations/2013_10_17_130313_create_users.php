<?php

class CreateUsers {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table) 
		{
			$table->increments('id');
			$table->integer('role_id')->unsigned()->index();
			$table->foreign('role_id')->references('id')->on('roles');
			$table->integer('group_id')->unsigned()->index();
			$table->foreign('group_id')->references('id')->on('groups');
			
			$table->string('email', 128)->unique();
			$table->string('password', 64);
			
			$table->boolean('admin')->default(false);
			$table->boolean('gossip')->default(false);
			
			$table->string('firstname', 64);
			$table->string('surname', 64);
			
			$table->date('lastlogin')->nullable();
			
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
		Schema::drop('users');
	}

}
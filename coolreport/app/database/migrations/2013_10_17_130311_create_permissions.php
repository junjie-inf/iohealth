<?php

class CreatePermissions {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permissions', function($table) 
		{
			$table->increments('id');
			$table->integer('role_id')->unsigned()->index();
			$table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
			
			$table->enum('type', Config::get('local.permission.types'));
			$table->enum('value', Config::get('local.permission.values'));
			
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
		Schema::drop('permissions');
	}

}
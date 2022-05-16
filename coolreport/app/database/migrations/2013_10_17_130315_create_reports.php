<?php

class CreateReports {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reports', function($table) 
		{
			// Keys
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->integer('template_id')->unsigned()->index();
			$table->foreign('template_id')->references('id')->on('templates');
			
			// Content
			$table->text('datum');
			
			// Geolocation
			$table->decimal('latitude', 9, 6);
			$table->decimal('longitude', 9, 6);
			$table->decimal('accuracy', 9, 6);
			
			// Geocoding
			$table->text('geodata')->nullable();
			
			// Mobile info
			$table->string('device', 128)->nullable();
			$table->string('platform', 128)->nullable();
			$table->string('version', 128)->nullable();
			$table->string('screen', 16)->nullable();
			
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
		Schema::drop('reports');
	}

}
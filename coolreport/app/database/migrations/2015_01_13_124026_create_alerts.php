<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlerts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alerts', function($table) 
		{
			// Keys
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			
			// Title
			$table->string('title');
							
			$table->timestamps();
			
			$table->softDeletes();
		});

		// Content
		DB::statement('ALTER TABLE alerts ADD COLUMN conditions jsonb NOT NULL');
		DB::statement('ALTER TABLE alerts ADD COLUMN actions jsonb NOT NULL');
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alerts');
	}

}

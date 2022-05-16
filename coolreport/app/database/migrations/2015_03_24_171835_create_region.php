<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegion extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('regions', function($table) 
		{
			// Keys
			$table->increments('id');
			$table->integer('map_id')->unsigned()->index();
			$table->foreign('map_id')->references('id')->on('maps')->onDelete('cascade');
			
			// Title
			$table->string('title');
							
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
		Schema::drop('regions');
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMap extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maps', function($table) 
		{
			// Keys
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			
			// Title
			$table->string('title');

			// Parametros de mapa
			$table->boolean('politic')->default(false);
			$table->boolean('proccesed')->default(false);
							
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
		Schema::drop('maps');
	}

}

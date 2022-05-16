<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharedObjects extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shared_objects', function($table)
		{
			// Keys
			$table->increments('id');
			$table->string('hash', 32)->unique();
			$table->enum('object_type', array('dashboard', 'dashitem'));
			$table->integer('object_id')->unsigned();
			$table->boolean('enabled')->default(false);

			// object_type & object_id are unique
			$table->unique( array('object_type', 'object_id') );

			$table->timestamps();

			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shared_objects');
	}

}

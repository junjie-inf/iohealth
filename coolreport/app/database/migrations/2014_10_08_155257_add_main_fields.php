<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMainFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('reports', function(Blueprint $table)
		{
			$table->dateTime('main_date')->nullable();
		});
		
		Schema::table('templates', function(Blueprint $table)
		{
			$table->text('settings')->default('{}');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('reports', function(Blueprint $table)
		{
			$table->dropColumn('main_date');
		});
		
		Schema::table('templates', function(Blueprint $table)
		{
			$table->dropColumn('settings');
		});
	}

}

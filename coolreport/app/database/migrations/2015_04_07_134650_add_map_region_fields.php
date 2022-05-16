<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMapRegionFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('maps', function(Blueprint $table)
		{
			// Fichero json o geojson
			$table->string('hash', 64);
		});
		
		DB::statement('ALTER TABLE regions ADD COLUMN geo geography');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('maps', function(Blueprint $table)
		{
			$table->dropColumn('hash');
		});
		
		DB::statement('ALTER TABLE regions DROP COLUMN geo geography');
	}

}

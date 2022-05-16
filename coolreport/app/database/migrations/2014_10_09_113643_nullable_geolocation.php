<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullableGeolocation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE reports ALTER COLUMN latitude DROP NOT NULL');
		DB::statement('ALTER TABLE reports ALTER COLUMN longitude DROP NOT NULL');
		DB::statement('ALTER TABLE reports ALTER COLUMN accuracy DROP NOT NULL');
		DB::statement('ALTER TABLE reports ALTER COLUMN geodata DROP NOT NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE reports ALTER COLUMN latitude SET NOT NULL');
		DB::statement('ALTER TABLE reports ALTER COLUMN longitude SET NOT NULL');
		DB::statement('ALTER TABLE reports ALTER COLUMN accuracy SET NOT NULL');
		DB::statement('ALTER TABLE reports ALTER COLUMN geodata SET NOT NULL');
	}

}

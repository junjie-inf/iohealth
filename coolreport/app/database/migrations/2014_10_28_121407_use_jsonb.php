<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UseJsonb extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE reports ALTER COLUMN datum TYPE jsonb USING datum::jsonb');
		DB::statement('ALTER TABLE reports ALTER COLUMN geodata TYPE jsonb USING geodata::jsonb');
		
		DB::statement('ALTER TABLE templates ALTER COLUMN fields TYPE jsonb USING fields::jsonb');
		DB::statement('ALTER TABLE templates ALTER COLUMN settings DROP DEFAULT');
		DB::statement('ALTER TABLE templates ALTER COLUMN settings TYPE jsonb USING settings::jsonb');
		
		DB::statement('ALTER TABLE dash_items ALTER COLUMN datum TYPE jsonb USING datum::jsonb');
		DB::statement('ALTER TABLE dashboards ALTER COLUMN datum TYPE jsonb USING datum::jsonb');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE reports ALTER COLUMN datum TYPE text USING datum::text');
		DB::statement('ALTER TABLE reports ALTER COLUMN geodata TYPE text USING geodata::text');
		
		DB::statement('ALTER TABLE templates ALTER COLUMN fields TYPE text USING fields::text');
		DB::statement('ALTER TABLE templates ALTER COLUMN settings TYPE text USING settings::text');
		DB::statement("ALTER TABLE templates ALTER COLUMN settings ADD DEFAULT '{}'");
		
		DB::statement('ALTER TABLE dash_items ALTER COLUMN datum TYPE text USING datum::text');
		DB::statement('ALTER TABLE dashboards ALTER COLUMN datum TYPE text USING datum::text');
	}

}

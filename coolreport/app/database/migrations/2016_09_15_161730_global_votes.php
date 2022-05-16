<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GlobalVotes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('votes', function(Blueprint $table)
		{
			$table->enum('votable_type', array('Report', 'Comment'));
		});

		DB::statement('ALTER TABLE votes DROP CONSTRAINT votes_report_id_foreign');
		DB::statement('ALTER TABLE votes RENAME report_id TO votable_id');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('votes', function(Blueprint $table)
		{
			$table->dropColumn('votable_id');
			$table->dropColumn('votable_type');
		});
	}

}

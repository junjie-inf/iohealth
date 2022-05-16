<?php

class GroupsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('groups')->insert(array(
			array(
				'title' => 'Default group', 
				'created_at' => new DateTime, 
				'updated_at' => new DateTime
			),
		));
	}
}
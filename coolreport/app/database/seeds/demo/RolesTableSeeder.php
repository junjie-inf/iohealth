<?php

class RolesTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('roles')->insert(array(
			array(
				'title' => 'Administrator', 
				'created_at' => new DateTime, 
				'updated_at' => new DateTime
			),
			array(
				'title' => 'Moderator', 
				'created_at' => new DateTime, 
				'updated_at' => new DateTime
			),
			array(
				'title' => 'User', 
				'created_at' => new DateTime, 
				'updated_at' => new DateTime
			),
		));
	}
}
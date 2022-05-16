<?php

class UsersTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('users')->insert(array(
			array(
				'role_id' => 1,
				'group_id' => 1,
				'email' => 'admin@demo.com', 
				'password' => Hash::make('123456'), 
				'admin' => true, 
				'gossip' => false, 
				'firstname' => 'Administrator User', 
				'surname' => 'Demo', 
				'created_at' => new DateTime, 
				'updated_at' => new DateTime
			)
		));
	}
}
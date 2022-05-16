<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		//Eloquent::unguard();

		$this->command->info('--------------------------------');
		$this->command->info('-   Seeding initial database   -');
		$this->command->info('--------------------------------');
				
		// ---

		$this->command->info('Seeding table: groups...');
		$this->call('GroupsTableSeeder');
		
		$this->command->info('Seeding table: roles...');
		$this->call('RolesTableSeeder');
		
		$this->command->info('Seeding table: permissions...');
		$this->call('PermissionsTableSeeder');
		
		$this->command->info('Seeding table: users...');
		$this->call('UsersTableSeeder');
		
		$this->command->info('Seeding table: templates...');
		$this->call('TemplatesTableSeeder');
		
		$this->command->info('--- Tables seeded! ---'."\n");
	}

}
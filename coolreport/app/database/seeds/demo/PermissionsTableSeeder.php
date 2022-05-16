<?php

class PermissionsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('permissions')->insert(array(
			// Permissions 'administradores'
			array('role_id' => 1, 'type' => 'VIEW_REPORTS', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 1, 'type' => 'MANAGE_REPORTS', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 1, 'type' => 'ADD_COMMENTS', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 1, 'type' => 'MODIFY_COMMENTS', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 1, 'type' => 'ADD_VOTES', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			//--
			array('role_id' => 1, 'type' => 'VIEW_USERS', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 1, 'type' => 'GRANT_USERS', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 1, 'type' => 'VIEW_GROUPS', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 1, 'type' => 'GRANT_GROUPS', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 1, 'type' => 'VIEW_ROLES', 'value' => 'ALL', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			
			// Permissions 'moderadores'
			array('role_id' => 2, 'type' => 'VIEW_REPORTS', 'value' => 'GROUP', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 2, 'type' => 'MANAGE_REPORTS', 'value' => 'GROUP', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 2, 'type' => 'ADD_COMMENTS', 'value' => 'GROUP', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 2, 'type' => 'MODIFY_COMMENTS', 'value' => 'GROUP', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 2, 'type' => 'ADD_VOTES', 'value' => 'GROUP', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			
			// Permissions 'usuarios'
			array('role_id' => 3, 'type' => 'VIEW_REPORTS', 'value' => 'GROUP', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 3, 'type' => 'MANAGE_REPORTS', 'value' => 'OWN', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 3, 'type' => 'ADD_COMMENTS', 'value' => 'GROUP', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 3, 'type' => 'MODIFY_COMMENTS', 'value' => 'OWN', 'created_at' => new DateTime, 'updated_at' => new DateTime),
			array('role_id' => 3, 'type' => 'ADD_VOTES', 'value' => 'GROUP', 'created_at' => new DateTime, 'updated_at' => new DateTime),
		));
	}
}
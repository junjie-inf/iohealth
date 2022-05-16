<?php

class TemplatesTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('templates')->insert(array(
			array(
				'user_id' => 1,
				'title' => 'Alert',
				'fields' => '[{"id": "54cb5c0b1119c0", "show": "show", "type": "text", "label": "Subject", "required": "required"}, {"id": "54cb5c0b1119c1", "type": "textarea", "label": "Body"}]',
				'settings' => '{"date": "_created_at", "visible": false, "geolocation": false}',
				'created_at' => new DateTime, 
				'updated_at' => new DateTime
			),
		));			
	}
}
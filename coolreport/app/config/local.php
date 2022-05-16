<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Site
	|--------------------------------------------------------------------------
	*/

	'site' => array(
		'name' => 'IOHealth',
		'description' => 'IOHealth.',
		'keywords' => 'IOHealth, reports, geolocation',
		'email' => 'juanmiguel.gomez@uc3m.es',
		'version' => '1.0 Beta',
	),
	
	/*
	|--------------------------------------------------------------------------
	| Company
	|--------------------------------------------------------------------------
	*/
	
	'company' => array(
		'name' => '',
		'url' => '',
	),
	
	/*
	|--------------------------------------------------------------------------
	| Settings
	|--------------------------------------------------------------------------
	*/
	
	'enabled' => array(
		'signup' => true,
	),

	'uploads' => array(
		'path' => 'uploads',
		'size' => 100 * 1024, // 100 MB
	),
	
	'map' => array(
		'default' => array(
			'latitude' => 40.416636872888716,
			'longitude' => -3.7038122420199215,
		),
	),
	
	/*
	|--------------------------------------------------------------------------
	| Mobile
	|--------------------------------------------------------------------------
	*/
	
	'app' => array(
		'version' => '0.1.0',
	),
	
	/*
	|--------------------------------------------------------------------------
	| Visits statistics
	|--------------------------------------------------------------------------
	*/
	
	'stats' => array(
		'visit' => array(
			'delay' => 60 // Seconds
		),
	),
	
	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	*/
	
	'permission' => array(
		'types' => array(
			// Reports
			'VIEW_REPORTS',
			'MANAGE_REPORTS',

			// Comments
			'ADD_COMMENTS',
			'MODIFY_COMMENTS',

			// Votes
			'ADD_VOTES',

			// --
			
			// Users
			'VIEW_USERS',
			'GRANT_USERS',	
			
			// Groups
			'VIEW_GROUPS',
			'GRANT_GROUPS',	
			
			// Roles
			'VIEW_ROLES',
		),
		'values' => array(
			'OWN', 
			'GROUP', 
			'ALL',
		),
	),

	'mails' => array(
		'agua'			=> 'iohealthproject@gmail.com',
		'mantenimiento'	=> 'iohealthproject@gmail.com',
		'limpieza'		=> 'iohealthproject@gmail.com',
		'transporte'	=> 'iohealthproject@gmail.com',
		'seguridad'		=> 'iohealthproject@gmail.com',
		'emergencia'	=> 'iohealthproject@gmail.com'
	),
);

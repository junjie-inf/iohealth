<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
$UPLD_MAX_SIZE = | Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/**
 * Patterns
 */

Route::bind('report', 'ReportController');
Route::bind('attachment', 'AttachmentController');
Route::bind('template', 'TemplateController');
Route::bind('comment', 'CommentController');
Route::bind('vote', 'VoteController');
Route::bind('user', 'UserController');
Route::bind('group', 'GroupController');
Route::bind('role', 'RoleController');
Route::bind('profile', 'UserController');
Route::bind('dashboard', 'DashboardController');
Route::bind('dashitem', 'DashItemController');
Route::bind('alert', 'AlertController');
Route::bind('map', 'MapController');
Route::bind('sharedobj', 'SharedObjectController');
Route::bind('rank', 'ScoreController');
Route::bind('ihealth', 'IHealthController');
Route::bind('health', 'DashboardHealthController');

Route::bind('hash', function($value, $route)
{
	$attachment = Attachment::whereHash($value)->first();
	
	if( is_null($attachment) )
	{
		return App::abort(404, 'Page not found');
	}
	
	return $attachment;
});

/**
 * Attachments
 */

Route::get( 'report/{report}/attachment/{hash}/download', array('as' => 'report.{report}.attachment.{hash}.download', 'uses' => 'AttachmentController@download') );

Route::get( 'report/{report}/attachment/{hash}/display', array('as' => 'report.{report}.attachment.{hash}.display', 'uses' => 'AttachmentController@display') );

/**
 * Comments
 */

Route::resource( 'report/{report}/comment', 'CommentController', array('only' => array('index', 'store', 'destroy')) );

/**
 * Votes
 */

Route::resource( 'report/{report}/vote', 'VoteController', array('only' => array('store')) );
Route::resource( 'comment/{comment}/vote', 'VoteController', array('only' => array('store')) );

/**
 * Reports
 */

Route::post( 'report/file', array('as' => 'report.file', 'uses' => 'ReportController@file') );

Route::post( 'report/check', array('as' => 'report.check', 'uses' => 'ReportController@check') );

Route::get( 'report/search', array('as' => 'report.search', 'uses' => 'ReportController@search') );

Route::post( 'report/{report}/follow', array('as' => 'report.follow', 'uses' => 'ReportController@follow') );

Route::get( 'report/import', array('as' => 'report.import', 'uses' => 'ReportController@import') );

Route::match( array('GET', 'POST'), 'report/table', array('as' => 'report.table', 'uses' => 'ReportController@table1') );

Route::post( 'report/populate', array('as' => 'report.populate', 'uses' => 'ReportController@populate') );

Route::post( 'report/dbtables', array('as' => 'report.dbtables', 'uses' => 'ReportController@get_tables') );

Route::post( 'report/dbcolumns', array('as' => 'report.dbcolumns', 'uses' => 'ReportController@get_columns') );

Route::get( 'report/populate2', array('as' => 'report.populate2', 'uses' => 'ReportController@populate_from_MSSQL') );

Route::get( 'report/signs', array('as' => 'report.signs', 'uses' => 'ReportController@signs') );

Route::resource( 'report', 'ReportController' );

Route::get( 'report/create/{template}-{link?}-{linkTitle?}-{linkTemplate?}', array('as' => 'report.create', 'uses' => 'ReportController@create') );

/**
 * Templates
 */

Route::get( 'template/{template}/clean', array('as' => 'template.clean', 'uses' => 'TemplateController@clean') );

Route::resource( 'template', 'TemplateController' );

/**
 * Users
 */

Route::post( 'user/{user}/follow', array('as' => 'user.follow', 'uses' => 'UserController@follow') );

Route::resource( 'user', 'UserController' );

/**
 * Groups
 */

Route::post( 'group/{group}/follow', array('as' => 'group.follow', 'uses' => 'GroupController@follow') );

Route::resource( 'group', 'GroupController' );

/**
 * Roles
 */

Route::post( 'role/{role}/follow', array('as' => 'role.follow', 'uses' => 'RoleController@follow') );

Route::resource( 'role/{role}/permission', 'PermissionController', array('only' => array('store', 'update', 'destroy')) );

Route::resource( 'role', 'RoleController' );

/**
 * Auth
 */

Route::post( 'auth/login', array('as' => 'auth.login', 'uses' => 'AuthController@login') );

Route::post( 'auth/check', array('as' => 'auth.check', 'uses' => 'AuthController@check') );

Route::get( 'auth/logout', array('as' => 'auth.logout', 'uses' => 'AuthController@logout') );

Route::get( 'auth/signup', array('as' => 'auth.signup', 'uses' => 'AuthController@create') ); // Alias of 'auth/create'

Route::resource( 'auth', 'AuthController', array('only' => array('index', 'create', 'store')) );

/**
 * Profile
 */

Route::get( 'profile/report', array('as' => 'profile.report', 'uses' => 'ProfileController@report') );

Route::resource( 'profile', 'ProfileController', array('only' => array('index', 'show', 'update')) );

/**
 * Dashboard
 */
Route::resource( 'dashboard', 'DashboardController' );

/**
 * Dash item
 */
Route::resource( 'dashitem', 'DashItemController' );

Route::post( 'dashitem/preview', array('as' => 'dashboard.preview', 'uses' => 'DashItemController@preview') );


/**
 * Statistic
 */

Route::get( 'statistic', array('as' => 'statistic', 'uses' => 'StatisticController@index') );

Route::get( 'statistic/query', array('as' => 'statistic.query', 'uses' => 'StatisticController@query') );

/**
 * Alert
 */

Route::resource( 'alert', 'AlertController' );

/**
 * Map
 */

Route::get( 'map/marker', array('as' => 'map.marker', 'uses' => 'MainMapController@marker') );

/**
 * Maps
 */

Route::resource( 'map', 'MapController' );
Route::post( 'map/file', array('as' => 'map.file', 'uses' => 'MapController@file') );
Route::get( 'map/geojson/{hash}', array('as' => 'map.geojson', 'uses' => 'MapController@geojson') );
Route::get( 'map/rawgeojson/{hash}', array('as' => 'map.rawGeojson', 'uses' => 'MapController@rawGeojson') );

/**
 * Home
 */

Route::get( 'mainMap', array('as' => ( Auth::check() ? 'map' : 'landing'), 'uses' => 'MainMapController@index') );

/**
 * Páginas estáticas.
 */

Route::get( 'about', array('as' => 'static.about', function() {
	return View::make('static.about');
}));

Route::get( 'contact', array('as' => 'static.contact', function() {
	return View::make('static.contact');
}));

Route::get( 'terms', array('as' => 'static.terms', function() {
	return View::make('static.terms');
}));

Route::get( 'product', array('as' => 'static.product', function() {
	return View::make('static.product');
}));

Route::get( 'crawler', array('as' => 'crawler', function() {
	return View::make('pages.crawler');
}));

/**
 * Reminder.
 */

Route::get( 'password/reset', array('as' => 'password.remind', 'uses' => 'RemindersController@getRemind') );

Route::post( 'password/reset', array('as' => 'password.request', 'uses' => 'RemindersController@postRemind') );

Route::get( 'password/reset/{token}', array('as' => 'password.reset', 'uses' => 'RemindersController@getReset') );

Route::post( 'password/reset/{token}', array('uses' => 'RemindersController@postReset', 'as' => 'password.update') );

/**
 * Shared objects.
 */
Route::post( 'share/{type}/{id}', array('as' => 'sharedobj.share', 'uses' => 'SharedObjectController@share') );
Route::get( 'share/img/{type}/{id}', array('as' => 'sharedobj.share_img', 'uses' => 'SharedObjectController@share_img') );
Route::get( 'share/csv/{type}/{id}', array('as' => 'sharedobj.share_csv', 'uses' => 'SharedObjectController@share_csv') );
Route::post( 'share/toggle/{type}/{id}/{val}', array('as' => 'sharedobj.toggle', 'uses' => 'SharedObjectController@toggle_link') );
Route::get( 'shared/{objhash}', array('as' => 'sharedobj.show', 'uses' => 'SharedObjectController@show') );
Route::get( 'dashitem/{id}/data', array('as' => 'sharedobj.data', 'uses' => 'SharedObjectController@get_data') );

/**
 * Score
 */
Route::resource( 'rank', 'ScoreController');

/**
 * Perfil
 */
Route::resource( 'perfil', 'PerfilController' );
/**
* IHEALTH
*/

Route::post( 'ihealth/subscription', array('as' => 'ihealth.subscription', 'uses' => 'IHealthController@subscription') );
Route::get( 'ihealth/register', array('as' => 'ihealth.register', 'uses' => 'IHealthController@register') );
Route::get( 'ihealth/removal', array('as' => 'ihealth.removal', 'uses' => 'IHealthController@removal') );
// Route::get( 'ihealth/test', array('as' => 'ihealth.test', 'uses' => 'IHealthController@generateTest') );
// Route::get( 'ihealth/token', array('as' => 'ihealth.token', 'uses' => 'IHealthController@getAccessToken') );
// Route::get( 'ihealth/refreshToken', array('as' => 'ihealth.refresh', 'uses' => 'IHealthController@refresh') );
// Route::get( 'ihealth/weigth', array('as' => 'ihealth.weigth', 'uses' => 'IHealthController@getWeigth') );
// Route::get( 'ihealth/input', array('as' => 'ihealth.input', 'uses' => 'IHealthController@input') );

// Route::get( 'iohealth/register', array('as' => 'ihealth.register', 'uses' => 'IHealthController@register') );
// Route::post( 'iohealth/subscription', array('as' => 'ihealth.subscription', 'uses' => 'IHealthController@subscription') );


Route::resource( 'health', 'DashboardHealthController' );
Route::get( 'persons', array('as' => 'ihealth.persons', 'uses' => 'DashboardHealthController@persons') );
Route::get( 'personsData', array('as' => 'ihealth.getPersonData', 'uses' => 'DashboardHealthController@getPersonData') );
Route::get( 'personInfo', array('as' => 'ihealth.getPersonInfo', 'uses' => 'DashboardHealthController@getPersonInfo') );
Route::get( 'personAlerts', array('as' => 'ihealth.getPersonAlerts', 'uses' => 'DashboardHealthController@getPersonAlerts') );
Route::get( 'personDoctorReco', array( 'as'=> 'ihealth.getPersonDoctorReco', 'uses'=> 'DashboardHealthController@getPersonDoctorReco' ) );
Route::get( 'getDiseases', array('as' => 'ihealth.getDiseases', 'uses' => 'DashboardHealthController@getDiseases') );


/**
 * Set route to import CDAs
 *
 * This controller will be called after a used uploaded a CDA HL7 compilant
 * file to obtain information
 *
 * @param $id int|null The id of the user. If no ID is passed, assume logged user
 *
 * @author <joseantonio.garcia8@um.es
 */
Route::get( 'getCDA', array('as' => 'ihealth.getCDA', 'uses' => 'DashboardHealthController@getCDA') );
Route::get( 'getCDA/{id}', array('as' => 'ihealth.getCDA', 'uses' => 'DashboardHealthController@getCDA') );

/**
 * Set route to import CDAs
 *
 * This controller will be called after a used uploaded a CDA HL7 compilant
 * file to obtain information
 *
 * @param $file String Base64 Representation of the file
 * @param $id int|null The id of the user. If no ID is passed, assume logged user
 *
 * @author <joseantonio.garcia8@um.es
 */
Route::post( 'importCDA', array('as' => 'ihealth.importCDA', 'uses' => 'DashboardHealthController@importCDA') );


/**
 * Set route to register push_notification
 *
 * This controller will be called after a used uploaded a CDA HL7 compilant
 * file to obtain information
 *
 * @@todo Chen. ¿Mover esto a un controlador específico?
 *
 * @author <joseantonio.garcia8@um.es
 */
Route::post ( 'subscribe_to_push', array (
	'as' => 'ihealth.subscribe_to_push', 
	'uses' => 'DashboardHealthController@subscribeToPushNotification'
));


/**
 * getPushState
 *
 * Determine if the user has the push notifications activated
 *
 * @@todo Chen. ¿Mover esto a un controlador específico?
 *
 * @author <joseantonio.garcia8@um.es
 */
Route::get ( 'get_push_state', array (
	'as' => 'ihealth.get_push_state', 
	'uses' => 'DashboardHealthController@getPushState'
));



Route::get( '/', array('as' => ('landing'), 'uses' => function () {
	if(Auth::check())
    	return Redirect::to('health');
    else
    	return View::make('pages.landing');
} ) );
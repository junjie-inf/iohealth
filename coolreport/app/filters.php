<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('auth');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/*
|--------------------------------------------------------------------------
| Custom filters
|--------------------------------------------------------------------------
|
| Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
| Etiam ac risus ac arcu ultrices venenatis nec non mauris. 
| Ut non rutrum est, a faucibus neque. Morbi ultricies sem et ligula fringilla, 
| sed varius quam commodo. Nam tristique auctor tellus, 
| in mollis sem adipiscing quis. Nam ante felis, aliquam ac eros a, 
| porta dapibus massa.
|
*/

Route::filter('ajax', function()
{
	if( ! Config::get('app.debug') && ! Request::ajax() ) return App::abort(404, 'Page not found');
});

/**
 * Comprueba si el usuario tiene permisos para acceder a determinado objeto obtenido por url.
 * 
 * @param $route Route
 * @param $request Request
 * @param $permission String
 * @param $class String
 * @param $paramter String
 */
Route::filter('permission', function($route, $request, $permission, $parameter = null)
{	
	$object = $route->getParameter( $parameter );

	if( ! Auth::user()->can($permission, $object) )
	{
		return App::abort(403, 'Forbidden');
	}
});

/**
 * Comprueba si el usuario es superadmin.
 */
Route::filter('admin', function()
{
	if( ! Auth::user()->admin ) 
	{
		return App::abort(403, 'Forbidden');
	}
});

/**
 * Comprueba si el signup está activo.
 */
Route::filter('signup', function()
{
	if( ! Config::get('local.enabled.signup') )
	{
		return App::abort(404, 'Page not found');
	}
});
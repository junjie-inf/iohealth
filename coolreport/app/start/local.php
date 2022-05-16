<?php

/**
 * Error 404 handler
 */

App::missing(function($exception)
{
	Log::error($exception);
	
	if( Config::get('app.debug') === false )
	{
		return Response::view('errors.404', array(), 404);
	}
});

/**
 * Error 500 handler.
 */

App::fatal(function($exception)
{
	Log::error($exception);
	
	if( Config::get('app.debug') === false )
	{
		return Response::view('errors.500', array(), 500);
	}
});

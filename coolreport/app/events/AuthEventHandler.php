<?php

class AuthEventHandler {
	
	/*
	|--------------------------------------------------------------------------
	| Event handlers
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * Auth login.
	 */
	public function login()
	{
		$user = Auth::user();
		
		$user->lastlogin = new DateTime;

		$user->save();
	}
	
	/**
	 * Auth signup.
	 * 
	 */
	public function signup($user)
	{
		//
	}

	
	/*
	|--------------------------------------------------------------------------
	| Event listeners
	|--------------------------------------------------------------------------
	*/
	
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
		$events->listen('auth.login', 'AuthEventHandler@login');
		$events->listen('auth.signup', 'AuthEventHandler@signup');
    }
}

<?php

use Carbon\Carbon;

class PasswordController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function remind()
	{
		return $this->layout('pages.password.remind');
	}

	public function request()
	{
		$credentials = array( 'email' => Input::get('email') );

		return Password::remind($credentials);
	}

	public function reset( $token )
	{
		return View::make('pages.password.reset')->with('token', $token);
	}

	public function update()
	{
		$credentials = array('email' => Input::get('email'));

		return Password::reset($credentials, function($user, $password)
		{
			$user->password = Hash::make($password);

			$user->save();

			return Redirect::to('auth')->with('alert', 'Your password has been reset');
		});
	}
}
<?php

class ProfileController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->layout('pages.profile.index')
					->with('user', Auth::user());
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(User $user)
	{
		// No contamos visitas del propio usuario
		if( Auth::user()->getKey() != $user->getKey() )
		{
			// $user->visit();
		}
		
		return $this->layout('pages.profile.show')
					->with('user', $user);
	}
	
	/**
	 * Display My Reports.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function report()
	{
		if( Request::ajax() )
		{
			return $this->json( array('status' => 'OK', 'data' => Auth::user()->reports->toArray()), 200 );
		}
		
		return $this->layout('pages.profile.report');
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		// Obtenemos el usuario
		$user = Auth::user();
		
		// Validamos y asignamos campos
		$v = $user->parse( 'profile', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
	
		// Hack force checkbox
		if( ! Input::has('gossip') ) $user->gossip = false;

		// Asociamos otras variables
		if( Input::has('password') )
		{
			$user->password = Hash::make( Input::get('password') );
		}

		if ( Input::has('twitter') )
		{
			$user->twitter = Input::get('twitter');
		}
		
		// Guardamos el modelo
		if( $user->save() )
		{
			return $this->json( array(				
				'status' => 'OK'
			), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}
	
}
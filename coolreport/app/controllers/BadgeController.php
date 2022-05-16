<?php

class BadgeController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// Get user
		$user = Auth::user();
		
		// Creamos el objeto
		$badge = new Badge;

		// Validamos y asignamos campos
		$v = $badge->parse( 'store', Input::all() );
				
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		$badge->user()->associate( Auth::user() );

		// Guardamos el modelo
		if( $badge->save() )
		{
			return $this->json( array(
				'status' => 'OK', 
				'id' => $badge->id
			), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}
}
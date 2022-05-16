<?php

class GroupController extends BaseController {

	/**
	 * Trait FollowableController
	 */
	use FollowableController;

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
		$this->beforeFilter('permission:VIEW_GROUPS,group', array('only' => array('show', 'follow')));
		$this->beforeFilter('permission:GRANT_GROUPS,group', array('only' => array('create', 'store', 'edit', 'update', 'destroy')));
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$permission = Auth::user()->getPermissionValue('VIEW_GROUPS');
		
		// Permisos del usuario
		if( $permission == 'ALL' )
		{
			$data = Group::all();
		}
		else if( $permission == 'GROUP' )
		{
			$data = array( Auth::user()->group );
		}
		else if( $permission == 'OWN' )
		{
			$data = array( Auth::user()->group );
		}
		else
		{
			return App::abort(403, 'Forbidden');
		}
		
		return $this->layout('pages.group.index')
					->with('data', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return $this->layout('pages.group.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// Creamos el objeto
		$group = new Group;
		
		// Validamos y asignamos campos
		$v = $group->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		// Guardamos el modelo
		if( $group->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('group.show', array( $group->id )), 
				'id' => $group->id
			), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Group $group)
	{
		if( Request::ajax() )
		{
			return $this->json( array('status' => 'OK', 'data' => $group->datesForHumans()->toArray()), 200 );
		}
		
		return $this->layout('pages.group.show')
					->with('data', $group->datesForHumans());
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Group $group)
	{
		return $this->layout('pages.group.edit')
					->with('data', $group->datesForHumans());
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Group $group)
	{
		// Validamos y asignamos campos
		$v = $group->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		// Guardamos el modelo
		if( $group->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('group.show', array( $group->id )), 
			), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Group $group)
	{
		if( $group->users->count() == 0 )
		{
			$group->delete();
			
			return $this->json( array('status' => 'OK'), 200 );
		}
		
		return $this->json( array('status' => 'FATAL'), 400 );
	}

}
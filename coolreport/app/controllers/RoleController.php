<?php

class RoleController extends BaseController {

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
		$this->beforeFilter('permission:VIEW_ROLES,role', array('only' => array('show', 'follow')));
		$this->beforeFilter('admin', array('only' => array('create', 'store', 'edit', 'update', 'destroy')));
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$permission = Auth::user()->getPermissionValue('VIEW_ROLES');
		
		// Permisos del usuario
		if( $permission == 'ALL' )
		{
			$data = Role::all();
		}
		else if( $permission == 'GROUP' )
		{
			$data = Role::whereIn('id', Auth::user()->group->users()->lists('role_id'))->get();
		}
		else if( $permission == 'OWN' )
		{
			$data = array( Auth::user()->role );
		}
		else
		{
			return App::abort(403, 'Forbidden');
		}
		
		return $this->layout('pages.role.index')
					->with('data', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return $this->layout('pages.role.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// Creamos el objeto
		$role = new Role;
		
		// Validamos y asignamos campos
		$v = $role->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		// Guardamos el modelo
		if( $role->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('role.show', array( $role->id )), 
				'id' => $role->id
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
	public function show(Role $role)
	{
		$role->load('permissions')->datesForHumans();
		
		if( Request::ajax() )
		{
			return $this->json( array('status' => 'OK', 'data' => $role->toArray()), 200 );
		}

		return $this->layout('pages.role.show')
					->with('data', $role);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Role $role)
	{
		return $this->layout('pages.role.edit')
					->with('data', $role->load('permissions')->datesForHumans());
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Role $role)
	{
		// Validamos y asignamos campos
		$v = $role->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		foreach( Input::all() as $type => $value )
		{
			if( in_array( $type, Config::get('local.permission.types') ) )
			{
				$permission = $role->permissions()->whereType($type)->first();
				
				if( ! is_null($permission) )
				{
					if( $value != 'NONE' )
					{
						$permission->value = $value;
						$permission->save();
					}
					else
					{
						$permission->delete();
					}
				}
				else
				{
					if( $value != 'NONE' )
					{
						$permission = new Permission( array( 'type' => $type, 'value' => $value ) );
						$permission->role()->associate($role);
						$permission->save();
					}
				}
			}
		}
		
		// Guardamos el modelo
		if( $role->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('role.show', array( $role->id )), 
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
	public function destroy(Role $role)
	{

		if( $role->users->count() == 0 )
		{
			$role->delete();
			
			return $this->json( array('status' => 'OK'), 200 );
		}
		
		return $this->json( array('status' => 'FATAL'), 400 );
	}

}
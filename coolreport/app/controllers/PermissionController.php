<?php

class PermissionController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
		$this->beforeFilter('permission:VIEW_ROLES,role,role_id');
		$this->beforeFilter('admin', array('only' => array('store', 'update', 'destroy')));
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Role $role)
	{
		// Creamos el objeto
		$permission = new Permission;
		
		// Validamos y asignamos campos
		$v = $permission->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		// Asociamos relaciones externas
		$permission->role()->associate( $role );
		
		// Guardamos el modelo
		if( $permission->save() )
		{
			return $this->json( array('status' => 'OK', 'id' => $permission->id), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Role $role, Permission $permission)
	{
		// Validamos y asignamos campos
		$v = $permission->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		// Guardamos el modelo
		if( $permission->save() )
		{
			return $this->json( array('status' => 'OK'), 200 );
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
	public function destroy(Role $role, Permission $permission)
	{
		$permission->delete();

		return $this->json( array('status' => 'OK'), 200 );
	}

}
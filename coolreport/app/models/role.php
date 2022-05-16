<?php

class Role extends Eloquent {
	
	/**
	 * Trait followable.
	 */
	use Followable;
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id');
	
	/**
	 * The attributes appended to the model's JSON form.
	 * 
	 * @var type 
	 */
	protected $appends = array('readable', 'editable', 'removable');
	
	/**
	 * Reglas de validación de campos.
	 * 
	 * @var type 
	 */
	protected $rules = array(
		'store' => array(
			'title'			=> 'required|max:256',
		),
	);
	
	/**
	 * Devuelve los usuarios del role.
	 * 
	 * @return object User
	 */
	public function users()
	{
		return $this->hasMany('User');
	}
	
	/**
	 * Devuelve los permisos del role.
	 * 
	 * @return object Permission
	 */
	public function permissions()
	{
		return $this->hasMany('Permission');
	}
	
	/**
	 * Atributo de lectura de objeto.
	 * 
	 * @return boolean
	 */
	public function getReadableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('VIEW_ROLES') );
	}
	
	/**
	 * Atributo de modificación de objeto.
	 * 
	 * @return boolean
	 */
	public function getEditableAttribute()
	{
		return ( Auth::check() && Auth::user()->admin );
	}
	
	/**
	 * Atributo de eliminación de objeto.
	 * 
	 * @return boolean
	 */
	public function getRemovableAttribute()
	{
		return ( Auth::check() && Auth::user()->admin && $this->users->count() == 0 );
	}
	
	// -----------------
	
	/**
	 * Consulta si el rol tiene asignado un permiso.
	 * 
	 * @param String $permission
	 * @return boolean
	 */
	public function hasPermission( $permission )
	{
		return $this->permissions()->whereType($permission)->get()->count() > 0;
	}
	
	/**
	 * Consulta si el rol tiene asignado un permiso.
	 * 
	 * @param String $permission
	 * @return boolean
	 */
	public function getPermissionValue( $permission )
	{
		if( ! $this->hasPermission($permission) ) 
		{
			return 'NONE';
		}
		
		return $this->permissions()->whereType($permission)->first()->value;
	}

}
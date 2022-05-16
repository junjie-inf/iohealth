<?php

class Permission extends Eloquent {
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'role_id');
	
	/**
	 * Reglas de validación de campos.
	 * 
	 * @var type 
	 */
	protected $rules = array(
		'store' => array(
			'type' => 'required',
			'value' => 'required',
		),
	);
	
	/**
	 * Devuelve el rol del permiso.
	 * 
	 * @return object Role
	 */
	public function role()
	{
		return $this->belongsTo('Role', 'role_id');
	}
	
	/**
	 * Establece el limite de los tipos de permisos.
	 * 
	 * @return type
	 */
	public function getTypeRule()
	{
		return 'in:' . implode(',', Config::get('local.permission.types'));
	}
	
	/**
	 * Establece el limite de los valores de permisos.
	 * 
	 * @return type
	 */
	public function getValueRule()
	{
		return 'in:' . implode(',', Config::get('local.permission.values'));
	}
	
	// -----------------

}
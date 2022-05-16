<?php

class Group extends Eloquent {
	
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
	 * Devuelve los usuarios del grupo.
	 * 
	 * @return object User
	 */
	public function users()
	{
		return $this->hasMany('User');
	}
	
	/**
	 * Atributo de lectura de objeto.
	 * 
	 * @return boolean
	 */
	public function getReadableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('VIEW_GROUPS') );
	}
	
	/**
	 * Atributo de modificación de objeto.
	 * 
	 * @return boolean
	 */
	public function getEditableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('GRANT_GROUPS') );
	}
	
	/**
	 * Atributo de eliminación de objeto.
	 * 
	 * @return boolean
	 */
	public function getRemovableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('GRANT_GROUPS') && $this->users->count() == 0 );
	}
	
	// -----------------

}
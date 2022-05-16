<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Map extends Eloquent {
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'user_id', 'title', 'politic', 'proccesed', 'hash');
		
	/**
	 * Enable soft deletes.
	 * 
	 * @var type 
	 */
	use SoftDeletingTrait;
	
	protected $dates = ['deleted_at'];
		
	/**
	 * Reglas de validación de campos.
	 * 
	 * @var type 
	 */
	protected $rules = array(
		'store' => array(
			'title'			=> 'required',
		),
		'update' => array(
			'title'			=> 'required',
		),
	);
	
	/**
	 * Devuelve el usuario autor del reporte.
	 * 
	 * @return object User
	 */
	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
	
	/**
	 * Obtiene el datum del reporte por id.
	 * 
	 * @param type $id
	 * @return type
	 */
	public function getDatumById($id)
	{
		return array_first($this->datum, function($foo, $datum) use ($id)
		{
			return $datum->id == $id;
		});
	}
	
	/**
	 * Atributo de lectura de objeto.
	 * 
	 * @return boolean
	 */
	public function getReadableAttribute()
	{
		return ( Auth::check() && Auth::user()->admin );
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
		return ( Auth::check() && Auth::user()->admin );
	}
	
	// -----------------

}
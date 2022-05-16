<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Vote extends Eloquent {
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'votable_id', 'user_id');
	
	/**
	 * The attributes appended to the model's JSON form.
	 * 
	 * @var type 
	 */
	protected $appends = array('readable');
	
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
			'value'			=> 'required', // TODO: verificar si se permite boolean
		),
		'update' => array(
			'value'			=> 'required',
		),
	);
	
	/**
	 * Devuelve el reporte del voto.
	 * 
	 * @return object Report
	 */
	public function votable()
	{
		return $this->morphTo();
	}
	
	/**
	 * Devuelve el usuario autor del voto.
	 * 
	 * @return object User
	 */
	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
	
	/**
	 * Atributo de lectura de objeto.
	 * 
	 * @return boolean
	 */
	public function getReadableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('VIEW_REPORTS', $this->report) );
	}
	
	// -----------------

}
<?php

class Visit extends Eloquent {
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'visitable_id', 'user_id');
	
	/**
	 * Disable timestamps.
	 * 
	 * @var type 
	 */
	public $timestamps = false;
	
	/**
	 * Devuelve el reporte de la visita.
	 * 
	 * @return object Report
	 */
	public function visitable()
	{
		return $this->morphTo();
	}
	
	/**
	 * Devuelve el usuario autor de la visita.
	 * 
	 * @return object User
	 */
	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}

}
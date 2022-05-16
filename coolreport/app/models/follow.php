<?php

class Follow extends Eloquent {
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'followable_id', 'user_id');
	
	/**
	 * Disable timestamps.
	 * 
	 * @var type 
	 */
	public $timestamps = false;
	
	/**
	 * Devuelve el reporte del voto.
	 * 
	 * @return object Report
	 */
	public function followable()
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

}
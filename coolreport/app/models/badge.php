<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Badge extends Eloquent {
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'user_id', 'badge');
	
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
			'user_id'			=> 'required',
			'badge'				=> 'required'
		)
	);
}
<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Score extends Eloquent {
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'user_id', 'score');
	
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
			'score'				=> 'required'
		),
		'update' => array(
			'user_id'			=> 'required',
			'score'			=> 'required'
		),
	);

	/**
	 * Devuelve el usuario correspondiente al score.
	 * 
	 * @return object User
	 */
	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
}
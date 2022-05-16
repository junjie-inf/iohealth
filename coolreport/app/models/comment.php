<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Comment extends Eloquent {
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'report_id', 'user_id');
	
	/**
	 * The attributes appended to the model's JSON form.
	 * 
	 * @var type 
	 */
	protected $appends = array('readable', 'editable', 'removable');
	
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
			'content'		=> 'required',
		),
	);
	
	/**
	 * Devuelve el reporte del comentario.
	 * 
	 * @return object Report
	 */
	public function report()
	{
		return $this->belongsTo('Report', 'report_id');
	}
	
	/**
	 * Devuelve el usuario autor del comentario.
	 * 
	 * @return object User
	 */
	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
	
	public function votes()
	{
		return $this->morphMany('Vote', 'votable');
	}


	/**
	 * Atributo de lectura de objeto.
	 * 
	 * @return boolean
	 */
	public function getReadableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('VIEW_REPORTS', Report::find($this->report_id)) );
	}
	
	/**
	 * Atributo de modificación de objeto.
	 * 
	 * @return boolean
	 */
	public function getEditableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('MODIFY_COMMENTS', $this) );
	}
	/**
	 * Atributo de eliminación de objeto.
	 * 
	 * @return boolean
	 */
	public function getRemovableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('MODIFY_COMMENTS', $this) );
	}
	
	// -----------------
}
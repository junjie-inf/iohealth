<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Attachment extends Eloquent {

	/**
	 * Trait visitable.
	 */
	use Visitable;
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'hash';
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('hash', 'mimetype', 'size');
	
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
	 * Devuelve los usuarios que han visitado a este attachment.
	 * 
	 * @return Collection Visit
	 */
	public function visitors()
	{
		return $this->morphMany('Visit', 'visitable');
	}
	
	/**
	 * Devuelve la url del attachment.
	 * 
	 * @return type
	 */
	public function getMimetypeAttribute()
	{
		return explode('/', $this->attributes['mimetype']);
	}
	
	/**
	 * Tamaño del archivo.
	 * 
	 * @return type
	 */
	public function getFileRule()
	{
		return 'max:' . Config::get('local.uploads.size');
	}
	
	/**
	 * Atributo de lectura de objeto.
	 * 
	 * @return boolean
	 */
	public function getReadableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('VIEW_REPORTS', Report::find($this->report_id) ) );
	}
	
	/**
	 * Atributo de modificación de objeto.
	 * 
	 * @return boolean
	 */
	public function getEditableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('MANAGE_REPORTS', $this) );
	}
	/**
	 * Atributo de eliminación de objeto.
	 * 
	 * @return boolean
	 */
	public function getRemovableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('MANAGE_REPORTS', $this) );
	}
	
	// -----------------

}
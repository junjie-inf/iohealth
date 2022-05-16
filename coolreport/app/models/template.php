<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Template extends Eloquent {
	
	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'fields');
	
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
	 * Campos que deben leerse y escribirse como json.
	 * 
	 * @var type 
	 */
	protected $json = array('fields', 'settings');
	
	/**
	 * Reglas de validación de campos.
	 * 
	 * @var type 
	 */
	protected $rules = array(
		'store' => array(
			'title'			=> 'required|max:256',
		),
		'update' => array(
			'title'			=> 'required|max:256',
		),
	);
	
	/**
	 * Devuelve el usuario autor del template.
	 * 
	 * @return object User
	 */
	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
	
	/** 
	 * Devuelve los reportes en los que se ha respondido al template.
	 * 
	 * @return object Collection
	 */
	public function reports()
	{
		return $this->hasMany('Report');
	}
	
	/**
	 * Obtiene el field del template por id.
	 * 
	 * @param type $id
	 * @return type
	 */
	public function getFieldById($id)
	{
		return array_first($this->fields, function($foo, $field) use ($id)
		{
			return $field->id == $id;
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
		// Controla ver la SECCIÓN de templates (la lista de templates predefinidos sí puede verse siempre)
		return ( Auth::check() && Auth::user()->admin );
	}
	
	/**
	 * Atributo de eliminación de objeto.
	 * 
	 * @return boolean
	 */
	public function getRemovableAttribute()
	{
		return ( Auth::check() && Auth::user()->admin && Report::whereTemplateId($this->getKey())->count() == 0 );
	}

	public function getShowFields()
	{
		$titles = [];

		foreach( $this->fields as $field )
		{
			if( isset($field->show) )
			{
				$titles[$field->id] = array('label' => $field->label,
											'type' => $field->type);
			}
		}

		return $titles;
	}
	
	// -----------------

}
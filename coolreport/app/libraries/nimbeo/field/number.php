<?php namespace Nimbeo\Field;

use \Form;

class Number extends Field {
	
	/*
	|--------------------------------------------------------------------------
	| Attributes of a Numeric field
	|--------------------------------------------------------------------------
	| id
	| type
	| required
	| show
	| label
	*/

	/**
	 * The specific attributes of the field.
	 * 
	 * @var string 
	 */
	protected $attributes = array(
		'class' => 'form-control'
	);
	
	/**
	 * Extra html attributes.
	 * 
	 * @var array
	 */
	protected $extras = array('min', 'max', 'step');

	/**
	 * Devuelve el HTML completo de este field.
	 * 
	 * @return string
	 */
	function build()
	{
		$html = Form::input('number', $this->id, reset($this->answers), $this->attributes);
		
		return $this->buildHtml( $html );
	}
	
}
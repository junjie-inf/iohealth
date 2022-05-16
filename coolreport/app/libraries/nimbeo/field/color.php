<?php namespace Nimbeo\Field;

use \Form;

class Color extends Field {
	
	/*
	|--------------------------------------------------------------------------
	| Attributes of a Text field
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
		'class' => 'form-control color-picker'
	);

	/**
	 * Devuelve el HTML completo de este field.
	 * 
	 * @return string
	 */
	function build()
	{
		$html = Form::text($this->id, reset($this->answers), $this->attributes);
		
		return $this->buildHtml( $html );
	}
	
}
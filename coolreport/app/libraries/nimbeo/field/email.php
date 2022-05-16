<?php namespace Nimbeo\Field;

use \Form;

class Email extends Field {
	
	/*
	|--------------------------------------------------------------------------
	| Attributes of an Email field
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
	 * Devuelve el HTML completo de este field.
	 * 
	 * @return string
	 */
	function build()
	{
		$html = Form::email($this->id, reset($this->answers), $this->attributes);
		
		return $this->buildHtml( $html );
	}
	
}
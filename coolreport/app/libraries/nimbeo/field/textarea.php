<?php namespace Nimbeo\Field;

use \Form;

class Textarea extends Field {
	
	/*
	|--------------------------------------------------------------------------
	| Attributes of a Textarea field
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
		'style' => 'width:100%',
		'rows' => 6,
	);

	/**
	 * Devuelve el HTML completo de este field.
	 * 
	 * @return string
	 */
	function build()
	{
		$html = Form::textarea($this->id, reset($this->answers), $this->attributes);
		
		return $this->buildHtml( $html );
	}

}
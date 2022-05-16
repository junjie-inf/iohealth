<?php namespace Nimbeo\Field;

use \Form;

class Time extends Field {
	
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
		'class' => 'form-control time-picker'
	);

	/**
	 * Devuelve el HTML completo de este field.
	 * 
	 * @return string
	 */
	function build()
	{
		$html = array();
		$html[] = '<div class="bootstrap-timepicker">';
		$html[] = Form::text($this->id, reset($this->answers), $this->attributes);
		$html[] = '</div>';
		
		return $this->buildHtml( implode('', $html) );
	}
	
}
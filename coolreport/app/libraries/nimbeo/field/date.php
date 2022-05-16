<?php namespace Nimbeo\Field;

use \Form;

class Date extends Field {
	
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
		'class' => 'form-control date-picker',
		// 'data-date-format' => 'dd/mm/yyyy'
		'data-date-format' => 'yyyy-mm-dd'
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
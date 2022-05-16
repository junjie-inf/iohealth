<?php namespace Nimbeo\Field;

use \Form;

class Radio extends Field {
	
	/*
	|--------------------------------------------------------------------------
	| Attributes of a Select field
	|--------------------------------------------------------------------------
	| id
	| type
	| required
	| show
	| label
	| multiple
	| options (array)
	*/
	
	/**
	 * The options of the field.
	 * 
	 * @var array 
	 */
	protected $options = null;

	/**
	 * The specific attributes of the field.
	 * 
	 * @var string 
	 */
	protected $attributes = array();
	
	/**
	 * Overwrites the readonly() method of the parent class.
	 * Los select no tienen atributo readonly sino disabled.
	 * 
	 * @return \Field\Select
	 */
	public function readonly()
	{
		$this->attributes['disabled'] = 'disabled';
		
		return $this;
	}
	
	/**
	 * Devuelve el HTML completo de este field.
	 * 
	 * @return string
	 */
	public function build()
	{
		$html = array();
		
		foreach( $this->options as $option )
		{
			$html[] = '<label class="radio">'. Form::radio( $this->id, $option->id, reset($this->answers) == $option->id, $this->attributes ) . $option->value .'</label>';
		}

		return $this->buildHtml( implode('', $html) );
	}

}
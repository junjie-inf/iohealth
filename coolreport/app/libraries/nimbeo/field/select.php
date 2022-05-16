<?php namespace Nimbeo\Field;

use \Form;

class Select extends Field {
	
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
	protected $options = array();

	/**
	 * The specific attributes of the field.
	 * 
	 * @var string 
	 */
	protected $attributes = array(
		'class' => 'form-control',
		'data-rel' => 'chosen',
		'data-placeholder' => 'Select a option...'
	);
	
	/**
	 * Extra html attributes.
	 * 
	 * @var array
	 */
	protected $extras = array('multiple');
	
	/**
	 * The constructor.
	 * 
	 * @param type $attributes
	 */
	public function __construct( $attributes )
	{
		parent::__construct( $attributes );
		
		// Flat options
		$this->options[''] = $this->attributes['data-placeholder'];
		foreach( $attributes->options as $option )
		{
			$this->options[$option->id] = $option->value;
		}
	}
	
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
		$html = Form::select($this->id . (isset($this->attributes['multiple']) ? '[]' : ''), $this->options, $this->answers, $this->attributes);
		
		return $this->buildHtml( $html );
	}

}
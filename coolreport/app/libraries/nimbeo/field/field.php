<?php namespace Nimbeo\Field;

use \Form;

class Field {
	
	/*
	|--------------------------------------------------------------------------
	| Common attributes of a Field object
	|--------------------------------------------------------------------------
	| id
	| label
	| type
	*/
	
	/**
	 * The ID of the field.
	 * 
	 * @var string|null 
	 */
	public $id = null;
	
	/**
	 * The label of the field.
	 * 
	 * @var string|null 
	 */
	protected $label = null;
	
	/**
	 * The type of the field.
	 * 
	 * @var string|null 
	 */
	protected $type = null;
	
	/**
	 * Determines if the field will be shown in the tooltips.
	 * 
	 * @var bool|null 
	 */
	protected $show = null;
	
	/**
	 * The specific attributes of the field. (Se llena en las clases hijo).
	 * 
	 * @var string 
	 */
	protected $attributes = array();
	
	/**
	 * Respuesta(s) del field.
	 * 
	 * @var string 
	 */
	protected $answers = array();
	
	/**
	 * Extra html attributes.
	 * 
	 * @var array
	 */
	protected $extras = array();
	
	/**
	 * The constructor.
	 */
	public function __construct( $attributes )
	{
		// Set all attributes
		foreach( $attributes as $attribute => $value )
		{
			if( property_exists($this, $attribute) && is_null($this->{$attribute}) ) // Si el atributo != null, no se rellena
			{
				$this->{$attribute} = $value;
			}
		}
		
		// Append extra html attributes.
		foreach( $this->getExtras() as $extra )
		{
			if( isset($attributes->{$extra}) )
			{
				$this->attributes[$extra] = $attributes->{$extra};
			}
		}
	}

	/**
	 * Get the attributes that should be added like extras.
	 *
	 * @return array
	 */
	protected function getExtras()
	{
		$defaults = array('required');
		
		return array_merge( $this->extras, $defaults );
	}
	
	/**
	 * Set the readonly option.
	 * 
	 * @return \Field\Field
	 */
	public function readonly()
	{
		$this->attributes['readonly'] = 'readonly';
		
		return $this;
	}
	
	/**
	 * Contestamos el formulario.
	 * 
	 * @param type $answer
	 */
	public function answer( $answer )
	{
		if( is_array($answer->value) )
		{
			foreach( $answer->value as $value )
			{
				$this->answers[] = $value;
			}
		}
		else
		{
			$this->answers[] = $answer->value;
		}
	}

	/**
	 * Devuelve el HTML del Field.
	 * 
	 * @param string $field HTML del field específico que estoy construyendo
	 * @return string
	 */
	public function buildHtml( $field )
	{
		$block_open = '<div class="form-group">'; $block_close = '</div>';
		
		$field_open = '<div class="controls">'; $field_close = '</div>';
		
		$label = $this->buildLabel();
		
		$html = compact('block_open', 'label', 'field_open', 'field', 'field_close', 'block_close');
		
		return implode( '', $html );
	}
	
	/**
	 * Builds a label.
	 * 
	 * @return string HTML de la label.
	 */
	protected function buildLabel()
	{
		$required = ( isset($this->attributes['required']) ? ' <i class="icon-asterisk red" title="Required"></i>' : '' );
		
		$show = ( ! is_null($this->show) ? ' <i class="icon-eye-open" title="Shown in title"></i>' : '' );
		
		return html_entity_decode( Form::label($this->id, $this->label . $required . $show, array('class' => 'control-label')), ENT_QUOTES, 'UTF-8');
	}
}
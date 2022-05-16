<?php namespace Nimbeo\Field;

use \Form;

class Report extends Field {
	
	/*
	|--------------------------------------------------------------------------
	| Attributes of a Report field
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
	
	protected $template;

	public function __construct( $attributes )
	{
		parent::__construct( $attributes );
		
		$this->attributes['data-template'] = $attributes->template;
	}
	
	/**
	 * Devuelve el HTML completo de este field.
	 * 
	 * @return string
	 */
	function build()
	{
		$html = Form::hidden($this->id, reset($this->answers), $this->attributes);
		
		if (!isset($this->attributes['readonly']))
			$html .= '<button class="btn btn-primary btn-select-report">Selección</button>';
		
		if ($this->answers)
		{
			if (is_numeric($this->answers[0])){
			
				$title = \Report::find($this->answers[0])->title;
				if (isset($this->attributes['readonly']))
				{
					//Generar enlace sólo al mostrar, no al editar
					//$title = '<a href="' . \URL::route('report.show', $this->answers[0]) . '">'.$title.'</a>';
				}
			}
			else{
				$html = Form::text($this->id, reset($this->answers), $this->attributes);
				return $this->buildHtml( $html );
			}
		}
		else
		{
			$title = '[Vacío]';
		}
		
		$html .= ' <span class="report-title">'.$title.'</span>';

		
		return $this->buildHtml( $html );
	}
	
}
<?php namespace Nimbeo\Field;

use \Form;
use \Attachment;

class File extends Field {
	
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
		'class' => 'form-control form-file'
	);
	
	/**
	 * Extra html attributes.
	 * 
	 * @var array
	 */
	protected $extras = array('accept', 'max');

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
	 * Contestamos el formulario.
	 * 
	 * @param type $answer
	 */
	public function answer( $answer )
	{
		parent::answer( $answer );
		
		foreach( $this->answers as & $answer )
		{
			$answer->file = Attachment::find($answer->hash);
			
			if( is_null($answer->file) )
			{
				throw new \Exception("Attachment [{$answer->hash}] not found.");
			}
		}
	}
	
	/**
	 * Devuelve el HTML completo de este field.
	 * 
	 * @return string
	 */
	function build()
	{
		$html = array();
		
		if( ! empty($this->answers) )
		{
			$html[] = '<div class="dropzone-previews">';
			
			foreach( $this->answers as $answer )
			{
				$data = array(
					'hash' => $answer->hash,
					'filename' => $answer->filename,
					'size' => \Nimbeo\Util::bytes_format($answer->file->size),
					'mimetype' => $answer->file->mimetype,
				);

				//$html[] = Form::input('hidden', $this->id . '[]', preg_replace('/"/', '\'', json_encode($data) ), $this->attributes);
				$html[] = '<input class="form-control form-file" disabled="disabled" name="'.$this->id.'[]" type="hidden" value=\'' . json_encode($data) . '\'>';
			}
			
			$html[] = '</div>';
		}
		
		if( ! isset($this->attributes['disabled']) )
		{
			$html[] = Form::file($this->id, $this->attributes);
		}
		
		return $this->buildHtml( implode('', $html) );
	}
	
}
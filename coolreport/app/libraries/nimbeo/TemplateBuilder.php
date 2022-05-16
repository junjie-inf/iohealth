<?php namespace Nimbeo;

class TemplateBuilder {
	
	/**
	 *Juanan comenta
	 * @var type 
	 */
	protected $fields = array();
	
	/**
	 * Creamos el builder a partir de la plantilla.
	 * La plantilla es un array de objetos StdClass
	 * 
	 * @param array $objects
	 */
	public function with( array $objects ) 
	{
		//dd($objects);
		foreach( $objects as $object )
		{
			$class = 'Nimbeo\\Field\\' . ucfirst( $object->type );
			
			$this->fields[] = new $class($object);
		}
		
		return $this;
	}
	
	/**
	 * Juann comenta
	 * @param $objects
	 */
	public function answer( $objects )
	{
		foreach( $objects as $object )
		{
			foreach( $this->fields as $field )
			{
				if( $field->id == $object->id )
				{
					$field->answer($object);
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * Juann comenta
	 * @param array $objects
	 */
	public function readonly()
	{
		foreach( $this->fields as $field )
		{
			$field->readonly();
		}
		
		return $this;
	}
	
	/**
	 * Juann comenta
	 * @param array $objects
	 */
	public function build()
	{
		$html = array();

		$i=0;
		
		foreach( $this->fields as $field )
		{
			$html[] = $field->build(); // por ahi ()// Nota: para hacer un field readonly: $field->readonly()->build()
		}
		
		return implode('', $html);
	}
	
}

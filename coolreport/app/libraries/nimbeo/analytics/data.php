<?php namespace Nimbeo\Analytics;

use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;
use Nimbeo\Util;

class Data implements ArrayableInterface, JsonableInterface {
	
	/**
	 * Name of the data serie.
	 * 
	 * @var string 
	 */
	public $key = null;
	
	/**
	 * Serie color.
	 * 
	 * @var type 
	 */
	public $color = null;
	
	/**
	 * Values of the data serie.
	 * 
	 * @var array 
	 */
	public $values = array();
	
	/**
	 * Constructor.
	 * 
	 * @param type $key
	 */
	public function __construct($key, array $values)
	{
		$this->key = $key;
		
		//$this->color = Util::getColor(Util::stringToInteger($key));
		
		foreach( $values as $value )
		{
			if( is_object($value) )
			{
				$this->values[] = new Value( $value->x, $value->y );
			}
			else
			{
				$this->values[] = new Value( $value[0], $value[1] );
			}
		}
	}
	
	/**
	 * Arrayable function.
	 * 
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'key' => $this->key,
			'color' => $this->color,
			'values' => $this->valuesToArray()
		);
	}
	
	/**
	 * Jsonable function.
	 * 
	 * @param type $options
	 * @return json
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}
	
	/**
	 * Values to array.
	 * 
	 * @return array
	 */
	protected function valuesToArray()
	{
		$values = array();
		
		foreach( $this->values as $value )
		{
			$values[] = $value->toArray();
		}
		
		return $values;
	}
	
	/**
	 * toString function.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}
	
}

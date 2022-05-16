<?php namespace Nimbeo\Analytics;

use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;

class Value implements ArrayableInterface, JsonableInterface {
	
	/**
	 * x axis value.
	 * 
	 * @var type 
	 */
	public $x;
	
	/**
	 * y axis value.
	 * 
	 * @var type 
	 */
	public $y;
	
	/**
	 * Constructor.
	 * 
	 * @param type $x
	 * @param type $y
	 */
	public function __construct( $x, $y )
	{
		$this->x = $x;
		$this->y = floatval($y);
	}
	
	/**
	 * Arrayable function.
	 * 
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'x' => $this->x,
			'y' => $this->y
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
	 * toString function.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}
	
}

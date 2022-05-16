<?php namespace Nimbeo;

use Illuminate\Database\Eloquent\Collection as ExtensibleCollection;

class Collection extends ExtensibleCollection {

	/**
	 * Activa la funcion de datesForHumans en todos los elementos de la colección.
	 * 
	 * @return \Nimbeo\Collection
	 */
	public function datesForHumans()
	{
		$this->each(function ($item) 
		{
			$item->datesForHumans();
		});
		
		return $this;
	}
	
	/**
	 * Load eager relations loading only count number.
	 * 
	 * @param type $relations
	 * @return \Nimbeo\Collection
	 */
	public function loadCountableRelations($relations)
	{
		if (is_string($relations)) $relations = func_get_args();
		
		$this->each(function ($item) use ($relations) 
		{
			$item->loadCountableRelations($relations);
		});
		
		return $this;
	}
	
	public function hide($fields)
	{
		if (is_string($fields)) $fields = func_get_args();
		
		$this->each(function ($item) use ($fields) 
		{
			$item->hide($fields);
		});
		
		return $this;
	}
	
	public function show($fields)
	{
		if (is_string($fields)) $fields = func_get_args();
		
		$this->each(function ($item) use ($fields) 
		{
			$item->show($fields);
		});
		
		return $this;
	}
	
}
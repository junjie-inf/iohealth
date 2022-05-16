<?php namespace Nimbeo;

use Validator;
use Nimbeo\Collection;
use Illuminate\Database\Eloquent\Model as ExtensibleModel;
use App;
use Carbon\Carbon;
use Config;
use Auth;

class Model extends ExtensibleModel {

	/**
	 * Activa el uso de datesForHumans al obtener un atributo.
	 * 
	 * @var type 
	 */
	protected $datesForHumans = false;
	
	/**
	 * Reglas de verificación de formularios.
	 * 
	 * @var type 
	 */
	protected $rules = array();
	
	/**
	 * Campos que deben leerse y escribirse como json.
	 * 
	 * @var type 
	 */
	protected $json = array();
	
	/**
	 * Devuelve un atributo del array 'attributes' sin aplicar mutators.
	 * 
	 * @param string $key Key del modelo que se quiere
	 * @return type Valor del atributo
	 */
	public function getRawAttribute( $key )
	{
		return $this->attributes[$key];
	}
	
	/**
	 * Devuelve las reglas de validación para el modelo.
	 * 
	 * @param string $action
	 * @return type Array de reglas
	 */
	public function getRules($action)
	{
		$rules = array();
		
		foreach($this->rules[$action] as $rule => $value)
		{
			$rules[ $rule ] = $this->hasRuleMutator($rule) ? $this->mutateRule($rule, $value, $action) : $value;
		}
		
		return $rules;
	}
	
	/**
	 * Determine if a get mutator exists for a rule.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasRuleMutator($rule)
	{
		return method_exists($this, 'get'.studly_case($rule).'Rule');
	}
	
	/**
	 * Get the value of a rule using its mutator.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return mixed
	 */
	protected function mutateRule($rule, $value, $action)
	{
		return $value . ( ! empty($value) ? '|' : '' ) . $this->{'get'.studly_case($rule).'Rule'}($action);
	}
	
	/**
	 * Create a new Eloquent Collection instance.
	 *
	 * @param  array  $models
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function newCollection(array $models = array())
	{
		return new Collection($models);
	}
	
	/*public function getMutatedAttributes()
	{
		if( in_array($key, $this->json) )
		{
			return true;
		}
	}*/
	
	/**
	 * Añadimos como mutator las fechas si tenemos el parametro activado.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasGetMutator($key)
	{
		if( $this->datesForHumans && in_array($key, $this->getDates()) )
		{
			return true;
		}
		
		if( in_array($key, $this->json) )
		{
			return true;
		}
		
		return parent::hasGetMutator($key);
	}

	/**
	 * Devolvemos la fecha en formato humano si el parametro está activado.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return mixed
	 */
	protected function mutateAttribute($key, $value)
	{
		if( $this->datesForHumans && in_array($key, $this->getDates()) )
		{
			return $this->asDateTime($value)->diffForHumans();
		}
		
		// Get object from json models.
		if( in_array($key, $this->json) )
		{
			return json_decode($value);
		}
		
		return parent::mutateAttribute($key, $value);
	}
	
	/**
	 * Custom set attribute.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function setAttribute($key, $value)
	{
		// Set object from json models.
		if( in_array($key, $this->json) )
		{
			$value = json_encode($value);
		}
		
		return parent::setAttribute($key, $value);
	}
	
	/**
	 * Añadimos fechas a los parametros de atributos mutados.
	 *
	 * @return array
	 */
	public function getMutatedAttributes()
	{
		$mutated = parent::getMutatedAttributes();
		
		if( $this->datesForHumans )
		{
			$mutated = array_merge($mutated, array('created_at', 'updated_at')); // not mutate deleted_at
		}
		
		$mutated = array_merge($mutated, $this->json);
		
		return $mutated;
	}
	
	/**
	 * Activamos fechas para todos los modelos.
	 * 
	 * @return \Nimbeo\Model
	 */
	public function datesForHumans()
	{
		$this->datesForHumans = true;
				
		array_map(function($relation)
		{
			if( get_class($relation) != 'Illuminate\Database\Eloquent\Relations\Pivot' ) // Omitimos la relación Pivot
			{
				$relation->datesForHumans();
			}
		}, $this->relations);
		
		return $this;
	}
	
	/**
	 * Fill a model with inputs parsing rules.
	 * 
	 * @param type $rule
	 * @param array $input
	 * @return type
	 */
	public function parse($rule, array $input)
	{
		// Reglas de modelo
		$rules = $this->getRules($rule);
		
		// Obtenemos los valores de los inputs usando las claves de las reglas
		$fields = sanitize($input);
		
		// Validamos los campos
		$v = Validator::make( $fields, $rules );
		
		// Si no hay error rellenamos el objeto
		if( ! $v->fails() )
		{
			// Obtenemos todas las columnas del modelo.
			$attributes = array_fill_keys( array_values( \DB::table('information_schema.columns')->whereTableName($this->getTable())->lists('column_name') ), null ); // TODO: find a better way.

			// Rellenamos con las columnas y los valores recibidos.
			$this->fill( array_group( $attributes, $fields ) );
		}
		
		return $v;
	}
	
	/**
	 * Load eager relations loading only count number.
	 * 
	 * @param type $relations
	 */
	public function loadCountableRelations($relations)
	{
		if (is_string($relations)) $relations = func_get_args();

		$model = $this;
		
		array_map(function($relation) use ($model)
		{
			$model[$relation] = $model->$relation->count();
			$model->setRelations( array_except( $model->getRelations(), $relation ) );
		}, $relations);
	}
	
	/**
	 * Remove html entities from database.
	 * 
	 * @return type
	 */
	public function toArray()
	{
		return unsanitize( parent::toArray() );
	}
	
	public function hide($fields)
	{
		if (is_string($fields)) $fields = func_get_args();
		
		// TODO: crear un helper de array_add que no sea array_add
		foreach($fields as $field)
		{
			array_push($this->hidden, $field);
		}
		
		return $this;
	}
	
	public function show($fields)
	{
		if (is_string($fields)) $fields = func_get_args();
		
		array_remove($this->hidden, $fields);
		
		return $this;
	}
}
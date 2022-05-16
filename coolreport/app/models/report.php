<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Report extends Eloquent {
	
	/**
	 * Trait followable.
	 */
	use Followable;

	/**
	 * Trait visitable.
	 */
	use Visitable;

	/**
	 * Crea un campo "geo" con el GeoJSON
	 */
	use GeoJSONTrait;
	public function getGeoAttribute($value)
	{
		return json_decode($value);
	}
	public function setGeoAttribute($value)
	{
		$this->attributes['main_geo'] = DB::raw("ST_GeomFromGeoJSON('" . $value . "')::geography");
	}

	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'user_id', 'template_id', 'datum', 'geodata');
	
	/**
	 * The attributes appended to the model's JSON form.
	 * 
	 * @var type 
	 */
	protected $appends = array('title', 'country', 'city', 'address', 'readable', 'editable', 'removable');
	
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	//protected $hidden = array('datum');
	
	/**
	 * Enable soft deletes.
	 * 
	 * @var type 
	 */
	use SoftDeletingTrait;
	
	protected $dates = ['deleted_at'];
	
	/**
	 * Campos que deben leerse y escribirse como json.
	 * 
	 * @var type 
	 */
	protected $json = array('datum', 'geodata');
	
	/**
	 * Reglas de validación de campos.
	 * 
	 * @var type 
	 */
	protected $rules = array(
		'store' => array(
			'template'		=> 'required|exists:templates,id',
			'latitude'		=> 'numeric',
			'longitude'		=> 'numeric',
			'accuracy'		=> 'numeric',
			'device'		=> 'max:128',
			'platform'		=> 'max:256',
			'version'		=> 'max:128',
			'screen'		=> 'max:16', 
		),
		'update' => array(
			'latitude'		=> 'numeric',
			'longitude'		=> 'numeric',
			'accuracy'		=> 'numeric',
		),
	);


	/**
	 * Eloquent models also contain a static boot method, which may provide a convenient place 
	 * to register your event bindings.
	 * 
	 * @link https://laravel.com/docs/4.2/eloquent#model-events
	 */
	public static function boot()
    {
    	// Delegate on parent
        parent::boot();


        // Creating only affects to new items not items being updated
        self::creating (function ($report) {

        	// ID assigned manually
        	if ($report->id) {
        		return true;
        	}

			// Manually assign the the id
			$max_id = DB::select (DB::raw ("SELECT id FROM reports ORDER BY id DESC LIMIT 1"));
			$max_id = reset ($max_id);
			$max_id = $max_id->id;
			$max_id = $max_id + 1;

			$report->id = $max_id;


			return true;
        });
	}


	
	/**
	 * Devuelve el usuario autor del reporte.
	 * 
	 * @return object User
	 */
	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
	
	/**
	 * Devuelve los comentarios del reporte.
	 * 
	 * @return object Comment
	 */
	public function comments()
	{
		return $this->hasMany('Comment')->orderBy('created_at', 'DESC');
	}
	
	/**
	 * Devuelve los votos del reporte.
	 * 
	 * @return object Vote
	 */
	public function votes()
	{
		return $this->morphMany('Vote', 'votable');
	}
	
	/**
	 * Devuelve el template al que pertenece la respuesta.
	 * 
	 * @return object Template
	 */
	public function template()
	{
		return $this->belongsTo('Template');
	}
	
	/**
	 * Devuelve los usuarios que han visitado a este report.
	 * 
	 * @return Collection Visit
	 */
	public function visitors()
	{
		return $this->morphMany('Visit', 'visitable');
	}
	
	/**
	 * Obtiene el datum del reporte por id.
	 * 
	 * @param type $id
	 * @return type
	 */
	public function getDatumById($id)
	{
		return array_first($this->datum, function($foo, $datum) use ($id)
		{
			return $datum->id == $id;
		});
	}

	
	public function getReadableDatum()
	{
		$titles = array();
		
		foreach( Template::find( $this->template_id )->fields as $field )
		{
			$datum = $this->getDatumById($field->id);
			
			if( $datum != null && isset($field->show) )
			{
				if( is_array( $datum->value ) )
				{
					foreach( $datum->value as $value )
					{
						$titles[$field->id] = $this->getDatumValue($field, $value);
					}
				}
				else
				{
					/*$report->content = TemplateBuilder::with($report->template->fields)->answer($report->datum)->readonly()->build();
					//dd($report->toArray());*/
					$titles[$field->id] = array('value' => $this->getDatumValue($field, $datum->value),
												'url' => ($field->type == 'report'? URL::route('report.show', $this->getDatumValue($field, $datum->value)) : ""));
				}
			}
		}
	
		if( empty($titles) )
		{
			$titles[] = '[Undefined title]';
		}
		
		return $titles;
	}
	
	
	/**
	 * Obtiene los campos marcados como show.
	 * 
	 * @return type
	 */
	public function getTitleAttribute()
	{
		$titles = array();
		
		foreach( Template::find( $this->template_id )->fields as $field )
		{
			$datum = $this->getDatumById($field->id);
			
			if( $datum != null && isset($field->show) )
			{
				if( is_array( $datum->value ) )
				{
					foreach( $datum->value as $value )
					{
						$titles[] = $this->getDatumValue($field, $value);
					}
				}
				else
				{
					$titles[] = $this->getDatumValue($field, $datum->value);
				}
			}
		}
		
		if( empty($titles) )
		{
			$titles[] = '[Undefined title]';
		}
		
		return implode(', ', $titles);
	}
	
	/**
	 * Return real value from datum.
	 */
	protected function getDatumValue($field, $value)
	{
		switch($field->type)
		{
			case 'file':
			{
				return $value->filename;
			} break;
			case 'checkbox':
			case 'select':
			case 'radio':
			{
				return array_first($field->options, function($foo, $option) use ($value) 
				{
					return $option->id == $value;
				})->value;
			} break;
		}
		
		return $value;
	}
	
	/**
	 * Get country.
	 * 
	 * @return type
	 */
	public function getCountryAttribute()
	{
		if( ! is_null($this->geodata) && ! is_null($this->geodata->address_components) )
		{
			$country = array_first($this->geodata->address_components, function($foo, $component)
			{
				return in_array('country', $component->types);
			});

			if( ! is_null($country) )
			{
				return $country->long_name; 
			}
		}
		return null;
	}
	
	/**
	 * Get country.
	 * 
	 * @return type
	 */
	public function getCityAttribute()
	{
		if( ! is_null($this->geodata) && ! is_null($this->geodata->address_components) )
		{
			$locality = array_first($this->geodata->address_components, function($foo, $component)
			{
				return in_array('locality', $component->types);
			});
		
			if( ! is_null($locality) ) 
			{
				return $locality->long_name;
			}
		}

		return null;
	}
	
	/**
	 * Get country.
	 * 
	 * @return type
	 */
	public function getAddressAttribute()
	{
		if( ! is_null($this->geodata) )
		{
			return $this->geodata->formatted_address;
		}

		return null;
	}
	
	/**
	 * Atributo de lectura de objeto.
	 * 
	 * @return boolean
	 */
	public function getReadableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('VIEW_REPORTS', $this) );
	}
	
	/**
	 * Atributo de modificación de objeto.
	 * 
	 * @return boolean
	 */
	public function getEditableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('MANAGE_REPORTS', $this) );
	}

	/**
	 * Atributo de eliminación de objeto.
	 * 
	 * @return boolean
	 */
	public function getRemovableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('MANAGE_REPORTS', $this) );
	}
	
	// -----------------
	
	/**
	 * Return a list of associated followers.
	 * 
	 * @return Collection
	 */
	public function getAssociatedFollowers($withThreadUsers = false)
	{
		$self = $this;

		$map = function ($item)
		{
			return $item->user;
		};

		// Usuarios con notificaciones globales activadas
		$users = User::whereGossip(true)->get();

		// Usuario del reporte
		$users->push( $this->user );

		// Seguidores del reporte
        $users = $users->merge( $this->followers->map($map) );

        // Seguidores del usuario del reporte
        $users = $users->merge( $this->user->followers->map($map) );

        // Seguidores del grupo del usuario del reporte
        $users = $users->merge( $this->user->group->followers->map($map) );

        // Seguidores del rol del usuario del reporte
        $users = $users->merge( $this->user->role->followers->map($map) );

        if( $withThreadUsers && ! $this->comments->isEmpty() )
        {
	        // Usuarios que forman parte del hilo de comentarios.
	    	$users = $users->merge( $this->comments->map(function($comment) {
	    		return $comment->user;
	    	}) );
	    }

        return $users->filter(function($user) use ($self) {
			// Si el usuario es el propio usuario o
			// el usuario no tiene permisos para ver el reporte
			// no se le añade a la lista de usuarios asociados.
			return ( Auth::user()->getKey() != $user->getKey() && $user->can('VIEW_REPORTS', $self) );
		});
	}
}

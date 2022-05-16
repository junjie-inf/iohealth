<?php

use Carbon\Carbon;

class MapController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('admin');
		$this->beforeFilter('ajax', array('on' => 'post'));
	}

	/**
	 * Display the first dahsboard of the user, or create a new one
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Auth::user();
		if ($user->admin)
			$data = Map::all()->sortBy('title');
		else
			$data = $user->dashitems->sortBy('title');

		return $this->layout('pages.map.index')
					->with('data', $data);
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Map $map)
	{
		$templates = Template::all();

		/* $geojson = $this->geojson($map->hash); */
		$geojson = $this->geojson($map->hash)->getData();

		return $this->layout('pages.map.show')
				->with('data', $map)
				->with('geojson', $geojson);
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  String hash
	 * @return Response
	 */
	public function geojson($hash)
	{
		if ( is_object($hash) )
		{
			
			$hash = $hash['attributes']['hash'];
		}

		$json = fopen( storage_path( Config::get('local.uploads.path') . '/maps/' . $hash), "r" );
		$geojson = fread($json, filesize(storage_path( Config::get('local.uploads.path') . '/maps/' . $hash)));

		//dd(DB::select("st_geomfromgeojson('".$geojson."')::geography"));

		if ( !empty(json_decode($geojson)->{'features'}) )
		{
			$return = json_decode($geojson)->{'features'};
		}
		else if ( !empty(json_decode($geojson)->{'geometry'}) )
		{
			$return = json_decode($geojson)->{'geometry'};
		}

		return Response::json($return);
		/* return $return; */
	}

    public function rawGeojson($hash)
    {
        if ( is_object($hash) )
        {
            
            $hash = $hash['attributes']['hash'];
        }
        
        return Response::download( storage_path( Config::get('local.uploads.path') . '/maps/' . $hash));
    }
    
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$templates = Template::all()->sortBy('title');

		return $this->layout('pages.map.create');
	}
	
	/**
	 * Show the form for editing resources.
	 *
	 * @return Response
	 */
	public function edit(Map $map)
	{
		$templates = Template::all();

		/* $geojson = $this->geojson($map->hash); */
		$geojson = $this->geojson($map->hash)->getData();

		return $this->layout('pages.map.edit')
				->with('map', $map)
				->with('geojson', $geojson);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Map $map)
	{
		$map->delete();
		
		Event::fire('map.destroy', $map);

		return $this->json( array('status' => 'OK'), 200 );
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		$map = new Map;
		
		// Validamos y asignamos campos
		$v = $map->parse( 'store', Input::all() );

		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		$map->user_id = Auth::user()->getKey();
		$map->title = Input::get('title');
		$map->hash = json_decode(Input::get('geojsonfile')[0])->{'hash'};
		
		if( $map->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('map.index'), 
				'id' => $map->id
			), 200 );
		}
		
		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 ); // Definir bien este error
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Map $map)
	{
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		// Validamos y asignamos campos
		$v = $map->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		$map->user_id = Auth::user()->getKey();
		$map->title = Input::get('title');

		try {
			$newHash = json_decode(Input::get('geojsonfile')[0])->{'hash'};
		}

		catch (ErrorException $e) {
			$newHash = NULL;
		}

		$oldHash = $map->hash;

		if( $newHash )
			$map->hash = $newHash;

		if( $map->save() )
		{
			// Remove old file
			if ( $newHash && $newHash != $oldHash )
				unlink( storage_path( Config::get('local.uploads.path') . '/maps/' . $oldHash ) );

			return $this->json( array(
				'status' => 'OK',
				'redirect' => URL::route('map.show', array( $map->id )),
			), 200 );
		}
		
		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 ); // Definir bien este error
	}

	protected function compactDatum(Template $template)
	{
		$datum = new stdClass();
		$datum->fields = array();

		foreach( Input::all() as $id => $value )
		{
			$field = $template->getFieldById($id);
			
			if( is_string($value) ) $value = trim($value);
					
			if( ! is_null($field) )
			{
				// TODO: comprobar los datos que vienen.
				
				if( $field->type == 'file' )
				{
					$files = array();

					foreach( $value as $json )
					{
						$files[] = compactJSON($json, 'hash', 'filename');
					}

					$value = $files;
				}

				if( ! empty($value) )
				{
					$datum->fields[$id] = (object) compact('id', 'value');
				}
			}
		}

		$datum->template = $template->id;
		$datum->type = 'report';

		return array($datum);
	}

	/**
	 * Upload a file before creating a map.
	 * 
	 * @return Response
	 */
	public function file()
	{		
		// TODO: comprobar mime.
		$v = Validator::make( Input::all(), array(
			'file' => 'required|max:' . Config::get('local.uploads.size'),
			//'template' => 'required|exists:templates,id',
		));
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		// Template TODO: comprobar si el template tiene adjuntos.
		//$template = Template::find( Input::get('template') );
		
		// File
		$file = Input::file('file');
		
		// Storage
		$path = storage_path( Config::get('local.uploads.path') . '/maps');
		
		// Calculamos el hash
		$hash = sha1_file( $file->getPathname() );
		
		// Miramos si el archivo ya existia en la base de datos.
		if( is_null( Attachment::find($hash) ) )
		{
			$attachment = new Attachment;
			$attachment->hash = $hash;
			$attachment->size = $file->getSize();
			$attachment->mimetype = $file->getMimeType();

			// No se ha podido guardar.
			if( ! $attachment->save() )
			{
				return $this->json( array('status' => 'FATAL'), 400 );
			}
		}
		
		// Movemos el archivo
		if( ! file_exists( $path . '/' . $hash ) )
		{
			$file->move($path, $hash);
		}
		
		return $this->json( array(				
			'status' => 'OK', 
			'data' => $hash, 
		), 200 );
	}
}

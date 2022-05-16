<?php

use Carbon\Carbon;

class AlertController extends BaseController {

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
			$data = Alert::all()->sortBy('title');
		else
			$data = $user->dashitems->sortBy('title');

		return $this->layout('pages.alert.index')
					->with('data', $data);
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Alert $alert)
	{
		// dd($alert->actions[0]->fields);
		$templates = Template::all();
		
		return $this->layout('pages.alert.show')
				->with('templates', $templates)
				->with('alert', $alert);
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$templates = Template::all()->sortBy('title');

		return $this->layout('pages.alert.create')
				->with('templates', $templates);
	}
	
	/**
	 * Show the form for editing resources.
	 *
	 * @return Response
	 */
	public function edit(Alert $alert)
	{
		$templates = Template::all();
		
		return $this->layout('pages.alert.edit')
				->with('templates', $templates)
				->with('alert', $alert);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Alert $alert)
	{
		$alert->delete();
		
		Event::fire('alert.destroy', $alert);

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
		
		$alert = new Alert;
		
		// Validamos y asignamos campos
		$v = $alert->parse( 'store', Input::all() );

		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		$alert->conditions = array('template' => Input::get('template'), 'condition' => Input::get('conditions'));
		$alert->actions = $this->compactDatum(Template::find( Input::get('actions')));

		$alert->user_id = Auth::user()->getKey();
		$alert->title = Input::get('title');
		
		if( $alert->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('alert.index'), 
				'id' => $alert->id
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
	public function update(Alert $alert)
	{
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		// Validamos y asignamos campos
		$v = $alert->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		$alert->conditions = array('template' => Input::get('template'), 'condition' => Input::get('conditions'));
		$alert->actions = $this->compactDatum(Template::find( Input::get('actions')));

		$alert->user_id = Auth::user()->getKey();
		$alert->title = Input::get('title');
		
		if( $alert->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('alert.index'), 
				'id' => $alert->id
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
}

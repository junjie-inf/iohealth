<?php

class TemplateController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
		//$this->beforeFilter('permission:VIEW_REPORTS,report,report_id');
		//$this->beforeFilter('permission:ADD_ATTACHMENTS,report,report_id', array('only' => array('create', 'store')));
		//$this->beforeFilter('permission:MODIFY_ATTACHMENTS,attachment', array('only' => array('edit', 'update', 'destroy')));
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = Template::all();
		
		if( Request::ajax() )
		{
			return $this->json( array('status' => 'OK', 'data' => $data->toArray()), 200 );
		}
		
		return $this->layout('pages.template.index')->with('data', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		// TODO FIX THIS
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		$templates = array_values(Template::all()->sortBy('title')->toArray());

		//dd( $templates->toArray() );
		return $this->layout('pages.template.create')
			->with('templates', $templates);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// TODO FIX THIS
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		$template = new Template;
		
		// Validamos y asignamos campos
		$v = $template->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		$fields = json_decode( Input::get('fields') );
		$settings = json_decode( Input::get('settings') );
		
		// Error si no se ha rellenado al menos un campo
		if( count($fields) <= 0 )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => array('Template Fields' => Lang::get('validation.required', array('attribute' => 'Template Fields')))), 400 );
		}
		
		// Generamos las IDs.
		$fields = $this->generateFieldsId($fields);
		
		$dateField = null;
		// Comprobamos si estan todas las options
		foreach( $fields as & $field )
		{
			// Error si no se ha rellenado al menos un campo
			if( isset($field->options) && count($field->options) <= 0 )
			{
				return $this->json( array('status' => 'ERROR', 'messages' => array($field->type => Lang::get('validation.required', array('attribute' => $field->type)))), 400 );
			}
			
			if( isset($field->main_date) && $field->main_date == true )
			{
				$dateField = $field->id;
				unset($field->main_date);
			}
		}
		
		if ($dateField != null)
		{
			$settings->date = $dateField;
		}
		
		$template->user_id = Auth::user()->getKey();
		
		$template->fields = $fields;		
		$template->settings = $settings;
		
		if( $template->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('template.show', array( $template->id )), 
				'id' => $template->id
			), 200 );
		}
		
		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 ); // Definir bien este error
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Template $template)
	{
		$form = TemplateBuilder::with($template->fields)->build();
		
		if( Request::ajax() )
		{
			return $this->json( array('status' => 'OK',
										'data' => $template->datesForHumans()->toArray(),
										'form' => $form), 200 );
		}
		
		return $this->layout('pages.template.show')
					->with('data', $template->datesForHumans())
					->with('form', $form);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Template $template)
	{
		// TODO FIX THIS
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		$templates = array_values(Template::all()->sortBy('title')->toArray());

		$template->content = TemplateBuilder::with($template->fields)->build();
		
		return $this->layout('pages.template.edit')
					->with('data', $template->datesForHumans())
					->with('templates', $templates);
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Template $template)
	{
		// TODO FIX THIS
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		// Validamos y asignamos campos
		$v = $template->parse( 'update', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		$fields = json_decode( Input::get('fields') );
		$settings = json_decode( Input::get('settings') );
		
		// Error si no se ha rellenado al menos un campo
		if( count($fields) <= 0 )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => array('Template Fields' => Lang::get('validation.required', array('attribute' => 'Template Fields')))), 400 );
		}		
		
		// Generamos las IDs.
		$fields = $this->generateFieldsId($fields);
		
		// Comprobamos si estan todas las options
		$dateField = null;
		foreach( $fields as & $field )
		{
			// Error si no se ha rellenado al menos un campo
			if( isset($field->options) && count($field->options) <= 0 )
			{
				return $this->json( array('status' => 'ERROR', 'messages' => array($field->type => Lang::get('validation.required', array('attribute' => $field->type)))), 400 );
			}
			if( isset($field->main_date) && $field->main_date == true )
			{
				$dateField = $field->id;
				unset($field->main_date);
			}
		}
		
		if ($dateField != null)
		{
			$settings->date = $dateField;
		}
		//Marcar sucio si tenemos que actualizar los reports
		if (isset($template->settings->date) != isset($settings->date))
			$isDirty = true;
		else if (!isset($template->settings->date) && !isset($settings->date))
			$isDirty = false;
		else
			$isDirty = $settings->date != $template->settings->date;
		
		$template->fields = $fields;
		$template->settings = $settings;
		
		// Guardamos el modelo
		if( $template->save() )
		{
			if ($isDirty)
			{
				if (!isset($template->settings->date))
					$newValue = null;
				else if ($template->settings->date == '_created_at')
					$newValue = DB::raw('"created_at"');
				else
					$newValue = DB::raw('("datum"#>>\'{'.$template->settings->date.',value}\')::timestamp');
				$affected = Report::where('template_id', '=', $template->id)->update(array('main_date' => $newValue));
			}
			
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('template.show', array( $template->id )),
			), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Template $template)
	{
		// TODO FIX THIS
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		$template->delete();

		return $this->json( array('status' => 'OK'), 200 );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function clean(Template $template)
	{
		// TODO FIX THIS
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		$query = DB::raw("UPDATE reports SET deleted_at = NOW() WHERE template_id = " . $template->id);
		DB::update($query);

		return Redirect::to(URL::route('template.show', array( $template->id )));
	}
	
	/**
	 * Generamos las IDS de los campos.
	 * 
	 * @param type $fields
	 */
	protected function generateFieldsId( $fields )
	{
		// Generamos ID de todos los campos
		$id = uniqid();
		$contador = 0;

		foreach( $fields as & $field )
		{
			if( ! isset($field->id) ) 
			{
				//$field->id = uniqid();
				$field->id = $id . $contador++;
			}
			
			if( isset($field->options) )
			{
				foreach( $field->options as & $option )
				{
					if( ! isset($option->id) ) 
					{
						//$option->id = uniqid();	
						$option->id = $id . $contador++;
					}
				}
			}
		}
		
		return $fields;
	}
	
}
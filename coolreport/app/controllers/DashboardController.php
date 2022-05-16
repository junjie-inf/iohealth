<?php

use Carbon\Carbon;

class DashboardController extends BaseController {

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
		$dashboard = Auth::user()->dashboards->first();
		if ($dashboard)
			return Redirect::route('dashboard.show', array($dashboard->id));
		else
			return Redirect::route('dashboard.create');
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Dashboard $dashboard)
	{
		if( Request::ajax() )
		{
			return $this->json( array('status' => 'OK', 'data' => $group->datesForHumans()->toArray()), 200 );
		}
		
		$renders = [];
		$dic = new DashItemController;
		foreach ($dashboard->datum->items as $row)
		{
			foreach ($row as $element)
			{
				//dd(is_null(DashItem::find($element->id)));
				if (! is_null(DashItem::find($element->id)) )
					$renders[$element->id] = $dic->render(DashItem::find($element->id)->datum);
			}
		}
		
		return $this->layout('pages.dashboard.show')
					->with('dashboard', $dashboard->datesForHumans())
					->with('dashboards', Auth::user()->dashboards->sortBy('title'))
					->with('renders', $renders);
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return $this->layout('pages.dashboard.create')
				->with('dashitems', DashItem::all());
	}
	
	/**
	 * Show the form for editing resources.
	 *
	 * @return Response
	 */
	public function edit(Dashboard $dashboard)
	{
		return $this->layout('pages.dashboard.edit')
				->with('dashitems', DashItem::all())
				->with('dashboard', $dashboard);
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
		
		$dashboard = new Dashboard;
		
		// Validamos y asignamos campos
		$v = $dashboard->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		$datum = json_decode( Input::get('dashboard') );

		// TODO: Comprobar datum
// 		// Error si no se ha rellenado al menos un campo
// 		if( count($fields) <= 0 )
// 		{
// 			return $this->json( array('status' => 'ERROR', 'messages' => array('Template Fields' => Lang::get('validation.required', array('attribute' => 'Template Fields')))), 400 );
// 		}		
// 		
// 		// Generamos las IDs.
// 		$fields = $this->generateFieldsId($fields);
// 		
// 		// Comprobamos si estan todas las options
// 		foreach( $fields as & $field )
// 		{
// 			// Error si no se ha rellenado al menos un campo
// 			if( isset($field->options) && count($field->options) <= 0 )
// 			{
// 				return $this->json( array('status' => 'ERROR', 'messages' => array($field->type => Lang::get('validation.required', array('attribute' => $field->type)))), 400 );
// 			}
// 		}
		$dashboard->user_id = Auth::user()->getKey();
		$dashboard->title = $datum->title;
		$dashboard->datum = $datum;
		
		if( $dashboard->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('dashboard.show', array( $dashboard->id )), 
				'id' => $dashboard->id
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
	public function update(Dashboard $dashboard)
	{
		// TODO FIX THIS
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		// Validamos y asignamos campos
		$v = $dashboard->parse( 'update', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		$datum = json_decode( Input::get('dashboard') );
		
		$dashboard->user_id = Auth::user()->getKey();
		$dashboard->title = $datum->title;
		$dashboard->datum = $datum;
		
		// Guardamos el modelo
		if( $dashboard->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('dashboard.show', array( $dashboard->id )),
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
	public function destroy(Dashboard $dashboard)
	{
		$dashboard->delete();
		
		Event::fire('dashboard.destroy', $dashboard);

		return $this->json( array('status' => 'OK'), 200 );
	}
}
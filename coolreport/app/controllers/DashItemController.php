<?php

use Carbon\Carbon;
use Nimbeo\QueryGenerator;

class DashItemController extends BaseController {

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
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Auth::user();
		if ($user->admin)
			$data = DashItem::all()->sortBy('title');
		else
			$data = $user->dashitems->sortBy('title');

		return $this->layout('pages.dashitem.index')
					->with('data', $data);
	}
	
	private function getSqlField($templateid, $fieldid, $queryGen, $datetype = null)
	{
		return $qg->getSqlField($templateid, $fieldid, $datetype);
	}
	
	public function query($datum)
	{
		$queryGen = new QueryGenerator;
		
		foreach ($datum->data->from as $from)
		{
			$queryGen->addTemplate($from->template, object_get($from,'condition'));
		}
		
		//Group
		foreach ($datum->data->group_by as $group)
		{
			$dateType = object_get($group, 'datetype', null);
			$queryGen->addGroupBy($group->template, $group->column, $dateType);
		}
		
		$header = [];

		// En caso de que sea un mapa, añado latitude y longitude a pelo para que el usuario no tenga que seleccionarlo
		if ($datum->display->type == 'map')
		{
			$queryGen->addSelect($datum->display->options->geolocationFrom, '_latitude', null);
			$queryGen->addSelect($datum->display->options->geolocationFrom, '_longitude', null);

			$queryGen->addGroupBy($datum->display->options->geolocationFrom, '_latitude', null);
			$queryGen->addGroupBy($datum->display->options->geolocationFrom, '_longitude', null);
		}

		foreach ($datum->data->select as $column)
		{
			$header[] = $column->alias;
			if ($column->type == 'json' || $column->type == 'field')
			{
				$queryGen->addSelect($column->template, $column->column, object_get($column, 'datetype'));
			}
			else if ($column->type == 'aggregate')
			{
				$queryGen->addAggregate($column->function, object_get($column,'template'), object_get($column,'column'), object_get($column,'filter'));
			}
			else if ($column->type == 'expression')
			{
				$queryGen->addSelectExpression($column->expression);
			}
		}
		
		foreach ($datum->data->order_by as $order)
		{
			$queryGen->addOrderBy($order->direction, $order->template, $order->column, object_get($order, 'datetype'));
		}
		
		if (isset($datum->data->filter) && $datum->data->filter)
		{
			$queryGen->addWhere($datum->data->filter);
		}

		$sql = $queryGen->toSql();
		$objData = (DB::select(DB::raw($sql)));

		//Convert $data to an array, change select fields to name (not id)
		$data = [];

		foreach ( $objData as $row )
		{
			$newrow = [];
			foreach ( $row as $k => $d )
			{
				$value = $d;
	
				// FIXME chanchu para mostrar mapas
				if ($datum->display->type != 'map')
				{
					$index = intval(substr($k,1));
					
					$metadata = $datum->data->select[$index];
					$template = Template::find(object_get($metadata, 'template'));
					
					// FIXME chanchu para mostrar el titulo en las consultas del dashitems
					if ( $metadata->type == 'field' && $metadata->column == '_title' )
					{
						$value = Report::find($d)->title;
					}
				
					switch ($metadata->datatype) {
					case 'radio':
						$fieldMetadata = $template->getFieldById($metadata->column);
						foreach($fieldMetadata->options as $opt)
						{
							if ($opt->id == $d)
							{
								$value = $opt->value;
								break;
							}
						}
						break;
					case 'select':
					case 'checkbox':
						$fieldMetadata = $template->getFieldById($metadata->column);
						$multi = json_decode($d);
						
						$value = '';
						foreach($fieldMetadata->options as $opt)
						{
							if ( is_null($d) )
							{
								$value = 'Null';
							}
							else
							{
								if ($opt->id == $d)
								{
									$value = $opt->value;
									break;
								}
								else if ( ! is_null($multi) )
								{
									foreach ($multi as $elem)
									{
										if ($opt->id == $elem)
										{
											$value .= $opt->value . ', ';
											break;
										}
									}								
								}
							}
						}

						$value = trim($value, ', ');
						break;
					}
				}

				$newrow[] = $value;
			}
			$data[] = $newrow;
		}

		return [$header, $data];
	}

	/**
	 * Special query for political maps
	 *
	 * @param $datum
	 *
	 * @return $header
	 * @return $data
	 */
	public function queryPolitical($datum)
	{
		// Get map geojson
		$map = Map::find($datum->display->options->map);
		$mapc = new MapController();
		$mapGeo = $mapc->geojson($map->hash)->getData();

		// Iterate over all map zones
		$data = [];

		foreach ($mapGeo as $zone)
		{
			$queryGen = new QueryGenerator;

			// Templates
			foreach ($datum->data->from as $from)
				$queryGen->addTemplate(
					$from->template, object_get($from,'condition'));

			/* dd(DB::select(DB::raw($queryGen->getSqlField($datum->data->from[0]->template, '_latitude')))); */

			// Group by
			foreach ($datum->data->group_by as $group)
			{
				$dateType = object_get($group, 'datetype', null);
				$queryGen->addGroupBy($group->template, $group->column, $dateType);
			}

			// Use format [Lat, Long, DATA]
			// DATA should be an aggregate
			$header = [];
			$aggr = null;
			foreach ($datum->data->select as $column)
			{
				$header[] = $column->alias;

				if ($column->type == 'json' || $column->type == 'field')
					$queryGen->addSelect(
						$column->template,
						$column->column,
						object_get($column, 'datetype'));

				else if ($column->type == 'aggregate')
				{
					$aggr = $column->function;

					$queryGen->addAggregate(
						$column->function,
						object_get($column,'template'),
						object_get($column,'column'),
						object_get($column,'filter'));
				}

				else if ($column->type == 'expression')
					$queryGen->addSelectExpression($column->expression);
			}

			// Order by
			foreach ($datum->data->order_by as $order)
				$queryGen->addOrderBy(
					$order->direction,
					$order->template,
					$order->column,
					object_get($order, 'datetype'));

			if (isset($datum->data->filter) && $datum->data->filter)
				$queryGen->addWhere($datum->data->filter);

			$geometry = json_encode($zone->geometry);
			if ($geometry == 'null')
				continue;

			$queryGen->addWhere("ST_CONTAINS(ST_GeometryFromText(ST_AsText(ST_GeomFromGeoJSON('". $geometry ."')), 4326), _cr_t".$datum->display->options->geolocationFrom."_.main_geo::geometry) = true");

			// Get raw data
			$sql = $queryGen->toSql();
				
			$zoneValue = DB::select(DB::raw($sql))[0]->f0;

			// Append to result
			array_push($data, $zoneValue);
		}

		return [$header, $data];
	}

    public function renderChart($datum, $header, $data)
	{
		$display = $datum->display;

		if ($display->type == 'table') {
			 $dFilterValues = [];
            if (isset($display->options->dFilter))
            {
                $queryGen = new QueryGenerator;
                $queryGen->addTemplate($display->options->dFilter->template);
                $queryGen->addSelect($display->options->dFilter->template, $display->options->dFilter->field, null);
                $queryGen->addGroupBy($display->options->dFilter->template, $display->options->dFilter->field, null);
                $queryGen->addOrderBy('asc', $display->options->dFilter->template, $display->options->dFilter->field, null);
                $sql = $queryGen->toSql();
                $objData = (DB::select(DB::raw($sql)));
                foreach($objData as $row)
                {
                    $dFilterValues[] = $row->f0;
                }
            }

			return $this->layout('dashitems.table')
						->with('options', $display->options)
						->with('data', $data)
						->with('header', $header)
						->with('datum', $datum)
						->with('dFilterValues', $dFilterValues)
						->with('chartId', rand());
		} else if ($display->type == 'pie') {
			$pieData = [];
			$colLabels = $display->options->labels;
			$colValues = $display->options->values;
			foreach ( $data as $d )
			{
				$pieData[] = array(
					'l' => $d[$colLabels],
					'v' => $d[$colValues],
				);
			}
			return $this->layout('dashitems.pie')
						->with('data', $pieData)
						->with('chartId', rand());
		} else if ($display->type == 'line') {
            $dFilterValues = [];
            if (isset($display->options->dFilter))
            {
                $queryGen = new QueryGenerator;
                $queryGen->addTemplate($display->options->dFilter->template);
                $queryGen->addSelect($display->options->dFilter->template, $display->options->dFilter->field, null);
                $queryGen->addGroupBy($display->options->dFilter->template, $display->options->dFilter->field, null);
                $queryGen->addOrderBy('asc', $display->options->dFilter->template, $display->options->dFilter->field, null);
                $sql = $queryGen->toSql();
                $objData = (DB::select(DB::raw($sql)));
                foreach($objData as $row)
                {
                    $dFilterValues[] = $row->f0;
                }
            }

			return $this->layout('dashitems.line')
						->with('options', $display->options)
						->with('xCol', $datum->data->select[$display->options->axis->x->data])
						->with('data', $data)
						->with('datum', $datum)
						->with('dFilterValues', $dFilterValues)
						->with('chartId', rand());
		} else if ($display->type == 'bar') {
            $dFilterValues = [];
            if (isset($display->options->dFilter))
            {
                $queryGen = new QueryGenerator;
                $queryGen->addTemplate($display->options->dFilter->template);
                $queryGen->addSelect($display->options->dFilter->template, $display->options->dFilter->field, null);
                $queryGen->addGroupBy($display->options->dFilter->template, $display->options->dFilter->field, null);
                $queryGen->addOrderBy('asc', $display->options->dFilter->template, $display->options->dFilter->field, null);
                $sql = $queryGen->toSql();
                $objData = (DB::select(DB::raw($sql)));
                foreach($objData as $row)
                {
                    $dFilterValues[] = $row->f0;
                }
            }

			return $this->layout('dashitems.bar')
						->with('options', $display->options)
						->with('xCol', $datum->data->select[$display->options->axis->x->data])
						->with('data', $data)
						->with('datum', $datum)
						->with('dFilterValues', $dFilterValues)
						->with('chartId', rand());
		} else if ($display->type == 'indicator') {
			$indicatorData = [];
			$colLabels = $display->options->labels;
			$series = $display->options->series;
			foreach ( $data as $d )
			{
				// Fill data for individual pies
				$pieData = [];
				foreach ( $series as $s )
				{
					$pieData[] = array(
						'label' => $s->title,
						'value' => $d[$s->data],
					);
				}

				// Add legend and complete pie data
				$indicatorData[] = array(
					'label'		 	=> $d[$colLabels],
					'pie'			=> $pieData,
				);
			}
			return $this->layout('dashitems.indicator')
						->with('data', $indicatorData)
						->with('chartId', rand());
		}
	}

    public function renderMap($datum, $header, $data)
    {
        $display = $datum->display;
        
        //FIXME: Asume que el orden de campos es Id, Lat, Lng, Valor
        $mapData = [];
        foreach($data as $row)
        {
            $mapData[] = array(
                'lat' => floatval($row[0]),
                'lon' => floatval($row[1]),
                'm' => floatval($row[2])
            );
        }
        
        $map = Map::find($display->options->map);
        return $this->layout('dashitems.map')
                ->with('map_url', URL::route('map.rawGeojson',$map->hash))
                ->with('data', $mapData)
                ->with('chartId', rand());
    }

    public function renderPoliticalMap($datum, $header, $data)
    {
        $display = $datum->display;

        $map = Map::find($display->options->map);
        
        return $this->layout('dashitems.mappolitic')
                ->with('map_url', URL::route('map.rawGeojson',$map->hash))
                ->with('data', $data)
                ->with('chartId', rand());
    }
    
    public function render($datum)
    {
		if (isset($datum->political) && $datum->political)
		{
			list($header, $data) = $this->queryPolitical($datum);
		}
		else
		{
			list($header, $data) = $this->query($datum);
		}

        $display = $datum->display;
        
        //FIXME: Determinar esto en función de la opción del primer paso del dashitem
        //De momento, cambio el campo display manualmente en la DB
        //"display": {"type": "map", "options": {"map": <idMapaJson>, ...}}
        if ($display->type == 'map')
        {
        	if (isset($datum->political) && $datum->political)
			{
				return $this->renderPoliticalMap($datum, $header, $data);
			}
			else				
	    	{
            	return $this->renderMap($datum, $header, $data);
            }
		}
        else
        {
            return $this->renderChart($datum, $header, $data);
        }
    }
	
	/**
	 * Previews an item given its datum
	 */
	public function preview()
	{
		return $this->render(json_decode(Input::get('d')));
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(DashItem $dashboard)
	{
		if( Request::ajax() )
		{
			return $this->json( array('status' => 'OK', 'data' => $group->datesForHumans()->toArray()), 200 );
		}		
		
		$render = $this->render($dashboard->datum);
		
		return $this->layout('pages.dashitem.show')
					->with('dashboard', $dashboard->datesForHumans())
					->with('render', $render);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$templates = Template::all();
		$maps = Map::all();
		
		return $this->layout('pages.dashitem.create')
				->with('templates', $templates)
				->with('maps', $maps);
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function edit(DashItem $dashitem)
	{
		$templates = Template::all();
		$maps = Map::all();
		
		return $this->layout('pages.dashitem.edit')
				->with('templates', $templates)
				->with('dashitem', $dashitem)
				->with('maps', $maps);
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(DashItem $dashItem)
	{
		$dashItem->delete();
		
		Event::fire('dashitem.destroy', $dashItem);

		return $this->json( array('status' => 'OK'), 200 );
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
		
		$dashboard = new DashItem;
		
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
				'redirect' => URL::route('dashitem.show', array( $dashboard->id )), 
				'id' => $dashboard->id
			), 200 );
		}
		
		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 ); // Definir bien este error
	}

	/**
	 * Update a resource in storage.
	 *
	 * @return Response
	 */
	public function update(DashItem $dashboard)
	{
		// TODO FIX THIS
		if( ! Auth::user()->admin )
		{
			return App::abort(403, 'Forbidden');
		}
		
		// Validamos y asignamos campos
		$v = $dashboard->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		$datum = json_decode( Input::get('dashboard') );

		$dashboard->user_id = Auth::user()->getKey();
		$dashboard->title = $datum->title;
		$dashboard->datum = $datum;
		
		if( $dashboard->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('dashitem.show', array( $dashboard->id )), 
				'id' => $dashboard->id
			), 200 );
		}
		
		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 ); // Definir bien este error
	}
}

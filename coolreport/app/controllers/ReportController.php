<?php

use Carbon\Carbon;

class ReportController extends BaseController {

	/**
	 * Trait FollowableController
	 */
	use FollowableController;

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
		$this->beforeFilter('permission:VIEW_REPORTS,report', array('only' => array('show', 'follow')));
		$this->beforeFilter('permission:MANAGE_REPORTS,report', array('only' => array('create', 'store', 'edit', 'update', 'destroy')));
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{	
		$title='Informe';
		$input=Input::get('template');

		$campos = [];

		// Obtener el nombre del template si se busca por template
		if ($input) {
			$template=Template::where('id', $input)->get()->first();
			if($template)
			{
				$title = $template->title;
				$campos = $template->getShowFields();
			}
		}

		if( Request::ajax() )
		{
			/*$reports = Report::query();
			$reports->select(array('*', DB::raw('ST_AsGeoJSON(main_geo) as geo')));
			$reports->whereNull('deleted_at');
			$data = $reports->get();
			*/
			$data =  array(
						'title' => $title,
						'campos' => $campos
					);

			return $this->json( array('status' => 'OK', 'data' => $data), 200 );
		}
		return $this->layout('pages.report.index')
			->with('title', $title)
			->with('campos', $campos);
	}

	// public function tableIndex()
	public function table()
	{
		$template_id = Input::get('template');
		$template=Template::where('id', $template_id)->get()->first();
		if($template)
		{
			$title = $template->title;
			$campos = $template->getShowFields();
		}
		
		$query = Report::query();
		
		$permission = Auth::user()->getPermissionValue('VIEW_REPORTS');
		
		$query->whereUserId(Auth::user()->getKey()); // TODO: verificar si esto funciona. dudosa funcionalidad :D
	
		$query->whereNull('deleted_at');
		
		if ($template_id != '')
		{
			$query->where('template_id', '=', intval($template_id));
		}

		$count = $query->count();
		$query->orderBy('created_at', 'desc');
		
		$reports = $query->get();
		
		$data = [];
		foreach ($reports as $r)
		{
			$datum = array();
			foreach ($r->datum as $key => $value) {
				$datum[$key] = $value->value;
			}
			$row = array(
				'id' => $r->id,
				'datum' => $datum,
				'created_at' => $r->getRawAttribute('created_at'),
				'template' => $r->template->title,
				'user' => $r->user->getFullName(),
				);
			$data[] = $row;
		}
		
		$response = array(
			'fields' => $campos,
			'data' => $data,
		);
		
		return $response;
	}

	public function table1()
	{
		/* Modes:
		 * all: all reports
		 * user: all reports of 'user'
		 * my: all reports of logged user
		 * template: all reports of 'template'
		 * geo: all reports in 'geo'
		 * linked: all reports of 'template' which link to 'report' from 'field' 
		 */
		$draw = intval(Input::get('draw'));
		$offset = intval(Input::get('start'));
		$limit = intval(Input::get('length'));
		$mode = Input::get('mode');
		$template = Input::get('template');
		$columns = Input::get('columns');
		$q = Input::get('search')['value'];
		
		$query = Report::query();
		
		// Modificación del permiso para que un usuario tipo 'User' pueda crear registros seleccionando el tipo de enfermedad y tratamiento (no obligatorio).
		// Template = 11 (creación)
		$permission =  ($template == 8 || $template == 2 || $template == 43 ) ? "ALL" : Auth::user()->getPermissionValue('VIEW_REPORTS');

		// Permisos del usuario
		if( $mode == 'user' )
		{
			$query->whereUserId( intval(Input::get('user')) );
		}
		else if( $mode == 'my' || $permission == 'OWN' )
		{
			$query->whereUserId(Auth::user()->getKey()); // TODO: verificar si esto funciona. dudosa funcionalidad :D
		}
		else if( $permission == 'GROUP' )
		{
			$query->whereIn('user_id', Auth::user()->group->users()->lists('id'));
		}
		$query->whereNull('deleted_at');

		// search
		if ($q != '')
		{
			$query->where(function($oq) use ($q) {
				$oq
					->where(DB::raw('datum::text'), 'ILIKE', '%'.$q.'%')
					->orWhere(DB::raw("geodata#>>'{formatted_address}'"), 'ILIKE', '%'.$q.'%');
			});
		}
		
		if ($mode == 'geo')
		{
			$grid_threshold = 0.00001;
			$geo = json_encode(Input::get('geo'));
			$query->whereRaw("ST_SnapToGrid(ST_GeomFromGeoJSON('$geo')::geography::geometry,$grid_threshold,$grid_threshold)".
				"=ST_SnapToGrid(main_geo::geometry,$grid_threshold,$grid_threshold)");
		}
		
		if ( Auth::user()->role_id == '4' && $template == '51' )
		{
			$query->where('template_id', '=', intval($template))
					->where('user_id', '=', intval(Auth::user()->id));
		}
		elseif ($template != '')
		{
			$query->where('template_id', '=', intval($template));
		}
		
		if ($mode == 'linked')
		{
			$field = Input::get('field');
			$report = Input::get('report');
			$query->where(DB::raw("datum#>>'{".$field.",value}'"), '=', $report);
		}
		
		$count = $query->count();
		
		foreach(Input::get('order') as $order)
		{
			$name = $columns[$order['column']]['name'];
			if ($name == 'address')
			{
				$query->orderBy(DB::raw("geodata#>>'{formatted_address}'"), $order['dir']);
			}
			else
			{
				// Por defecto en OIGO
				$query->orderBy('created_at', 'desc');
				//$query->orderBy($name, $order['dir']);
			}
		}
		
		$reports = $query->skip($offset)->take($limit)->get()->datesForHumans();
		$reports->loadCountableRelations('comments');
		
		$data = [];
		foreach ($reports as $r)
		{
			$datas = $r->getReadableDatum();

			/* METHOD */
			foreach ($datas as $key => $field)
			{


				if( $field['url']!='' )
				{
					/* Se trata de un informe asociado */
					$ref_id = $field['value'];
					$text_value = DB::select(DB::raw("SELECT id, template_id, datum from reports where id= ".$ref_id))[0];
					$template_value = $text_value->template_id;

					/* Casos particulares para cada template (distintos id´s) */
					if( $template_value == 10 ) // Datos del paciente
					{
						$u_name='';
						$u_surname='';
						if ( isset(json_decode($text_value->datum)->{'5889efb0944a91'}) )
							{$u_name = json_decode($text_value->datum)->{'5889efb0944a91'}->value;}
						if ( isset(json_decode($text_value->datum)->{'59143940683680'}) )
							{$u_surname = json_decode($text_value->datum)->{'59143940683680'}->value;}
						$value = $u_surname . " ," . $u_name;
					}
					elseif ( $template_value == 43 ) // Nombre del medicamento 
					{
						$value='';
						if ( isset(json_decode($text_value->datum)->{'59e60422ec67e0'}) )
							{$value = json_decode($text_value->datum)->{'59e60422ec67e0'}->value;}
					}
					elseif ( $template_value == 2 ) // Nombre de la enfermedad
					{
						$enfermedad='';
						$tipo='';
						$value='';
						if( isset(json_decode($text_value->datum)->{'5889e63a4646c0'}) )
						{$enfermedad = json_decode($text_value->datum)->{'5889e63a4646c0'}->value;}
						//if ( get_object_vars(json_decode($text_value->datum))['5922b08ed8df00']->value !=''  )
						if( isset(json_decode($text_value->datum)->{'5922b08ed8df00'}) )
						{$tipo = json_decode($text_value->datum)->{'5922b08ed8df00'}->value;}
						$value = $enfermedad ." ". $tipo;
					}
					elseif ( $template_value == 8 ) 
					{	
						$value='';
						if( isset(json_decode($text_value->datum)->{'5889ea714ccc92'}) )
						{$value = json_decode($text_value->datum)->{'5889ea714ccc92'}->value;}
					}
					$template_value = 0;
					$datas[$key]['value'] = $value;
					$value = '';
				}
				else
				{
					$datas[$key]['value'] = $field['value'];
				}
				/* END IF */
			}/* END foreach */

			$row = array(
				'DT_RowClass' => 'action-data',
				'DT_RowData' => array(
					'id' => $r->id,
					'user_url' => $r->user->getUrl(),
					'url' => URL::route('report.show', $r->id),
					'followed' => $r->getUsersFromFollowers()->contains(Auth::user()->getKey()),
					'removable' => $r->removable,
					'editable' => $r->editable,
					'type' => 'report'
				),
				'title' => $r->title,
				// Usa 'campos' para mostrar el contenido de las tablas p.ej: /report?template=...
				//'campos' => $r->getReadableDatum(),
				'campos' => $datas,				
				'address' => ($r->geodata != null) ? $r->geodata->formatted_address : null,
				'created_at' => $r->getRawAttribute('created_at'),
				'template' => $r->template->title,
				'user' => $r->user->getFullName(),
				'comments' => $r->comments);
			$data[] = $row;
		}
		
		
		$response = array(
			'draw' => $draw,
			'recordsTotal' => $count,
			'recordsFiltered' => $count,
			'data' => $data,
			'first' => $offset,
			'last' => $offset + count($data) - 1
		);
		
		return $response;
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function search()
	{
		$wheres = array('deleted_at IS NULL');

		if (Input::has('templates'))
			$wheres[] = "template_id IN (" . implode( ',', Input::get('templates') ) . ")";
		
		// Filtro por permisos
		$permission = Auth::user()->getPermissionValue('VIEW_REPORTS');

		if( $permission == 'GROUP' )
		{
			$wheres[] = 'user_id IN (' . implode(',', Auth::user()->group->users()->lists('id')) . ')';
		}
		else if( $permission == 'OWN' )
		{
			$wheres[] = 'user_id = '.Auth::user()->getKey();
		}
		
		$grid_resolution = 0.01;
		
		// Filtro por localización
		if( Input::has('maxlatitude') && Input::has('minlatitude') && Input::has('maxlongitude') && Input::has('minlongitude') )
		{
			//Generamos un punto intermedio para cuando el área a mostrar es mayor de 180º de longitud
			//ya que geography siempre coge el camino más corto
			$intermediateLng = (Input::get('minlongitude') + Input::get('maxlongitude'))/2;
			$intermediateLngMax = $intermediateLngMin = $intermediateLng;
			
			//En el caso de que el rectángulo atraviese la línea de cambio de fecha, el valor intermedio
			//hay que hacer la media por el otro lado de la tierra.
			if (Input::get('minlongitude') > Input::get('maxlongitude'))
			{
				$intermediateLngMax = 180;
				$intermediateLngMin = -180;
			}
			
			$wheres[] = '(ST_MakeEnvelope('.
				Input::get('minlongitude').','.
				Input::get('minlatitude').','.
				$intermediateLngMin.','.
				Input::get('maxlatitude').') && main_geo::geometry OR '.
				
				'ST_MakeEnvelope('.
				$intermediateLngMax.','.
				Input::get('minlatitude').','.
				Input::get('maxlongitude').','.
				Input::get('maxlatitude').') && main_geo::geometry)';
				
			//Resolución de la rejilla
			$grid_resolution = abs((Input::get('maxlongitude') - Input::get('minlongitude')) / 20.0);
		}
		
		// Calculamos la fecha
		if( Input::has('maxdate') && Input::has('mindate') )
		{
			$maxdate = Carbon::createFromTimeStamp( Input::get('maxdate') )->addDay()->subSecond();
			$mindate = Carbon::createFromTimeStamp( Input::get('mindate') );
			
			$wheres[] = "(main_date IS NULL OR main_date BETWEEN '$mindate' AND '$maxdate')";
		}
		$where = implode(' AND ',$wheres);
		
		$grid_threshold = 0.00001;
		if ($grid_resolution < 0.0005)
		{
			$query = <<<EOT
				SELECT 
					ST_AsGeoJSON(ST_SnapToGrid(main_geo::geometry,$grid_threshold,$grid_threshold)) AS main_geo,
					count(*) AS reports_per_geo
				FROM reports 
				WHERE 
					$where
				GROUP BY ST_SnapToGrid(main_geo::geometry,$grid_threshold,$grid_threshold)
EOT;
			$grids = DB::select(DB::raw($query));
			$data = array();
			foreach($grids as $grid)
			{
				$data[] = array(
					'type' => 'reports',
					'geo' => json_decode($grid->main_geo),
					'count' => $grid->reports_per_geo
				);
			}
		}
		else
		{
			$query = <<<EOT
				WITH r AS (
					SELECT
						ST_SnapToGrid(main_geo::geometry,$grid_threshold,$grid_threshold) AS main_geo,
						count(*) AS reports_per_geo
					FROM reports 
					WHERE 
						$where
					GROUP BY ST_SnapToGrid(main_geo::geometry,$grid_threshold,$grid_threshold)
				)
				SELECT
					ST_AsGeoJSON(
						ST_SnapToGrid(ST_Centroid(main_geo), $grid_resolution, $grid_resolution)
					) AS grid_center,
					count(*) AS geos_per_grid,
					sum(reports_per_geo) AS reports_per_grid,
					ST_AsGeoJSON(min(ST_GeogFromWKB(main_geo)::text)) AS first_geo
				FROM r
				GROUP BY
					ST_SnapToGrid(ST_Centroid(main_geo), $grid_resolution, $grid_resolution);
EOT;
			$grids = DB::select(DB::raw($query));
			$data = array();
			foreach($grids as $grid)
			{
				if ($grid->geos_per_grid == 1)
				{
					//Carga reports de un punto dado
					//$reports = Report::query()->whereRaw($where)->where('main_geo', '=', DB::raw("ST_GeomFromGeoJSON('".$grid->first_geo."')"))->count();
					/*$reports->datesForHumans()->show('datum');
					$reports->loadCountableRelations('comments');
					$shortReports = array_map(function($r) {
						return array(
							'geo' => $r['geo'],
							'title' => $r['title'],
							'created_at' => $r['created_at'],
							'geodata' => $r['geodata'],
							'id' => $r['id'],
							'comments' => $r['comments'],
							);
					}, $reports->toArray());*/
					
					// Añadir report(s)
					$data[] = array(
						'type' => 'reports',
						'geo' => json_decode($grid->first_geo),
						'count' => $grid->reports_per_grid
					);
				}
				else
				{
					// Añadir cluster
					$data[] = array(
						'type' => 'cluster',
						'geo' => json_decode($grid->grid_center),
						'count' => $grid->reports_per_grid);
				}
			}
		}
		// Nota: No vale tabular el EOT
		return $this->json( array(
			'status' => 'OK',
			'data' => $data,
			'meta' => array(
				'grid' => $grid_resolution)), 200 );
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
    public function create($template = null,  $link = null, $linkTitle=null, $linkTemplate = null)
	{

		$registerTemplates = DB::select(DB::raw("SELECT id FROM templates WHERE settings->>'register' = 'true'"));
		$registerTemplates = array_map(function($v){return $v->id;}, $registerTemplates);
		array_push($registerTemplates,10);	//Persona
		array_push($registerTemplates,11);	//Paciente
		$registerTemplates = Template::whereIn('id',$registerTemplates)->get();

		if( Auth::user()->admin )
			$templates = Template::all()->sortBy('title');
		else{
			$templates = $registerTemplates;
		}

		$latitude = Input::has('latitude') ? Input::get('latitude') : Config::get('local.map.default.latitude'); 
		$longitude = Input::has('longitude') ? Input::get('longitude') : Config::get('local.map.default.longitude');

		return $this->layout('pages.report.create')
				->with('templates', $templates)
				->with('registerTemplates', $registerTemplates)
				->withLatitude($latitude)
				->withLongitude($longitude)
				->with('selectedTemplate', $template)
                ->with('link', $link)
                ->with('linkTitle', $linkTitle)
                ->with('linkTemplate', $linkTemplate);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// Creamos el objeto
		$report = new Report;

		// Validamos y asignamos campos
		$v = $report->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		// Asociamos otras variables
		// Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36
		if( ! Input::has('device') )
		{
			$report->device = ''; // TODO: analizar el user agent.
		}
		
		if( ! Input::has('platform') )
		{
			$report->platform = ''; // TODO: analizar el user agent.
		}
		
		if( ! Input::has('version') )
		{
			$report->version = Config::get('local.app.version');
		}
		
		$template = Template::find( Input::get('template') );
		
		// Asociamos relaciones externas

		if( ! Input::has('user') )
		{
			$report->user()->associate( Auth::user() );
		}else{
			$report->user()->associate( User::find(Input::get('user')) );
		}
		$report->template()->associate( $template );
		
		$report->geodata = json_decode(Input::get('geodata'));
		$report->datum = $this->compactDatum($template);

		//Extraer fecha principal
		if (!isset($template->settings->date))
			$newValue = null;
		else if ($template->settings->date == '_created_at')
			$newValue = DB::raw('now()');
		else
			$newValue = object_get($report->datum, $template->settings->date)->value;
		$report->main_date = $newValue;
		
		//Borrar localización si no es gelocalizado
		if (!$template->settings->geolocation)
		{
			$report->accuracy = null;
			$report->latitude = null;
			$report->longitude = null;
			$report->geodata = null;
		}
		else
		{
			if ( !Input::has('geo') )
			{
				//$report->main_geo = DB::raw('ST_Point('.$report->longitude.', '.$report->latitude.')::geography');
				$report->geo = Input::get('geo');
				$report->accuracy = null;
				$report->latitude = null;
				$report->longitude = null;
			}
			else
			{
				$report->main_geo = DB::raw("ST_GeomFromGeoJSON('". Input::get('geo') ."')::geography");
			}
		}

		// Gestion del usuario de Twitter
		if ($template->id == 4 && Auth::user()->admin)
		{
			// FIX para introducir la @ en los reports que vienen del WS
			if (substr($report->datum->{'57231a10192bc0'}->value, 0, 1) !== '@')
			{
				$datum = $report->datum;
				$datum->{'57231a10192bc0'}->value = '@' . $datum->{'57231a10192bc0'}->value;
				$report->datum = $datum;
			}

			$twitterUser = User::whereTwitter($report->datum->{'57231a10192bc0'}->value)->first();
			if (is_null($twitterUser))
			{
				$twitterUser = Auth::user();
			}

			$report->user()->associate($twitterUser);
		}


		// Push notifications
		// Comprobamos que la plantilla sea la de enviar un mensaje
		define ('DEVICES_REPORT', 52);
		define ('DEVICES_REPORT_PATIENT_ID', '5af1e21fce7020');
		define ('DEVICES_REPORT_DEVICE_ID', '5af1e2c8ecc5c0');


		define ('RECOMMENDATIONS_REPORT', 51);
		define ('RECOMMENDATIONS_REPORT_PATIENT_ID', '5a57bd971a3c60');
		define ('RECOMMENDATIONS_REPORT_MESSAGE', '5a57bd971a3c62');

		/** @link https://cordova-plugin-fcm.appspot.com */
		define ('FIREBASE_URL', 'https://fcm.googleapis.com/fcm/send');
		define ('FIRBASE_KEY', 'AIzaSyCSctZUhPKUqu9n38011ECar8hSbh98hBo');

		
		// Guardamos el modelo
		if( $report->save() )
		{
			// Fire the event
			Event::fire('report.store', $report);


			// If the report is the template
			if (RECOMMENDATIONS_REPORT == $template->id) {

				/** @var $datum Array The data of the report */
				$datum = $report->datum;

				
				/** @var $recommendation_patient_id int */
				$recommendation_patient_id = $datum->{RECOMMENDATIONS_REPORT_PATIENT_ID}->value;


				/** @var $recommendation_patient_message String */
				$recommendation_message = $datum->{RECOMMENDATIONS_REPORT_MESSAGE}->value;



				/** @var $sql_check_device String Retrieve the registered devices */
				$sql_get_patient_subcribed_devices = "
					SELECT 
						* 
					FROM 
						reports 
					WHERE 
						template_id = ? AND 
						datum->'" . DEVICES_REPORT_PATIENT_ID . "'->>'value' = ? AND
						deleted_at IS NULL
				";


				/** var $sql_get_patient_subcribed_devices_params Array */
				$sql_get_patient_subcribed_devices_params = [
						DEVICES_REPORT,
						$recommendation_patient_id
				];


				/** @var $result Obtain the devices */
				$devices_result = DB::select ($sql_get_patient_subcribed_devices, $sql_get_patient_subcribed_devices_params);


				foreach ($devices_result as $device_item) {

					/** @var $datum Array */
					$datum = json_decode ($device_item->datum, true);



					// Skip wrong forms
					if ( ! isset ($datum[DEVICES_REPORT_DEVICE_ID])) {
						continue;
					}
					

					/** @var $device_id int */
					$device_id = $datum[DEVICES_REPORT_DEVICE_ID]['value'];


					/** @var $firebase_fields JSON */

					$firebase_fields = json_encode ([
					    // 'to' => '/topics/reminders',
					    'registration_ids' => [$device_id],
					    
					    "notification" => [
					        "title" => "Nueva recomendación",
					        "body"  => $recommendation_message,
					        "icon"  => "fcm_push_icon"
					    ],
					    
					    'data' => [
					        "message" => 'test'
					    ]
					]);



					/** @var $firebase_headers Array */
	            	$firebase_headers = [
	            		'Authorization: key=' . FIRBASE_KEY,
	            		'Content-Type: application/json'
	    			];


	    			// Send CURL
					$ch = curl_init ();
	    			curl_setopt ($ch, CURLOPT_URL, FIREBASE_URL);
	    			curl_setopt ($ch, CURLOPT_POST, true);
					curl_setopt ($ch, CURLOPT_HTTPHEADER, $firebase_headers);
					curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt ($ch, CURLOPT_POSTFIELDS, $firebase_fields);
					curl_setopt ($ch, CURLOPT_VERBOSE, true);
					curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);


					$result = curl_exec ($ch);
					curl_close ($ch);


				}
			}


			// Puntuación Karma
			$user = $report->user;
	        $score = $user->score;

	        if (is_null($score))
	        {
	        	$score = new Score();	
	        }
			
			$score->score = $score->score + 1;
			$score->user()->associate( $user );

			$score->save();

			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('report.show', array( $report->id )), 
				'id' => $report->id
			), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Report $report)
	{
		// $report->visit();
		// Eager Loading
		$report->load('user', 'comments', 'comments.user', 'votes')->datesForHumans()->show('datum');

		$report->content = TemplateBuilder::with($report->template->fields)->answer($report->datum)->readonly()->build();
		//dd($report->toArray());
		if( Request::ajax() )
		{
			//dd($report->toArray());
			return $this->json( array('status' => 'OK', 'data' => $report->toArray() ), 200 );
		}

		// Analisis sintomas
		$diseases = [];
		$signs = [];
		if ( ($report->template_id == 2) && isset($report->datum->{'5821ee5b3b91f1'}) )
		{
			$signs_json = json_decode(file_get_contents("signs.json"), true);	

			$signs_detected = [];
			foreach ($signs_json as $sign=>$id)
			{
				if (preg_match("~\b". strtolower($sign) ."\b~", strtolower($report->datum->{'5821ee5b3b91f1'}->value)))
				{
					$signs[] = $sign;
					$signs_detected[] = $id;
				}
			}

			$ids_diseases = file_get_contents("http://nimbeo.com:8080/GalenoWS/rest/Diagnosis/diagnosis?signs=". implode(",", $signs_detected));
			$ids_diseases = $ids_diseases == "" ? null : explode(";", $ids_diseases);
			
			$diseases_json = json_decode(file_get_contents("diseases.json"), true);
			
			if (!is_null($ids_diseases))
			{
				foreach ($ids_diseases as $disease)
				{
					$diseases[] = $diseases_json[$disease];
				}
			}		
		}	
		
		$and = '';
		if( !Auth::user()->admin ){

			// Obtener solo los templates de registros de datos

			$registerTemplates = DB::select(DB::raw("SELECT id FROM templates WHERE settings->>'register' = 'true'"));
			$registerTemplates = array_map(function($v){return $v->id;}, $registerTemplates);
			array_push($registerTemplates,10);	//Persona
			array_push($registerTemplates,11);	//Paciente

			$and = 'AND id IN (' . implode(',', $registerTemplates) . ')';
		}

		$user = null;
		$enfermedadesAsociadas = null;
		if($report->template_id == 10){

			$user = User::find($report->datum->{'5889efb0944a90'}->value);

			// Enfermedades asociadas
			$q =  " SELECT e.*, e.datum->'5889e63a4646c0'->>'value' as nombre, p.datum->'5889f0a9092ea2'->>'value' as fecha  "
				."  FROM reports p, reports e  "
				."  WHERE p.template_id = 11  "
				."	      AND e.template_id = 2 "
				."	      AND (p.datum->'5889f0a9092ea1'->>'value')::int =  " . $report->id 
				."	      AND (p.datum->'5922aef15ccad0'->>'value')::int = e.id  "
				."	      AND e.deleted_at IS NULL  "
				."	      AND p.deleted_at IS NULL";
			$enfermedadesAsociadas = DB::select(DB::raw($q));

		}
//dd($enfermedadesAsociadas);

		// Load linked reports
		$linkedFields = DB::select(DB::raw("SELECT id AS template_id, title AS template_title, field->>'id' AS field_id, field->>'label' AS field_title FROM (SELECT *, jsonb_array_elements(fields) AS field FROM templates WHERE deleted_at IS NULL " . $and . ") a WHERE field->>'template' = :template"),
			array('template' => $report->template_id));

		$registerTemplates = DB::select(DB::raw("SELECT id FROM templates WHERE settings->>'register' = 'true'"));
		$registerTemplates = array_map(function($v){return $v->id;}, $registerTemplates);
		array_push($registerTemplates,10);
		$registerTemplates = Template::whereIn('id',$registerTemplates)->get();

		return $this->layout('pages.report.show')
				 ->with('data', $report)
				 ->with('linkedFields', $linkedFields)
				 ->with('registerTemplates', $registerTemplates)
				 ->with('user', $user)
				 ->with('diseases', $diseases)
				 ->with('signs', $signs)
				 ->with('enfermedadesAsociadas', $enfermedadesAsociadas);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Report $report)
	{
		$report->content = TemplateBuilder::with($report->template->fields)->answer($report->datum)->build();

		$user = null;
		if($report->template_id == 10){
			$user = User::find($report->datum->{'5889efb0944a90'}->value);
			$user = $user->datesForHumans();
		}

		$registerTemplates = DB::select(DB::raw("SELECT id FROM templates WHERE settings->>'register' = 'true'"));
		$registerTemplates = array_map(function($v){return $v->id;}, $registerTemplates);
		array_push($registerTemplates,10);
		$registerTemplates = Template::whereIn('id',$registerTemplates)->get();

		return $this->layout('pages.report.edit')
					->with('data', $report->datesForHumans())
					->with('registerTemplates', $registerTemplates)
					->with('user', $user);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Report $report)
	{
		// Validamos y asignamos campos
		$v = $report->parse( 'update', Input::all() );

		$IHealthID = null;
		if(isset($report->datum->IHealthID))
			$IHealthID = $report->datum->IHealthID;

		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		$report->geodata = json_decode(Input::get('geodata'));
		$datum = $this->compactDatum($report->template);

		if(isset($IHealthID))
			$datum->IHealthID = $IHealthID;

		$report->datum = $datum;

		$template = $report->template;
		if (!isset($template->settings->date))
			$newValue = null;
		else if ($template->settings->date == '_created_at')
			$newValue = $report->created_at;
		else
			$newValue = object_get($report->datum, $template->settings->date)->value;
		$report->main_date = $newValue;
		
		//Borrar localización si no es gelocalizado
		if (!$template->settings->geolocation)
		{
			$report->accuracy = null;
			$report->latitude = null;
			$report->longitude = null;
			$report->geodata = null;
		}
		else
		{
			if ( !Input::has('geo') )
			{
				//$report->main_geo = DB::raw('ST_Point('.$report->longitude.', '.$report->latitude.')::geography');
				$report->geo = Input::get('geo');
				$report->accuracy = null;
				$report->latitude = null;
				$report->longitude = null;
			}
			else
			{
				$report->main_geo = DB::raw("ST_GeomFromGeoJSON('". Input::get('geo') ."')::geography");
			}
		}
		
		// Guardamos el modelo
		if( $report->save() )
		{
			Event::fire('report.update', $report);

			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('report.show', array( $report->id )), 
			), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}

	/**
	 * Compacta los datos recibidos del input en un array de objetos.
	 * Si el tipo de field es un file, compactamos el json que recibimos.
	 * 
	 * Resultado:
	 *	[
	 *		{
	 *			"id" : "1",
	 *			"value" : ""
	 *		}, 
	 *		{
	 *			"id" : "2",
	 *			"value" : ""
	 *		}, 
	 *		{
	 *			"id" : "3",
	 *			"value" : ""
	 *		}, 
	 *		{
	 *			"id" : "4",
	 *			"value" : 
	 *			[
	 *				{
	 *					"hash" : "0123456789abcdef",
	 *					"filename" : ""
	 *				},
	 *				{
	 *					"hash" : "0123456789abcdef",
	 *					"filename" : ""
	 *				}
	 *			]
	 *		}
	 *	]
	 * 
	 * @param Template $template
	 * @return array
	 */
	protected function compactDatum(Template $template)
	{
		$datum = new stdClass();
		
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

				if( ! empty($value) || strlen($value) )
				{
					$datum->{$id} = (object) compact('id', 'value');
				}
			}
		}
		
		return $datum;
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Report $report)
	{
		$report->delete();
		
		Event::fire('report.destroy', $report);

		$follows = Follow::whereFollowableType('Report')->whereFollowableId($report->id)->get();

		foreach ($follows as $follow)
		{
			$follow->delete();
		}

		return $this->json( array('status' => 'OK'), 200 );
	}
	
	/**
	 * Upload a file before creating a report.
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
		$path = storage_path( Config::get('local.uploads.path') );
		
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
	
	/**
	 * Comprueba si existe un attachment con determinado hash.
	 * 
	 * @param Report $report
	 * @param type $hash
	 * @return type
	 */
	public function check()
	{
		// Storage
		$path = storage_path( Config::get('local.uploads.path') );
		
		// Attachment
		$attachment = Attachment::find( Input::get('hash') );
		
		// Movemos el archivo
		if( ! is_null($attachment) && file_exists( $path . '/' . $attachment->hash ) )
		{
			return $this->json( array('status' => 'OK'), 200 );
		}
		
		return App::abort(404, 'Page not found');
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function import()
	{
		$templates = Template::all()->sortBy('title');
		$encodes = array('ISO-8859-1', 'UTF-8', 'UTF-16');
		$origins = array('Base de datos', 'Servicio web', 'CSV');
		$db = array('MySQL', 'PostgreSQL', 'SQL Server');

		return $this->layout('pages.report.import')
				->with('templates', $templates)
				->with('encodes', $encodes)
				->with('origins', $origins)
				->with('db', $db)
				->with('errors', Session::get('errors'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function populate()
	{
		$error = false;
		$errors = '';
		$imports = [];

		//dd(json_decode(Input::get('csvpaste'), true));
		//dd(Input::all());

		if ( Input::get('template') == 0 )
		{
			$error = true;
			$errors = '<li style="margin-left: 20px">Debe especificar un template para realizar la importación.</li>';
		}

		if ( $error )
		{
			return Redirect::to('report/import')->with('errors', $errors);
		}
		else
		{
			$template = Template::find( Input::get('template') );
			$fields = $template->fields;

			$firstline = Input::get('header');

			$header = [];

			for ($i = 0; $i < count(Input::all())-3; $i++)
			{
				if ( ! is_null(Input::get('column'. $i)) )
				{
					$header[] = Input::get('column'. $i);
				}
			}
			// Aqui gestiono los distintos tipos de importacion
			if ( Input::get('data-select') == 'CSV' )
			{
				// Compruebo si es fichero o paste
				if ( Input::get('csv-type') == 'file' )
				{
					$csv = Input::file('file-import');
				}
				else
				{
					$csv = json_decode(Input::get('csv-paste'), true);
				}
				$array = ReportController::csv_to_array($csv, ';', $header, $firstline);

				if ( ! $array )
				{
					return Redirect::to('report/import')->with('errors','<li style="margin-left: 20px">Ha habido un problema con la codificación del fichero seleccionada.</li>');
				}				
			}
			else if ( Input::get('data-select') == 'Base de datos' )
			{
				$array = ReportController::pdo_to_array($header);
			}
			else
			{

			}

			DB::beginTransaction();

			$petadas = 0;
			foreach($array as $elementos)
			{		
				$report = new Report();
				
				// Asociamos localizacion en caso de que se hayan introducido coordenadas
				if ( ! empty($elementos['main_geo']) )
				{
					$geojson = json_decode($elementos['main_geo']);
					if ($geojson->type == 'Point')
					{
						$lat = $geojson->coordinates[1];
						$lng = $geojson->coordinates[0];
					}
					else if ($geojson->type == 'LineString')
					{
						$lat = $geojson->coordinates[0][1];
						$lng = $geojson->coordinates[0][0];
					}
					else if ($geojson->type == 'Polygon')
					{
						$lat = $geojson->coordinates[0][0][1];
						$lng = $geojson->coordinates[0][0][0];
					}
					$address = $lat . ',' . $lng;
					$request = file_get_contents("http://maps.google.com/maps/api/geocode/json?latlng=" . $address);
					$json = json_decode($request, true);
					while (empty($json['results'])){
						usleep(100000);
						$request = file_get_contents("http://maps.google.com/maps/api/geocode/json?latlng=" . $address);
						//dd($request);
						$json = json_decode($request, true);
						$petadas++;
					}
					
					$report->geodata = $json['results'][0];
					$report->main_geo = DB::raw("st_geomfromgeojson('".$elementos['main_geo']."')::geography");
				}
				else if ( ! empty($elementos['latitude']) && ! empty($elementos['longitude']) )
				{
					// Realizamos la peticion al API de Google Maps
					$address = $elementos['latitude'] . ',' . $elementos['longitude'];
					$request = file_get_contents("http://maps.google.com/maps/api/geocode/json?latlng=" . $address);
					//dd($request);
					$json = json_decode($request, true);
					while (empty($json['results'])){
						usleep(100000);
						$request = file_get_contents("http://maps.google.com/maps/api/geocode/json?latlng=" . $address);
						//dd($request);
						$json = json_decode($request, true);
						$petadas++;
					}

					// Asignamos los primeros valores obtenidos en la respuesta
					$report->latitude = $json['results'][0]['geometry']['location']['lat'];
					$report->longitude = $json['results'][0]['geometry']['location']['lng'];
					$report->geodata = $json['results'][0];
					$report->main_geo = DB::raw("st_geogfromtext('POINT(". $elementos['longitude'] ." ". $elementos['latitude'] .")')");
				}
				// Asociamos localizacion en caso de que se hayan introducido direcciones
				else if (  ! empty($elementos['address']) )
				{
					// Realizamos la peticion al API de Google Maps
					$address = urlencode($elementos['address']);
					//dd($csv);
					$request = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=" . $address);
					$json = json_decode($request, true);
					while (empty($json['results'])){
						usleep(100000);
						$request = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=" . $address);
						//dd($request);
						$json = json_decode($request, true);
						$petadas++;
					}
					// Asignamos los primeros valores obtenidos en la respuesta
					$report->latitude = $json['results'][0]['geometry']['location']['lat'];
					$report->longitude = $json['results'][0]['geometry']['location']['lng'];
					$report->geodata = $json['results'][0];
					$report->main_geo = DB::raw("st_geogfromtext('POINT(". $json['results'][0]['geometry']['location']['lng'] ." ". $json['results'][0]['geometry']['location']['lat'] .")')");
				}

				else 
				{
					// Por defecto
					/*
					$report->latitude = 40.416637;
					$report->longitude = -3.703812;
					$report->geodata = json_decode('{"address_components":[{"long_name":"7","short_name":"7","types":["street_number"]},{"long_name":"Plaza Puerta del Sol","short_name":"Plaza Puerta del Sol","types":["route"]},{"long_name":"Madrid","short_name":"Madrid","types":["locality","political"]},{"long_name":"Madrid","short_name":"M","types":["administrative_area_level_2","political"]},{"long_name":"Community of Madrid","short_name":"Community of Madrid","types":["administrative_area_level_1","political"]},{"long_name":"Spain","short_name":"ES","types":["country","political"]},{"long_name":"28013","short_name":"28013","types":["postal_code"]}],"formatted_address":"Plaza Puerta del Sol, 7, 28013 Madrid, Madrid, Spain","geometry":{"location":{"k":40.4165508,"B":-3.7037990000000036},"location_type":"ROOFTOP","viewport":{"Ca":{"k":40.4152018197085,"j":40.4178997802915},"ta":{"j":-3.7051479802914855,"k":-3.7024500197085217}}},"types":["street_address"]}');
					$report->main_geo = DB::raw("st_geogfromtext('POINT(-3.703812 40.416637)')");
					*/
				}

				// Asociamos fecha
				if ( ! empty($elementos['date']) )
				{
					$report->created_at = date('Y-m-d H:i:s', strtotime($elementos['date']));
					$report->updated_at = date('Y-m-d H:i:s', strtotime($elementos['date']));
				}

				// Quitamos la localizacion y la fecha de los elementos
				unset($elementos['main_geo']);
				unset($elementos['latitude']);
				unset($elementos['longitude']);
				unset($elementos['address']);
				unset($elementos['date']);


				// Asociamos relaciones externas
				$report->user()->associate( Auth::user() );
				$report->template()->associate( $template );
				$report->accuracy = 8;

				$report->datum = ReportController::array_to_json($elementos, $template->fields);

				//Comprobar si el datum no esta vacio antes de guardar
				if ( ! empty($report->datum->error) )
				{
					DB::rollback();

					return Redirect::to('report/import')->with('errors', $report->datum->error);
				}

				$imports[] = $report->datum;
				$report->save();
			}
			DB::commit();

			return Redirect::to('report');
		}
	}

	function array_to_json($elementos, $fields)
	{
		$json = [];
		$errors = '';

		$dict_fields = ReportController::dictionary_fields($fields);

		foreach($elementos as $id => $elemento)
		{
			if ( ! empty($dict_fields[$id]) )
			{
				$field = $dict_fields[$id];

				// Comprobamos que no sea un elemento vacio y required
				if ( $field['required'] == 'required' && empty($elemento) )
				{
					$errors .= '<li style="margin-left: 20px">Ha ocurrido un problema al intentar importar la columna (requerida): '. $field['label'] .'</li>';
				}
				else
				{
					// Comprobamos que no sea un elemento vacio
					if ( $elemento !== null && $elemento !== '' )
					{
						if ( $field['type'] == 'checkbox' || $field['type'] == 'radio' || $field['type'] == 'select' )
						{
							if ( ($field['type'] == 'checkbox' ||  $field['type'] == 'select') && strpos($elemento, ',') !== false ) 
							{
								$options = [];
								foreach (explode(',', $elemento) as $valor)
								{
									if ( $field['options'][$valor] !== null && $field['options'][$valor] !== '' )
									{
										$options[] = $field['options'][$valor]['id'];	
									}
									else
									{
										$errors .= '<li style="margin-left: 20px">Ha ocurrido un problema al intentar importar la columna: '. $field['label'] .'->'. $valor .'</li>';
									}							
								}

								$json[$id] = array("id" => $id, "value" => $options);
							}
							else
							{
								if ( $field['options'][$elemento]['id'] !== null && $field['options'][$elemento]['id'] !== '' )
								{
									$json[$id] = array("id" => $id, "value" => $field['options'][$elemento]['id']);
								}
								else
								{
									$errors .= '<li style="margin-left: 20px">Ha ocurrido un problema al intentar importar la columna: '. $field['label'] .'->'. $elemento .'</li>';
								}
							}
						}
						else
						{
							$json[$id] = array("id" => $id, "value" => $elemento);
						}
					}
				}
			}
			else
			{
				$errors .= '<li style="margin-left: 20px">Ha ocurrido un problema al intentar importar la columna: '. $field['label'] .'</li>';
			}
		}

		if (! empty($errors))
		{
			$json['error'] = $errors;
		}

		return $json;
	}

	function dictionary_fields($fields)
	{
		$dict_fields = [];

		foreach($fields as $field)
		{
			$options = [];
			$required = (empty($field->required) ? null : $field->required);

			if ( $field->type == 'checkbox' || $field->type == 'radio' || $field->type == 'select' )
			{
				foreach($field->options as $option)
				{
					$options[$option->value] = array("id" => $option->id);
				}
			}
			
			$dict_fields[$field->id] = array("label" => $field->label,"type" => $field->type, "options" => $options, "required" => $required);			
		}

		return $dict_fields;
	}

	function csv_to_array($csv='', $delimiter=',', $header, $firstline)
	{
		define ('UTF16_BIG_ENDIAN_BOM'   , chr(0x00));
		$data = array();

		if ( Input::get('csv-type') == 'file' )
		{
			if(!file_exists($csv) || !is_readable($csv))
				return FALSE;
		    try
		    {
		    	 if (($handle = ReportController::utf8_fopen_read($csv)) !== false)
			    {
			        while (($row = fgetcsv($handle, 100000, $delimiter, '"')) !== false)
			        {
			        	if (mb_detect_encoding($row[0], 'UTF-8',true) == 'UTF-8' && strpos($row[0], UTF16_BIG_ENDIAN_BOM) === false)
			        	{
			        		//var_dump('Ok: '. $row[0],  mb_detect_encoding($row[0], 'UTF-8',true));
			        	}
			        	else
			        	{
			        		//var_dump('Wrong: '. $row[0],  mb_detect_encoding($row[0], 'UTF-8',true));
			        		$data = array();
			        		return $data;
			        	}

			            if($firstline)
			            {
			                $firstline = null;
			            }
			            else
			            {
			            	// Ignoro líneas en blanco o que no coincidan número de elementos
			            	if ( !is_null($row[0]) && count($header) == count($row))
			            	{
			            		$data[] = array_combine($header, $row);
			            	}
			            }
			        }

			        fclose($handle);
			    }
			}
			catch (Exception $e)
			{
				//var_dump('Wrong: '. $e);
				$data = array();
				return $data;
			}
		}
		else
		{
			foreach ($csv as $row)
			{
				if ($firstline)
					$firstline = null;
				else
					$data[] = array_combine($header, $row);
			}
		}		

	    // Limpio los elementos que no estan asociados en la importacion
	    for ($i = 0; $i < count($data); $i++)
	    {
	    	unset($data[$i]['']);
	    }
	   
	    return $data;
	}

	function utf8_fopen_read($fileName) 
	{ 
	    $fc = iconv(Input::get('encoding'), 'utf-8', file_get_contents($fileName)); 
	    $handle=fopen("php://memory", "rw"); 
	    fwrite($handle, $fc); 
	    fseek($handle, 0); 

	    return $handle; 
	}

	function pdo_to_array($header)
	{
		$pdo = ReportController::get_pdo();
		$dbh = new PDO($pdo['connection'], $pdo['user'], $pdo['password']);
		$stmt = $dbh->query("SELECT * FROM ". Input::get('table'));

		$data = array();

		while( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
		{
			$data[] = array_combine($header, $row);
		}

		// Limpio los elementos que no estan asociados en la importacion
	    for ($i = 0; $i < count($data); $i++)
	    {
	    	unset($data[$i]['']);
	    }

	    return $data;
	}

	public function get_tables()
	{
		$pdo = ReportController::get_pdo();
		$dbh = new PDO($pdo['connection'], $pdo['user'], $pdo['password']);
		$stmt = $dbh->query($pdo['query']);

		
		$tables = [];
		while($row = $stmt->fetch(PDO::FETCH_NUM))
		{
			$tables[] = $row[0];
		}	

		return $tables;
	}

	public function get_columns()
	{
		$pdo = ReportController::get_pdo();
		$dbh = new PDO($pdo['connection'], $pdo['user'], $pdo['password']);
		$stmt = $dbh->query("SELECT * FROM ". Input::get('table'));
		$columnscount = $stmt->columnCount();

		$columns = [];
		$rows = [];

		if ( Input::get('system') == 'SQL Server' ) 
		{
			// Hago el fetch assoc para obtener los nombres de las columnas
			foreach ($rowsql = $stmt->fetch(PDO::FETCH_ASSOC) as $key => $column)
			{
				$columns[] = $key;
			}
		}
		else
		{
			for ($i = 0; $i < $columnscount; $i++)
			{
				// getColumnMeta es EXPERIMENTAL ... tener en cuenta por si peta en un futuro
				$columns[] = $stmt->getColumnMeta($i)['name'];
			}
		}

		// CAMBIAR de 3 a 2, 3 para pruebas
		for ($i = 0; $i < 3; $i++)
		{
			$row = $stmt->fetch(PDO::FETCH_NUM);
			$rows[] = $row;
		}

		return array('columns' => $columns, 'rows' => $rows);
	}

	function get_pdo()
	{
		$system = Input::get('system');
		$host = Input::get('host');
		$port = Input::get('port');
		$database = Input::get('database');
		$user = Input::get('user');
		$password = Input::get('password');

		switch ($system) {
			case 'MySQL':
				$connection = 'mysql:host='. $host .';port='. $port .';dbname='. $database;
				$query = "SHOW tables";
				break;
			case 'PostgreSQL':
				$connection = 'pgsql:host='. $host .';port='. $port .';dbname='. $database;
				$query = "SELECT schemaname || '.' || tablename FROM pg_catalog.pg_tables WHERE schemaname <> 'pg_catalog' and schemaname <> 'information_schema' ORDER BY schemaname, tablename ASC";
				break;
			case 'SQL Server':
				$connection = 'odbc:'. $database;
				$query = "SELECT table_name FROM information_schema.tables";
				break;
			default:
				/*$connection = 'pgsql:host=localhost;port=5432;dbname=coolreport_ecoembes';
				$user = 'postgres';
				$password = 'admin';
				$query = "SELECT schemaname || '.' || tablename FROM pg_catalog.pg_tables WHERE schemaname <> 'pg_catalog' and schemaname <> 'information_schema' ORDER BY schemaname, tablename ASC";
				*/
				$connection = 'error';
				$query = '';
				break;
		}

		return array('connection' => $connection, 'query' => $query, 'user' => $user, 'password' => $password);
	}

	function signs()
	{
		$signs = json_decode(file_get_contents("signs.json"), true);	

		ksort($signs);

		return $this->layout('pages.report.signs')
				->with("signs", $signs);
	}
}

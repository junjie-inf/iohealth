<?php

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

class DashboardHealthController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		// Datos de los select iniciales
		$personas = DB::select(DB::raw(
			"SELECT	reports.id,
				datum->'5889efb0944a90'->>'value' as userid,
				datum->'5889efb0944a91'->>'value' as name,
				datum->'59143940683680'->>'value' as surname
			FROM reports
			INNER JOIN users
			ON reports.datum->'5889efb0944a90'->>'value' = users.id::text
				AND users.deleted_at IS NULL and reports.template_id = 10
				AND reports.deleted_at IS NULL AND users.role_id = 3
			ORDER BY reports.datum->'59143940683680'->>'value';
		"));


		$user = null;
		if( !(Auth::user()->admin) ){
			$user = DB::select(DB::raw("SELECT id FROM reports WHERE template_id = 10 AND (datum->'5889efb0944a90'->>'value')::integer = ".Auth::user()->id." AND deleted_at IS NULL LIMIT 1"))[0]->id;
		}

		//Consulta enfermedades asociadas
		 $q =  " SELECT e.*, e.datum->'5889e63a4646c0'->>'value' as nombre  "
                ."  FROM reports p, reports e , reports persona "
                ."  WHERE persona.template_id= 10 "
                ."          AND p.template_id = 11   "
                ."          AND e.template_id = 2  "
                ."          AND (persona.datum->'5889efb0944a90'->>'value')::int = ". Auth::user()->id
                ."          AND (p.datum->'5889f0a9092ea1'->>'value')::int = persona.id "
                ."          AND (p.datum->'5922aef15ccad0'->>'value')::int = e.id   "
                ."          AND persona.deleted_at IS NULL "
                ."          AND e.deleted_at IS NULL "
                ."          AND p.deleted_at IS NULL ";

        $enfermedadesAsociadas = DB::select(DB::raw($q));


		return View::make('dashitems.dashboardHealth')
		 		->with('enfermedades', $enfermedadesAsociadas)
				->with('persons', $personas)
				->with('user', $user)
				;
	}

	public function getDiseases(){
		if (Input::has('user')){
			$user = Input::get('user');


			$user = DB::select(DB::raw("select datum->'5889efb0944a90'->>'value' as id from reports where id =".$user))[0]->id;


			//Consulta enfermedades asociadas
			 $q =  " SELECT e.*, e.datum->'5889e63a4646c0'->>'value' as nombre  "
	                ."  FROM reports p, reports e , reports persona "
	                ."  WHERE persona.template_id= 10 "
	                ."          AND p.template_id = 11   "
	                ."          AND e.template_id = 2  "
	                ."          AND (persona.datum->'5889efb0944a90'->>'value')::int = ".$user
	                ."          AND (p.datum->'5889f0a9092ea1'->>'value')::int = persona.id "
	                ."          AND (p.datum->'5922aef15ccad0'->>'value')::int = e.id   "
	                ."          AND persona.deleted_at IS NULL "
	                ."          AND e.deleted_at IS NULL "
	                ."          AND p.deleted_at IS NULL ";

	        $enfermedadesAsociadas = DB::select(DB::raw($q));

			return array('enfermedades' => $enfermedadesAsociadas );
		}
	}

	public function persons()
	{
		if (Input::has('search') && !empty(Input::get('search')))
		{
			$search = Input::get('search');
			$cnts = DB::select(DB::raw("SELECT id, datum->'5889efb0944a91'->>'value' as name FROM reports WHERE template_id = 10 AND datum->'5889efb0944a91'->>'value'::text like '". $search ."%' AND deleted_at IS NULL ORDER BY name LIMIT 100"));

		}else{
			$cnts = DB::select(DB::raw("SELECT id, datum->'5889efb0944a91'->>'value' as name FROM reports WHERE template_id = 10 AND deleted_at IS NULL ORDER BY name LIMIT 100"));
		}

		$data = array('data' => $cnts, 'count' => Input::get('count'));

		return $data;
	}

	public function getPersonData()
	{
		$person = Input::get('person');
		$start = Input::get("startDate");
		$end = Input::get("endDate");
		$enfermedad = Input::get("enfermedad");


		$glucosa = null;
		$weight = null;
		$bp = null;
		$spo2 = null;
		$frecResp  = null;
		$temp = null;

		//Recupera parametros de acuerdo a la enfermedad
		switch  ($enfermedad ){
        	case 637:   //Hiperpresión arterial
	           	$spo2 = DB::select(DB::raw("SELECT 	datum->'58e65e65d6d6a1'->>'value' as person,
											datum->'58e65e65d6d6a2'->>'value' as fecha,
											round((datum->'58e65e65d6d6a4'->>'value')::numeric, 2) as spo2
											FROM reports WHERE template_id = 37	AND deleted_at IS NULL
											AND (datum->'58e65e65d6d6a1'->>'value')::integer = ".$person."
											AND (datum->'58e65e65d6d6a2'->>'value')::timestamptz <= '".$end."'
											AND (datum->'58e65e65d6d6a2'->>'value')::timestamptz >= '".$start."'
											ORDER BY fecha"));

				$bp = DB::select(DB::raw("SELECT 	datum->'589056a213b6f1'->>'value' as person,
													datum->'589056a213b6f2'->>'value' as fecha,
													datum->'589056a213b6f3'->>'value' as sistolic,
													datum->'589056a213b6f4'->>'value' as diastolic,
													datum->'589056a213b6f5'->>'value' as pulse
													FROM reports WHERE template_id = 28	AND deleted_at IS NULL
													AND (datum->'589056a213b6f1'->>'value')::integer = ".$person."
													AND (datum->'589056a213b6f2'->>'value')::timestamptz <= '".$end."'
													AND (datum->'589056a213b6f2'->>'value')::timestamptz >= '".$start."'
													ORDER BY fecha"));

				$weight = DB::select(DB::raw("SELECT 	datum->'58905621bbd841'->>'value' as person,
														datum->'58905621bbd842'->>'value' as fecha,
														round((datum->'58905621bbd843'->>'value')::numeric, 2) as peso,
														round((datum->'58905621bbd844'->>'value')::numeric, 2) as imc
														FROM reports WHERE template_id = 27	AND deleted_at IS NULL
														AND (datum->'58905621bbd841'->>'value')::integer = ".$person."
														AND (datum->'58905621bbd842'->>'value')::timestamptz <= '".$end."'
														AND (datum->'58905621bbd842'->>'value')::timestamptz >= '".$start."'
														ORDER BY fecha"));

				$frecResp = DB::select(DB::raw("SELECT 	datum->'5a2934ce421820'->>'value' as person ,
														datum->'5a26d95b2f1ba1'->>'value' as fecha,
														round((datum->'5a26d95b2f1ba0'->>'value')::numeric, 2) as resppm
														FROM reports WHERE template_id = 48	AND deleted_at IS NULL
														AND (datum->'5a2934ce421820'->>'value')::integer = ".$person."
														AND (datum->'5a26d95b2f1ba1'->>'value')::timestamptz <= '".$end."'
														AND (datum->'5a26d95b2f1ba1'->>'value')::timestamptz >= '".$start."'
														ORDER BY fecha"));
        		break;
        	case 635: //Diabetes
        	case 636:
	            $glucosa =  DB::select(DB::raw("SELECT 	datum->'5890542e1d0321'->>'value' as person,
												datum->'5890542e1d0322'->>'value' as fecha,
												round((datum->'5890542e1d0323'->>'value')::numeric, 2) as glucosa
												FROM reports WHERE template_id = 25	AND deleted_at IS NULL
												AND (datum->'5890542e1d0321'->>'value')::integer = ".$person."
												AND (datum->'5890542e1d0322'->>'value')::timestamptz <= '".$end."'
												AND (datum->'5890542e1d0322'->>'value')::timestamptz >= '".$start."'
												ORDER BY fecha"));
	            //$glucosa =  empty($glucosa) ? null : $glucosa[0]->glucosa;
	           	$weight = DB::select(DB::raw("SELECT 	datum->'58905621bbd841'->>'value' as person,
												datum->'58905621bbd842'->>'value' as fecha,
												round((datum->'58905621bbd843'->>'value')::numeric, 2) as peso,
												round((datum->'58905621bbd844'->>'value')::numeric, 2) as imc
												FROM reports WHERE template_id = 27	AND deleted_at IS NULL
												AND (datum->'58905621bbd841'->>'value')::integer = ".$person."
												AND (datum->'58905621bbd842'->>'value')::timestamptz <= '".$end."'
												AND (datum->'58905621bbd842'->>'value')::timestamptz >= '".$start."'
												ORDER BY fecha"));
	           	$bp = DB::select(DB::raw("SELECT 	datum->'589056a213b6f1'->>'value' as person,
											datum->'589056a213b6f2'->>'value' as fecha,
											datum->'589056a213b6f3'->>'value' as sistolic,
											datum->'589056a213b6f4'->>'value' as diastolic,
											datum->'589056a213b6f5'->>'value' as pulse
											FROM reports WHERE template_id = 28	AND deleted_at IS NULL
											AND (datum->'589056a213b6f1'->>'value')::integer = ".$person."
											AND (datum->'589056a213b6f2'->>'value')::timestamptz <= '".$end."'
											AND (datum->'589056a213b6f2'->>'value')::timestamptz >= '".$start."'
											ORDER BY fecha"));
	           	$spo2 = DB::select(DB::raw("SELECT 	datum->'58e65e65d6d6a1'->>'value' as person,
											datum->'58e65e65d6d6a2'->>'value' as fecha,
											round((datum->'58e65e65d6d6a4'->>'value')::numeric, 2) as spo2
											FROM reports WHERE template_id = 37	AND deleted_at IS NULL
											AND (datum->'58e65e65d6d6a1'->>'value')::integer = ".$person."
											AND (datum->'58e65e65d6d6a2'->>'value')::timestamptz <= '".$end."'
											AND (datum->'58e65e65d6d6a2'->>'value')::timestamptz >= '".$start."'
											ORDER BY fecha"));
        		break;
        	case 630: //Hepatitis B
        	case 631:
        	case 632:
        	case 633:
        	case 634:
	            $weight = DB::select(DB::raw("SELECT 	datum->'58905621bbd841'->>'value' as person,
												datum->'58905621bbd842'->>'value' as fecha,
												round((datum->'58905621bbd843'->>'value')::numeric, 2) as peso,
												round((datum->'58905621bbd844'->>'value')::numeric, 2) as imc
												FROM reports WHERE template_id = 27	AND deleted_at IS NULL
												AND (datum->'58905621bbd841'->>'value')::integer = ".$person."
												AND (datum->'58905621bbd842'->>'value')::timestamptz <= '".$end."'
												AND (datum->'58905621bbd842'->>'value')::timestamptz >= '".$start."'
												ORDER BY fecha"));
	           	$bp = DB::select(DB::raw("SELECT 	datum->'589056a213b6f1'->>'value' as person,
											datum->'589056a213b6f2'->>'value' as fecha,
											datum->'589056a213b6f3'->>'value' as sistolic,
											datum->'589056a213b6f4'->>'value' as diastolic,
											datum->'589056a213b6f5'->>'value' as pulse
											FROM reports WHERE template_id = 28	AND deleted_at IS NULL
											AND (datum->'589056a213b6f1'->>'value')::integer = ".$person."
											AND (datum->'589056a213b6f2'->>'value')::timestamptz <= '".$end."'
											AND (datum->'589056a213b6f2'->>'value')::timestamptz >= '".$start."'
											ORDER BY fecha"));
	           	$spo2 = DB::select(DB::raw("SELECT 	datum->'58e65e65d6d6a1'->>'value' as person,
											datum->'58e65e65d6d6a2'->>'value' as fecha,
											round((datum->'58e65e65d6d6a4'->>'value')::numeric, 2) as spo2
											FROM reports WHERE template_id = 37	AND deleted_at IS NULL
											AND (datum->'58e65e65d6d6a1'->>'value')::integer = ".$person."
											AND (datum->'58e65e65d6d6a2'->>'value')::timestamptz <= '".$end."'
											AND (datum->'58e65e65d6d6a2'->>'value')::timestamptz >= '".$start."'
											ORDER BY fecha"));
	           	$frecResp = DB::select(DB::raw("SELECT 	datum->'5a2934ce421820'->>'value' as person ,
														datum->'5a26d95b2f1ba1'->>'value' as fecha,
														round((datum->'5a26d95b2f1ba0'->>'value')::numeric, 2) as resppm
														FROM reports WHERE template_id = 48	AND deleted_at IS NULL
														AND (datum->'5a2934ce421820'->>'value')::integer = ".$person."
														AND (datum->'5a26d95b2f1ba1'->>'value')::timestamptz <= '".$end."'
														AND (datum->'5a26d95b2f1ba1'->>'value')::timestamptz >= '".$start."'
														ORDER BY fecha"));
	           	$temp = DB::select(DB::raw("SELECT 	datum->'5a2934617b17c0'->>'value' as person,
														datum->'5a26dd2e84c3a2'->>'value' as fecha,
														round((datum->'5a26dd2e84c3a1'->>'value')::numeric, 2) as temp
														FROM reports WHERE template_id = 49	AND deleted_at IS NULL
														AND (datum->'5a2934617b17c0'->>'value')::integer = ".$person."
														AND (datum->'5a26dd2e84c3a2'->>'value')::timestamptz <= '".$end."'
														AND (datum->'5a26dd2e84c3a2'->>'value')::timestamptz >= '".$start."'
														ORDER BY fecha"));
	            break;
        } // end switch

		/*
		$spo2 = DB::select(DB::raw("SELECT 	datum->'58e65e65d6d6a1'->>'value' as person,
											datum->'58e65e65d6d6a2'->>'value' as fecha,
											round((datum->'58e65e65d6d6a4'->>'value')::numeric, 2) as spo2
											FROM reports WHERE template_id = 37	AND deleted_at IS NULL
											AND (datum->'58e65e65d6d6a1'->>'value')::integer = ".$person."
											AND (datum->'58e65e65d6d6a2'->>'value')::timestamptz <= '".$end."'
											AND (datum->'58e65e65d6d6a2'->>'value')::timestamptz >= '".$start."'
											ORDER BY fecha"));

		$bp = DB::select(DB::raw("SELECT 	datum->'589056a213b6f1'->>'value' as person,
											datum->'589056a213b6f2'->>'value' as fecha,
											datum->'589056a213b6f3'->>'value' as sistolic,
											datum->'589056a213b6f4'->>'value' as diastolic,
											datum->'589056a213b6f5'->>'value' as pulse
											FROM reports WHERE template_id = 28	AND deleted_at IS NULL
											AND (datum->'589056a213b6f1'->>'value')::integer = ".$person."
											AND (datum->'589056a213b6f2'->>'value')::timestamptz <= '".$end."'
											AND (datum->'589056a213b6f2'->>'value')::timestamptz >= '".$start."'
											ORDER BY fecha"));

		$weight = DB::select(DB::raw("SELECT 	datum->'58905621bbd841'->>'value' as person,
												datum->'58905621bbd842'->>'value' as fecha,
												round((datum->'58905621bbd843'->>'value')::numeric, 2) as peso,
												round((datum->'58905621bbd844'->>'value')::numeric, 2) as imc
												FROM reports WHERE template_id = 27	AND deleted_at IS NULL
												AND (datum->'58905621bbd841'->>'value')::integer = ".$person."
												AND (datum->'58905621bbd842'->>'value')::timestamptz <= '".$end."'
												AND (datum->'58905621bbd842'->>'value')::timestamptz >= '".$start."'
												ORDER BY fecha"));
		*/


		return array('spo2' => $spo2, 'bp' => $bp, 'weight' => $weight, 'glucosa' => $glucosa, 'frecResp' => $frecResp, 'temp' => $temp );
	} // end getPersonData ()

	public function getPersonDoctorReco()
	{
		$person = intval( Input::get('person') );
		$m = null;

		$mensajes = DB::select(DB::raw("SELECT id, user_id, datum, created_at
													FROM reports
													WHERE datum->'5a57bd971a3c60'->>'value' = '".$person."' and
															template_id = 51 and deleted_at is null
													ORDER BY created_at desc limit 5"
												));
		//creación de array
		$all_pila = array();

		foreach ($mensajes as $m)
		{
			$id = $m->id;
			$datum = json_decode($m->datum);
			//Creador
			$remitente = $m->user_id;
			//select firstname, surname from users where id = 1
			$remitente = DB::select(DB::raw("select firstname, surname from users where id = ".$remitente))[0];
			$remitente = $remitente->surname.", ".$remitente->firstname;

			//date_parse("2006-12-12 10:00:00.5")
			$date = date_parse($m->created_at);

			$content = $datum->{"5a57bd971a3c62"}->value;
			$creacion = $date["day"]."/".$date["month"]."/".$date["year"];

			//peque array()
			$temp_array = array($id, $remitente, $creacion, $content);
			array_push($all_pila, $temp_array);
		}

		return array( 'persona_id' => $person, 'mensajes' => $all_pila );
	}


	public function getPersonAlerts()
	{
		$person = Input::get('person');
		$start = Input::get("startDate");
		$end = Input::get("endDate");

		$alerts = DB::select(DB::raw("SELECT datum->'58fdc316d638a0'->>'value' as date,
											datum->'58fdc316d638a1'->>'value' as person,
											datum->'58fdc316d638a2'->>'value' as title,
											datum->'58fdc316d638a3'->>'value' as description
											FROM reports WHERE template_id = 38	AND deleted_at IS NULL
											AND datum->'58fdc316d638a1'->>'value' = ".$person."::text
											AND (datum->'58fdc316d638a0'->>'value')::timestamptz <= '".$end."'
											AND (datum->'58fdc316d638a0'->>'value')::timestamptz >= '".$start."'"));

		return (array)$alerts;
	}

	public function getPersonInfo()
	{
		$glucosa = null;
		$weight = null;
		$bp = null;
		$spo2 = null;
		$diastolic = null;
		$sistolic = null;
		$imc = null;

		$person = Input::get('person');
		$enfermedad = Input::get("enfermedad");

		$info = DB::select(DB::raw("SELECT 	datum->'5889efb0944a91'->>'value' as name,
											datum->'59143940683680'->>'value' as surname,
											CASE datum->'5889efb0944a92'->>'value' WHEN '5889efb0944a93' THEN 'Hombre' ELSE 'Mujer' END as gender,
											datum->'5889efb0944a95'->>'value' as height,
											datum->'5889efb0944a96'->>'value' as birth,
											CASE WHEN datum->'5889efb0944a96'->>'value' LIKE '%/%/%'
												THEN EXTRACT(YEAR FROM AGE(to_timestamp(datum->'5889efb0944a96'->>'value', 'DD/MM/YYYY')))
												ELSE EXTRACT(YEAR FROM AGE(to_timestamp(datum->'5889efb0944a96'->>'value', 'YYYY-MM-DD')))
											END as age
											FROM reports WHERE template_id = 10	AND deleted_at IS NULL
											AND id::integer = ".$person))[0];

		$paciente = DB::select(DB::raw("SELECT 	id,
											datum->'5889f0a9092ea1'->>'value' as person,
											datum->'5889f0a9092ea2'->>'value' as inicio,
											datum->'5922aef15ccad0'->>'value' as enfermedad
											FROM reports WHERE template_id = 11	AND deleted_at IS NULL
											AND (datum->'5889f0a9092ea1'->>'value')::integer = ".$person));
		

		$linked = [];

		foreach($paciente as $p){
			$p->enfermedad = Report::find($p->enfermedad);
		}

		//:Recupera parametros de acuerdo a la enfermedad
		switch  ($enfermedad ){
        	case 637:   //Hiperpresión arterial
	           	$spo2 = DB::select(DB::raw("SELECT 	datum->'58e65e65d6d6a1'->>'value' as person,
											datum->'58e65e65d6d6a2'->>'value' as fecha,
											round((datum->'58e65e65d6d6a4'->>'value')::numeric, 2) as spo2
											FROM reports WHERE template_id = 37	AND deleted_at IS NULL
											AND (datum->'58e65e65d6d6a1'->>'value')::integer = ".$person."
											ORDER BY fecha DESC LIMIT 1"));
	           	$spo2 = empty($spo2) ? null : $spo2[0]->spo2;

				$bp = DB::select(DB::raw("SELECT 	datum->'589056a213b6f1'->>'value' as person,
											datum->'589056a213b6f2'->>'value' as fecha,
											datum->'589056a213b6f3'->>'value' as sistolic,
											datum->'589056a213b6f4'->>'value' as diastolic,
											datum->'589056a213b6f5'->>'value' as pulse
											FROM reports WHERE template_id = 28	AND deleted_at IS NULL
											AND (datum->'589056a213b6f1'->>'value')::integer = ".$person."
											ORDER BY fecha DESC LIMIT 1"));
				$diastolic = empty($bp) ? null : $bp[0]->diastolic;
				$sistolic = empty($bp) ? null : $bp[0]->sistolic;

				$weight = DB::select(DB::raw("SELECT 	datum->'58905621bbd841'->>'value' as person,
												datum->'58905621bbd842'->>'value' as fecha,
												round((datum->'58905621bbd843'->>'value')::numeric, 2) as peso,
												round((datum->'58905621bbd844'->>'value')::numeric, 2) as imc
												FROM reports WHERE template_id = 27	AND deleted_at IS NULL
												AND (datum->'58905621bbd841'->>'value')::integer = ".$person."
												ORDER BY fecha DESC LIMIT 1"));
				$imc = empty($weight) ? null : $weight[0]->imc;

				$frecResp = DB::select(DB::raw("SELECT 	datum->'5a2934ce421820'->>'value' as person ,
														datum->'5a26d95b2f1ba1'->>'value' as fecha,
														round((datum->'5a26d95b2f1ba0'->>'value')::numeric, 2) as frecResp
														FROM reports WHERE template_id = 48	AND deleted_at IS NULL
														AND (datum->'5a2934ce421820'->>'value')::integer = ".$person."
														ORDER BY fecha DESC LIMIT 1"));
        		break;
        	case 635: //Diabetes
        	case 636:
	            $glucosa =  DB::select(DB::raw("SELECT 	datum->'5890542e1d0321'->>'value' as person,
												datum->'5890542e1d0322'->>'value' as fecha,
												round((datum->'5890542e1d0323'->>'value')::numeric, 2) as glucosa
												FROM reports WHERE template_id = 25	AND deleted_at IS NULL
												AND (datum->'5890542e1d0321'->>'value')::integer = ".$person."
												ORDER BY fecha DESC LIMIT 1"));
	            $glucosa =  empty($glucosa) ? "" : $glucosa[0]->glucosa;

	           	$weight = DB::select(DB::raw("SELECT 	datum->'58905621bbd841'->>'value' as person,
												datum->'58905621bbd842'->>'value' as fecha,
												round((datum->'58905621bbd843'->>'value')::numeric, 2) as peso,
												round((datum->'58905621bbd844'->>'value')::numeric, 2) as imc
												FROM reports WHERE template_id = 27	AND deleted_at IS NULL
												AND (datum->'58905621bbd841'->>'value')::integer = ".$person."
												ORDER BY fecha DESC LIMIT 1"));
				$imc = empty($weight) ? null : $weight[0]->imc;
	           	$bp = DB::select(DB::raw("SELECT 	datum->'589056a213b6f1'->>'value' as person,
											datum->'589056a213b6f2'->>'value' as fecha,
											datum->'589056a213b6f3'->>'value' as sistolic,
											datum->'589056a213b6f4'->>'value' as diastolic,
											datum->'589056a213b6f5'->>'value' as pulse
											FROM reports WHERE template_id = 28	AND deleted_at IS NULL
											AND (datum->'589056a213b6f1'->>'value')::integer = ".$person."
											ORDER BY fecha DESC LIMIT 1"));
				$diastolic = empty($bp) ? null : $bp[0]->diastolic;
				$sistolic = empty($bp) ? null : $bp[0]->sistolic;
				$spo2 = DB::select(DB::raw("SELECT 	datum->'58e65e65d6d6a1'->>'value' as person,
											datum->'58e65e65d6d6a2'->>'value' as fecha,
											round((datum->'58e65e65d6d6a4'->>'value')::numeric, 2) as spo2
											FROM reports WHERE template_id = 37	AND deleted_at IS NULL
											AND (datum->'58e65e65d6d6a1'->>'value')::integer = ".$person."
											ORDER BY fecha DESC LIMIT 1"));
	           	$spo2 = empty($spo2) ? null : $spo2[0]->spo2;
        		break;
        	case 630: //Hepatitis B
        	case 631:
        	case 632:
        	case 633:
        	case 634:
	            $weight = DB::select(DB::raw("SELECT 	datum->'58905621bbd841'->>'value' as person,
												datum->'58905621bbd842'->>'value' as fecha,
												round((datum->'58905621bbd843'->>'value')::numeric, 2) as peso,
												round((datum->'58905621bbd844'->>'value')::numeric, 2) as imc
												FROM reports WHERE template_id = 27	AND deleted_at IS NULL
												AND (datum->'58905621bbd841'->>'value')::integer = ".$person."
												ORDER BY fecha DESC LIMIT 1"));

				$imc = empty($weight) ? null : $weight[0]->imc;


	           	$bp = DB::select(DB::raw("SELECT 	datum->'589056a213b6f1'->>'value' as person,
											datum->'589056a213b6f2'->>'value' as fecha,
											datum->'589056a213b6f3'->>'value' as sistolic,
											datum->'589056a213b6f4'->>'value' as diastolic,
											datum->'589056a213b6f5'->>'value' as pulse
											FROM reports WHERE template_id = 28	AND deleted_at IS NULL
											AND (datum->'589056a213b6f1'->>'value')::integer = ".$person."
											ORDER BY fecha DESC LIMIT 1"));
				$diastolic = empty($bp) ? null : $bp[0]->diastolic;
				$sistolic = empty($bp) ? null : $bp[0]->sistolic;

				$spo2 = DB::select(DB::raw("SELECT 	datum->'58e65e65d6d6a1'->>'value' as person,
											datum->'58e65e65d6d6a2'->>'value' as fecha,
											round((datum->'58e65e65d6d6a4'->>'value')::numeric, 2) as spo2
											FROM reports WHERE template_id = 37	AND deleted_at IS NULL
											AND (datum->'58e65e65d6d6a1'->>'value')::integer = ".$person."
											ORDER BY fecha DESC LIMIT 1"));
				$frecResp = DB::select(DB::raw("SELECT 	datum->'5a2934ce421820'->>'value' as person,
														datum->'5a26d95b2f1ba1'->>'value' as fecha,
														round((datum->'5a26d95b2f1ba0'->>'value')::numeric, 2) as frecResp
														FROM reports WHERE template_id = 48	AND deleted_at IS NULL
														AND (datum->'5a2934ce421820'->>'value')::integer = ".$person."
														ORDER BY fecha DESC LIMIT 1"));

				$temp = DB::select(DB::raw("SELECT 	datum->'5a2934617b17c0'->>'value' as person,
														datum->'5a26dd2e84c3a2'->>'value' as fecha,
														round((datum->'5a26dd2e84c3a1'->>'value')::numeric, 2) as temp
														FROM reports WHERE template_id = 49	AND deleted_at IS NULL
														AND (datum->'5a2934617b17c0'->>'value')::integer = ".$person."
														ORDER BY fecha  DESC LIMIT 1"));

	            break;
        } // end switch
		/*
		$spo2 = DB::select(DB::raw("SELECT 	datum->'58e65e65d6d6a1'->>'value' as person,
											datum->'58e65e65d6d6a2'->>'value' as fecha,
											round((datum->'58e65e65d6d6a4'->>'value')::numeric, 2) as spo2
											FROM reports WHERE template_id = 37	AND deleted_at IS NULL
											AND (datum->'58e65e65d6d6a1'->>'value')::integer = ".$person."
											ORDER BY fecha DESC LIMIT 1"));
		$spo2 = empty($spo2) ? null : $spo2[0]->spo2;

		$bp = DB::select(DB::raw("SELECT 	datum->'589056a213b6f1'->>'value' as person,
											datum->'589056a213b6f2'->>'value' as fecha,
											datum->'589056a213b6f3'->>'value' as sistolic,
											datum->'589056a213b6f4'->>'value' as diastolic,
											datum->'589056a213b6f5'->>'value' as pulse
											FROM reports WHERE template_id = 28	AND deleted_at IS NULL
											AND (datum->'589056a213b6f1'->>'value')::integer = ".$person."
											ORDER BY fecha DESC LIMIT 1"));
		$diastolic = empty($bp) ? null : $bp[0]->diastolic;
		$sistolic = empty($bp) ? null : $bp[0]->sistolic;

		$weight = DB::select(DB::raw("SELECT 	datum->'58905621bbd841'->>'value' as person,
												datum->'58905621bbd842'->>'value' as fecha,
												round((datum->'58905621bbd843'->>'value')::numeric, 2) as peso,
												round((datum->'58905621bbd844'->>'value')::numeric, 2) as imc
												FROM reports WHERE template_id = 27	AND deleted_at IS NULL
												AND (datum->'58905621bbd841'->>'value')::integer = ".$person."
												ORDER BY fecha DESC LIMIT 1"));
		$imc = empty($weight) ? null : $weight[0]->imc;
		*/
		$recomendaciones = null;
		foreach ($paciente as $p) {
			switch ($p->enfermedad->datum->{'5889e63a4646c0'}->value) {
				case 'Diabetes':
					$recomendaciones['Diabetes'] = $this->recomendacionDiabetico($spo2, $diastolic, $sistolic, $imc);
					break;
				case 'Hipertensión Arterial':
					$recomendaciones['Hipertensión Arterial'] = $this->recomendacionHipertensión($info->age, $info->gender, $diastolic, $sistolic);
					break;

				default:
					break;
			}
		}

		return array( 'info' => (array)$info, 'spo2' => $spo2, 'diastolic' => $diastolic, 'sistolic' => $sistolic, 'imc' => $imc,  'glucosa' => $glucosa, 'paciente' => $paciente, 'recomendaciones' => $recomendaciones );
	}

	private function recomendacionDiabetico($spo2, $diastolic, $sistolic, $imc){

		$recomendaciones = array(
			'tension' => [	'La tension es mas baja que la recomendada',
							'La tension esta en los niveles recomendados',
							'La tension es mas alta que la recomendada'],
			'spo2'=> [	'El oxigeno en sangre es demasiado bajo',
						'El oxigeno en sangre es muy bajo',
						'El oxigeno en sangre esta en niveles normales'],
			'imc'=> [	'El peso esta por debajo del recomendado',
						'El peso esta en valores recomendados',
						'El peso esta por encima del recomendado',
						'El peso esta demasiado alto, puede afectar a su salud. Se le recomienda perder peso']
		);

		$warning = [];
		$critical = [];

		$t = 0;
		$s = 0;

		if ($spo2 <= 80 ) {
			$warning[] = $recomendaciones['spo2'][0];
		}else if  ($spo2 <= 90 ){
			$warning[] = $recomendaciones['spo2'][1];
			$s = 1;
		}else{
			$warning[] = $recomendaciones['spo2'][2];
			$s = 2;
		}

		if ($sistolic <= 90 || $diastolic <= 60 ) {
			$warning[] = $recomendaciones['tension'][0];
		}else if ($sistolic >= 140 || $diastolic >= 90  ){
			$warning[] = $recomendaciones['tension'][2];
			$t = 2;
		}else{
			$warning[] = $recomendaciones['tension'][1];
			$t = 1;
		}

		if ($imc < 18.5 ) {
			$warning[] = $recomendaciones['imc'][0];
		}else if ($imc <= 25 ){
			$warning[] = $recomendaciones['imc'][1];
		}else if ($imc <= 30 ){
			$warning[] = $recomendaciones['imc'][2];
		}else{
			$warning[] = $recomendaciones['imc'][3];
		}

		if( $t == 2 && $s == 1){
			$critical[] = 'Es posible que tenga el azucar en sangre alto. Se le recomienda tomar la dosis de insulina';
		}else if( $t == 2 && $s == 0){
			$critical[] = 'Es posible que tenga una crisis hipoglucémica. Llama al hospital';
		}

		return array( 'warning' => $warning,
					'critical' => $critical );
	}

	private function recomendacionHipertensión($age, $gender, $diastolic, $sistolic){

		$recomendaciones = array(
			'tension' => [	'La tension es mas baja que la recomendada',
							'La tension esta en los niveles recomendados',
							'La tension es mas alta que la recomendada. Se le recomienda hacer ejercicio']
		);

		$limits = array(
			'ages' => [[16,18],[19,24],[25,29],[30,39],[40,49],[50,59],[60, 10000]],
			'Hombre' =>array(
				'sistolic' => [[105,135],[105,139],[108,139],[110,145],[110,150],[115,155],[115,160]],
				'diastolic' => [[60,86],[62,88],[65,89],[68,92],[70,96],[70,98],[70,100]]
			),
			'Mujer' =>array(
				'sistolic' => [[100,130],[100,130],[102,135],[105,139],[105,150],[110,155],[115,160]],
				'diastolic' => [[60,85],[60,85],[60,86],[65,89],[65,96],[70,98],[70,100]]
			)
		);

		$key;
		$warning = [];
		$critical = [];

		foreach ($limits['ages'] as $key => $range) {
			if ($age <= $range[1])
				break;
		}

		if ($sistolic <= $limits[$gender]['sistolic'][$key][0] ) {
			$sis = 0;
		}else if ($sistolic >= $limits[$gender]['sistolic'][$key][1] ){
			$sis = 2;
		}else{
			$sis = 1;
		}

		if ($diastolic <= $limits[$gender]['diastolic'][$key][0] ) {
			$d = 0;
		}else if ($diastolic >= $limits[$gender]['diastolic'][$key][1]  ){
			$d = 2;
		}else{
			$d = 1;
		}

		if( $d >= 2 || $sis >= 2 ){
			$critical[] = 'La tension es mas alta que la recomendada. Se le recomienda hacer ejercicio';
		}else if( $d <= 0 || $sis <= 0 ){
			$critical[] = 'La tension es mas baja que la recomendada';
		}else{
			$warning[] = 'La tension esta en los niveles recomendados';
		}

		return array( 'warning' => $warning,
					'critical' => $critical );
	}


	/**
	 * importCDA
	 *
	 * Performs an importation of the CDA file
	 *
	 * @todo Chen Get the patient ID
	 *
	 * @author José Antonio García Díaz <joseantonio.garcia8@um.es>
	 * @see >https://github.com/Smolky/premytecd>
	 */
	public function importCDA () {

		/** @var $patient_id int The ID of the patient */
		$patient_id = isset ($_POST['id-patient']) ? $_POST['id-patient'] : '';
		if ( ! $patient_id) {
			$patient_id = DB::select(DB::raw ("SELECT id from reports where template_id = 10 and user_id = ".Auth::user()->id))[0]->id;
		}


		/** @var $user_id int */
		$user_id = DB::select(DB::raw ("SELECT user_id from reports where template_id = 10 and id = ". $patient_id))[0]->user_id;


		/** @var $created_at String */
		$created_at = date ('Y-m-d H:i:s', time ());


		/** @var response Array */
        $response = [];


        /** @var $historial String The base64 HL7 CDA file */
        $historial = Input::get('file');
        if ( ! $historial) {
			// @todo Handle error
        	return;
        }


		// Get the strinng and decode it
		$historial = explode (',', $historial);
		$historial = end ($historial);
		$historial = utf8_encode (base64_decode ($historial));


		// @note. Seems that this version of DOMCrawler of CSSSelector does not
		// work properly, but i do not know the reaseon. Converting to
		// lowercase seems to work
		// what I tried:
		// - convert to a DOMDocument object to lowercase the letters only
		// - load from string
		// - check encoding
		// - remove accent letters
		//
		// The problem seems to be that Laravel framework requires
		// an older version of laravel framework 2.7
		//
		// Documentation shows how to fix
		// @link https://symfony.com/doc/2.7/components/dom_crawler.html

		$crawler = new Crawler ();
		$crawler->registerNamespace ('cda', 'urn:hl7-org:v3');
		$crawler->addXmlContent ($historial);
		CssSelector::disableHtmlExtension();


		// Check validity
        if ('85353-1' != $crawler->filter ('cda|code')->first ()->attr ('code')) {
            // @todo. Return error according to Laravel format
            return;
        }


		// Identifying client
        $patient = $crawler->filter ('cda|recordTarget cda|patientRole')->first ();
        $patient->registerNamespace ('cda', 'urn:hl7-org:v3');
        $patient_dom_id = $patient->filter ('cda|id');
        $response['id_paciente'] = $patient_dom_id->attr ('extension');



        // Identifying measures
        $crawler->filter ('cda|observation')->each (function (Crawler $measure, $i) use (& $response) {

        	// Register old way
			$measure->registerNamespace ('cda', 'urn:hl7-org:v3');


			// Get values
            $measure_object = $measure->filter ('cda|code')->first ();
            $measure_value = $measure->filter ('cda|value')->first ();
            $measure_time = $measure->filter ('cda|effectiveTime')->first ();


			// Register old way
			$measure_object->registerNamespace ('cda', 'urn:hl7-org:v3');
			$measure_value->registerNamespace ('cda', 'urn:hl7-org:v3');
			$measure_time->registerNamespace ('cda', 'urn:hl7-org:v3');


            /** @var $measure_id String ID of the measurement */
            $measure_id = $measure_object->attr ('code');


            /** @var $measure_name String Name of the measurement*/
            $measure_name = $measure_object->attr ('displayName');


            /** @var $value String Value of the measurement */
            $value = $measure_value->attr ('value');


            /** @var $time String Moment of the measurement */
            $time = $measure_time->attr ('value');



			/** @var $key String form_key */
			$key = '';
            $attr = '';

            /** @var $key String form_key */
            $state = '';


            /** @var $template_id int The template of the report */
            $template_id = 0;

            switch ($measure_id) {

                case '2.16.840.1.113883.6.96':
                	$template_id = 25;
                    $key = 'glucosa';
                    break;

                case '60621009':
                case '301333006':
                case '165109007':
                	$template_id = 27;
                    $key = 'peso_corporal';
                    break;



                case '271649006':
                case '271650006':
                	$template_id = 28;
                    $key = 'presion_sanguinea';
                    break;

                case '104847001':
                case '366199006':
                	$template_id = 37;
                    $key = 'pulso_oxigeno';
                    break;

                case '86290005':
                	$template_id = 48;
                    $key = 'frecuencia_respiratoria';
                    break;

                case '386725007':
                	$template_id = 49;
                    $key = 'temperatura';
                    break;
        	}


        	// Get attribute
			switch ($measure_id) {
                default:
                case '60621009':
                    $attr = 'imc';
                    break;

                case '301333006':
                    $attr = 'peso';
                    break;

                case '165109007':
                    $attr = 'tmb';
                    break;

                case '271649006':
                    $attr = 'sistolica';
                    break;

                case '271650006':
                    $attr = 'diastolica';
                    break;

                case '366199006':
                    $attr = 'pulso';
                    break;

                case '104847001':
                    $attr = 'oxigeno';
                    break;

                case '86290005':
                    $attr = 'fr_respiratoria';
                    break;

                case '386725007':
                	$attr = 'temperatura';
                	break;
            }


            // Get state
            switch ($measure_id) {
                case '52302001':
                	$attr = 'glugcosa';
                    $state = '5a1e8b25e07481';
                    break;

                // postpandrial
                case '271063001':
                	$attr = 'glugcosa';
                    $state = '5a1e8b25e07482';
                    break;

                case '33747003':
                	$attr = 'glugcosa';
                    $state = '5a1e8b25e07482';
                    break;
            }



			$response[$template_id][$time][$attr] = $value;
            if ($state) {
                $response[$template_id][$time]['estado'] = $state;
            }

		});



		/** @var $datum Array A collction of IDs grouped by report id */
    	$datum = [

    		25 => [
    			'persona' => '5890542e1d0321',
			    'glucosa' => '5890542e1d0323',
			    'fecha' => '5890542e1d0322',
			    'hora' => '5a17109428e6b0',
			    'estado' => '5a1e8b25e07480'
    		],


    		27 => [
    			'persona' => '58905621bbd841',
				'fecha' => '58905621bbd842',
				'peso' => '58905621bbd843',
				'imc' => '58905621bbd844',
				'tmb' => '58905621bbd845',
				'water_percentage' => '58905621bbd846',
				'fat_percentage' => '58905621bbd847',
				'dmd' => '58905621bbd848',
				'imm' => '58905621bbd849',
			],

			28 => [
				'persona' => '589056a213b6f1',
			    'fecha' => '589056a213b6f2',
			    'sistolica' =>  '589056a213b6f3',
			    'diastolica' => '589056a213b6f4'
			],

			37 => [
				'persona' => '58e65e65d6d6a1',
    			'fecha' => '58e65e65d6d6a2',
			    'pulso' => '58e65e65d6d6a3',
			    'oxigeno' => '58e65e65d6d6a4'
			],

			48 => [
				'persona' => '5a2934ce421820',
				'fr_respiratoria' => '5a26d95b2f1ba0',
				'fecha' => '5a26d95b2f1ba1'
			],

			49 => [
				'persona' => '5a2934617b17c0',
			    'temperatura' => '5a26dd2e84c3a1',
			    'fecha' => '5a26dd2e84c3a2'
			]

		];


		// Get every form
		foreach (array_keys ($datum) as $template_id) {

			// Maybe our CDA does not have data for this template
			if ( ! isset ($response[$template_id])) {
				continue;
			}

			// Get every set of data grouped by time
	        foreach ($response[$template_id] as $time => $measures) {

	    		/** @var $object Array */
	    		$object = [];


	    		// For every field in the report
	    		foreach ($datum[$template_id] as $key => $data) {

					// Only works for diabetes

					if ("persona" == $key) {
						$object[$data] = [
							'id' => $data,
							'value' => $patient_id
						];

	    			} elseif ("hora" == $key) {

						/** @var $format_hour String hh:ii AM|PM */
						$format_hour = substr ($time, 8, 14);
						$format_hour = str_pad ($format_hour, 4, "0", STR_PAD_LEFT);

						$hour = substr ($format_hour, 0, 2) * 1;
						$hour_clock = $hour >= 12 ? $hour / 12 : $hour;
						$hour_clock = str_pad ($hour_clock, 2, "0", STR_PAD_LEFT);

						$format_hour = $hour_clock . ':' . substr ($format_hour, 2, 2) . ($hour >= 12 ? ' PM' : ' AM');


						$object[$datum[$template_id]['hora']] = [
							'id' => $datum[$template_id]['hora'],
							'value' => $format_hour
						];

					// Fecha is taken from $time
	    			} elseif ("fecha" == $key) {

						/** @var $format_date String yyyy-mm-dd */
						$format_date = substr ($time, 0, 4) . '-' . substr ($time, 4, 2) . '-' . substr ($time, 6, 2);



						$object[$data] = [
							'id' => $data,
							'value' => $format_date
						];

					// Skip missing measures
					} elseif (isset ($measures[$key])) {
						$object[$data] = [
							'id' => $data,
							'value' => $measures[$key]
						];
					}
	    		}


	    		// Get reports in the same time, to avoid duplicates
	    		// @todo. In case of glucose, also check hour
				$sql_check_report = "
					SELECT *
					FROM
							reports
					WHERE
							template_id = ? AND
							datum->'" . $datum[$template_id]['persona'] . "'->>'value' = ? AND
							datum->'" . $datum[$template_id]['fecha'] . "'->>'value' = ?

					LIMIT 1
				";

				/** var $sql_check_device_params Array */
				$sql_check_report_params = [
						$template_id,
						$patient_id,
						$format_date
				];


				/** var $sql_check_device_params Array */
				$result = DB::select (DB::raw ($sql_check_report), $sql_check_report_params);



				// Continue to the next time
				if (count ($result) >= 1) {
					continue;
				}



				/** @var $max_id Array Get next max id for the new report */
				$max_id = DB::select (DB::raw ("SELECT id FROM reports ORDER BY id DESC LIMIT 1"));
				$max_id = reset ($max_id);
				$max_id = $max_id->id;
				$max_id = $max_id + 1;


				/** @var $datum_to_store String A JSON */
				$datum_to_store = DB::raw (json_encode ($object));


				/** @var $params Array */
				$params = [
					$max_id,
					$user_id,
					$template_id,
					trim ($datum_to_store),
					$created_at,
					$created_at,
					$created_at
				];


				/** @var $sql String The SQL query statement */
				$sql = "
					INSERT INTO
						reports (id, user_id, template_id, datum, created_at, updated_at, main_date)
					VALUES
						(?, ?, ?, ?, ?, ?, ?)
					;
				";


				// Create the register
				$result = DB::select (DB::raw ($sql), $params);

	        }
	    }


		// Process ended, redirect to health
        return Redirect::to('health');
        die ();


	}


	/**
	 * subscribeToPushNotification
	 *
	 * Registers the push notifications for the mobile application
	 *
	 * @param $patient_id int Patient ID
	 * @param $token String The token identifies the device
	 *
	 * @author José Antonio García Díaz <joseantonio.garcia8@um.es>
	 */
	public function subscribeToPushNotification () {

		/** @var $user_id int We use the admin user "!" instead of Auth::user()->id; */
		$user_id = 1;


		/** @var $max_id Array */
		$max_id = DB::select (DB::raw ("SELECT id FROM reports ORDER BY id DESC LIMIT 1"));
		$max_id = reset ($max_id);
		$max_id = $max_id->id;
		$max_id = $max_id + 1;


		/* @var $template_id The report to store user device tokens */
		$template_id = 52;


		/** @var $device_token String */
		$device_token = Input::get ('device_token');
		if ( ! $device_token) {
			header ('Content-type: application/json');
			echo json_encode(['ok' => false, 'message' => 'No ID TOKEN']);
			die ();
		}


		/** @var $patient_id int The user ID */
		$patient_id = Input::get ('patient_id') * 1;
		if ( ! $patient_id) {
			header ('Content-type: application/json');
			echo json_encode(['ok' => false, 'message' => 'No patient']);
			die ();

		}


		/** @var $created_at String */
		$created_at = date ('Y-m-d H:i:s', time ());



		/** @var $sql_check_device String Check if the device is registered (deleted or not)*/
		$sql_check_device = "
			SELECT *
			FROM
					reports
			WHERE
					template_id = ? AND
					datum->'5af1e21fce7020'->>'value' = ? AND
					datum->'5af1e2c8ecc5c0'->>'value' = ?

			LIMIT 1
		";


		/** var $sql_check_device_params Array */
		$sql_check_device_params = [
				$template_id,
				$patient_id,
				$device_token
		];


		/** var $sql_check_device_params Array */
		$result = DB::select (DB::raw ($sql_check_device), $sql_check_device_params);


		/** @var $is_registered bool */
		$is_registered = count ($result) == 1;



		// If the device does not exists, register it
		if ( ! $is_registered) {

			/** @var $datum String A JSON */
			$datum = DB::raw ('
				{
					"5af1e21fce7020": {
							"id": "5af1e21fce7020",
							"value": "' . $patient_id . '"
					},
					"5af1e2c8ecc5c0": {
								"id": "5af1e2c8ecc5c0",
							"value": "' . $device_token . '"
					}
				}
			');


			/** @var $params Array */
			$params = [
				$max_id,
				$user_id,
				$template_id,
				trim ($datum),
				$created_at,
				$created_at,
				$created_at
			];


			/** @var $sql String The SQL query statement */
			$sql = "
				INSERT INTO
					reports (id, user_id, template_id, datum, created_at, updated_at, main_date)
				VALUES
					(?, ?, ?, ?, ?, ?, ?)
				;
			";


			// Create the register
			$result = DB::select (DB::raw ($sql), $params);


			// Return
			header ('Content-type: application/json');
			echo json_encode(['ok' => true, 'push' => true]);
			die ();


		}


		/** @var $new_is_deleted_state bool Toogle the state */
		$new_is_deleted_state = $result[0]->{'deleted_at'} ? null : $created_at;



		/** @var $sql_update_device String */
		$sql_update_device = "
				UPDATE
						reports
				SET
						deleted_at=?
				WHERE
						template_id = ? AND
						datum->'5af1e21fce7020'->>'value' = ? AND
						datum->'5af1e2c8ecc5c0'->>'value' = ?
			";

		/** var $sql_unregister_device_params Array */
		$sql_update_device_params = [
				$new_is_deleted_state,
				$template_id,
				$patient_id,
				$device_token
		];

		$result = DB::select ($sql_update_device, $sql_update_device_params);


		// Return the new state
		header ('Content-type: application/json');
		echo json_encode(['ok' => true, 'push' => $new_is_deleted_state ? true : false]);
		die ();

	}


	/**
	 * getPushState
	 *
	 * Determines if the user has the push notifications enabled or disabled

	 * @author José Antonio García Díaz <joseantonio.garcia8@um.es>
	 */
	public function getPushState () {

		/** @var $user_id int We use the admin user "!" instead of Auth::user()->id; */
		$user_id = 1;


		/* @var $template_id The report to store user device tokens */
		$template_id = 52;


		/** @var $device_token String */
		$device_token = Input::get ('device_token');
		if ( ! $device_token) {
			header ('Content-type: application/json');
			echo json_encode(['ok' => false, 'message' => 'No ID TOKEN']);
			die ();
		}


		/** @var $patient_id int The user ID */
		$patient_id = Input::get ('patient_id') * 1;
		if ( ! $patient_id) {
			header ('Content-type: application/json');
			echo json_encode(['ok' => false, 'message' => 'No patient']);
			die ();

		}


		/** @var $sql_check_device String Check if the device is registered (deleted or not)*/
		$sql_check_device = "
			SELECT *
			FROM
					reports
			WHERE
					template_id = ? AND
					datum->'5af1e21fce7020'->>'value' = ? AND
					datum->'5af1e2c8ecc5c0'->>'value' = ?

			LIMIT 1
		";


		/** var $sql_check_device_params Array */
		$sql_check_device_params = [
				$template_id,
				$patient_id,
				$device_token
		];


		/** var $sql_check_device_params Array */
		$result = DB::select (DB::raw ($sql_check_device), $sql_check_device_params);


		/** @var $is_registered bool */
		$is_registered = count ($result) == 1;



		// Return
		header ('Content-type: application/json');
		echo json_encode(['ok' => true, 'push' => $is_registered]);
		die ();


	}



	/**
	 * getCDA
	 *
	 * Returns an importantion of the HL7 - CDA template
	 * of the patient
	 *
	 * @param $patient_id If CDA was requested by a doctor
	 *
	 * @author José Antonio García Díaz <joseantonio.garcia8@um.es>
	 * @see >https://github.com/Smolky/premytecd>
	 */
	public function getCDA($patient_id=null) {

		// Retrieve patient ID
		if ( ! $patient_id) {
			$patient_id = DB::select(DB::raw ("SELECT id from reports where template_id = 10 and user_id = ".Auth::user()->id))[0]->id;
		}

		// Get JSON
		// @todo Add route to config
		$historial = file_get_contents ("http://nimbeo.com:8080/iohealth_webservice/api/service/history?user_id=" . $patient_id);


        // Convert to JSON
        $historial = json_decode ($historial, true);



        // Retrieve information about drugs
        /** @var $drugs Array */
        $drugs = [];
        foreach ($historial['nombres_medicamentos'] as $drug) {
            $drugs[$drug['id_medicamento']] = [
                'id'        => $drug['id_medicamento'],
                'category'  => $drug['categoria'],
                'name'      => $drug['nombre'],
                'family'    => $drug['familia'],
                'code'      => isset ($drug['id_RxNorm']) ? $drug['id_RxNorm'] : null
            ];
        }


        // Fill gaps
        foreach (['nombre', 'apellidos', 'genero', 'birth'] as $field) {
            $historial[$field] = isset ($historial[$field]) ? $historial[$field] : '';
        }


		// Prepare Data
		$data = [

            // Snomed CT information
            // @see https://www.msssi.gob.es/profesionales/hcdsns/areaRecursosSem/snomed-ct/quees.htm
            'snomed' => [
                'code' => '2.16.840.1.113883.6.96',
                'name' => 'SNOMED CT'
            ],


            // RxNorm
            // @see Web Search <https://mor.nlm.nih.gov/RxNav/>
            // @see API <https://rxnav.nlm.nih.gov/REST/rxcui?name=lipitor>
            'rxnorm' => [
                'code' => '2.16.840.1.113883.6.88',
                'name' => 'RxNorm'
            ],


            // Patient information
            'patient' => [
                'id' => $patient_id,
                'genre' => $historial['genero'] == 'hombre' ? 'M' : 'F',
                'name' => trim ($historial['nombre'] . ' ' . $historial['apellidos']),
                'birth' => str_replace ('-', '', $historial['f_nacimiento'])
            ],


            // Clinical Document Architecture
			'cda' => [
				'created_at' => date ("YmdHi"),
				'author' => "2.16.840.1.113883.19",
				'root' => "2.16.840.1.113883.19",
				"custodian" => "2.16.840.1.113883.19"
			],


			// Document information
			'document' => [
				'id' => '1',
				'date' => date ('YmdHi')
			],
		];



       // Init measure vars
        foreach ([
            'body_weight', 'glucose', 'blood_pressure',
            'pulse', 'oxygen', 'temperature', 'breathing_frequency'
        ] as $key) {
            $data['measures'][$key] = [];
        }


        // Weight measures
        foreach ($historial['peso_corporal'] as $record) {
            $data['measures']['body_weight'][] = [
                'time'         => $this->getCDACompilantTime ($record),
                'textual_time' => $record['fecha_medición'],
                'weight'       => isset ($record['peso']) ? $record['peso'] : null,
                'bmi'          => isset ($record['imc']) ? $record['imc'] : null,
                'bmr'          => isset ($record['tmb']) ? $record['tmb'] : null,
            ];
        }


        // Glocuse measures
        foreach ($historial['glucosa'] as $record) {

            if ( ! isset ($record['estado'])) {
            	$record['estado'] = "";
            }

            switch ($record['estado']) {
                case 'ayuna':
                    $code = '52302001';
                    $name = 'glucose-fasting';
                    break;

                case 'postpandrial':
                    $code = '271063001';
                    $name = 'glucose-lunch-time';
                    break;

                default:
                    $code = '33747003';
                    $name = 'glucose';
                    break;

            }


            $data['measures']['glucose'][] = [
                'code' => $code,
                'name' => $name,
                'time' => $this->getCDACompilantTime ($record),
                'textual_time' => $record['fecha_medición'],
                'glucose' => isset ($record['glucosa']) ? $record['glucosa'] : null
            ];
        }


        // Blood preassure measures
        foreach ($historial['presion_sanguinea'] as $record) {
            $data['measures']['blood_pressure'][] = [
                'time' => $this->getCDACompilantTime ($record),
                'textual_time' => $record['fecha_medición'],
                'systolic' => isset ($record['sistolica']) ? $record['sistolica'] : null,
                'diastolic' => isset ($record['diastolica']) ? $record['diastolica'] : null,
            ];
        }


        // Pulse and oxygen measures
        foreach ($historial['pulso_oxigeno'] as $record) {
            $data['measures']['pulse'][] = [
                'time' => $this->getCDACompilantTime ($record),
                'textual_time' => $record['fecha_medición'],
                'pulse' => isset ($record['pulso']) ? $record['pulso'] : null
            ];

            $data['measures']['oxygen'][] = [
                'time' => $this->getCDACompilantTime ($record),
                'textual_time' => $record['fecha_medición'],
                'oxygen' => isset ($record['oxigeno']) ? $record['oxigeno'] : null
            ];
        }


        // Breathing frequency measures
        foreach ($historial['frecuencia_respiratoria'] as $record) {
            $data['measures']['breathing_frequency'][] = [
                'time' => str_replace ('-', '', $record['fecha_medición']),
                'textual_time' => $record['fecha_medición'],
                'breathing_frequency' => $record['fr_respiratoria']
            ];
        }


        // temperature measures
        foreach ($historial['temperatura_corporal'] as $record) {
            $data['measures']['temperature'][] = [
                'time' => $this->getCDACompilantTime ($record),
                'textual_time' => $record['fecha_medición'],
                'temperature' => $record['temp_corporal']
            ];
        }


        // Substance administration
        $data['substances'] = [];
        foreach ($historial['registro_medicacion'] as $record) {
            $data['substances'][] = [
                'time' => $this->getCDACompilantTime ($record),
                'textual_time' => $record['fecha_medición'],
                'drug' => $drugs[$record['id_medicamento']],
                'doses' => $record['cantidad']
            ];
        }


        // Response
		header ('Content-Disposition: attachment; filename=hl7-cda-' . $patient_id . '-' . date ('YmdHi') . '.xml');
		$contents = View::make ('export.cda', $data);
		$response = Response::make ($contents, 200);
		$response->header ('Content-Type', "text/xml");


		return $response;

	}



    /**
     * getCDACompilantTime
     *
     * A moment in time is often termed a “timestamp” in CDA.
     * There are many contexts in which an XML sub-element is added
     * to “something” in CDA in order to capture the moment in time
     * when that “something” happened.
     *
     * The moments are a timestamp with the format: YYYYMMDDhhmmss.SSSS±ZZzz
     *
     * @param $record Array a record from the JSON
     *
     * @return String
     *
     * @see http://www.cdapro.com/know/25001
     *
     * @package PreMyTECD
     */
    private function getCDACompilantTime ($record) {

        /** @var $hour String The default value when hour is not recorded */
        $hour = "0000";


        /** @var $$hour_field To reference the key where the hour is stored */
        $hour_field = '';



        // Determine if the record has some type of hour
        if (isset ($record['hora'])) {
            $hour_field = 'hora';
        } elseif (isset ($record['hora_medicion'])) {
            $hour_field = 'hora_medicion';
        }


        // Get the hour and translate it to a "hhmm" format
        if ($hour_field) {
            $hour = str_replace (':', '', $record[$hour_field]) * 1;
            if (strpos ($hour, 'PM') !== false) {
                $hour = ($hour * 1) + 1200;
            }
        }


        // There are case where fecha_medicion contains also
        // the hour
		$date = $record['fecha_medición'];
        if (false !== strpos ($date, ":")) {
        	if ("0000" === $hour) {
        		$hour = "";
        	}
        }

        $date = str_replace ('-', '', $date);
        $date = str_replace (':', '', $date);
        $date = str_replace (' ', '', $date);



        // Return the date and the hour
        return $date . $hour;

    }


}

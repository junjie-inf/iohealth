<?php
	use OAuth2\OAuth2;
	use OAuth2\Token_Access;
	use OAuth2\Exception as OAuth2_Exception;

	use GuzzleHttp\Client;

class IHealthController extends BaseController {

	private $id;
	private $secret;
	private $APIName;
	private $redirect_uri;
	private $client;
	private $sc;
	private $sv;
	private $inpu;

	public function __construct()
	{
		//// For Pull + subscription data
		// $this->id = 'bd27acf3f5f64fc48c120eeb4b7dfb9d';
		// $this->secret = '104a8235144a483fa7442b31a8b630c7';
		// $this->APIName = 'OpenApiBP OpenApiUserInfo OpenApiSpO2 OpenApiWeight';
		// $this->redirect_uri = 'https://iohealth.nimbeo.com/iohealth/register';
		// $this->sc = '280C2617C80A406998BFB548D40B7257';
		// $this->sv = array(
		// 		'spo2' => '380FB0C19CE04F4890A925EABF53EFB4',
		// 		'bp' => '762CF4CA82F7486BA915CC8007085116',
		// 		'user' => 'CC1BF47B400E4C4590A64C899F3C7BCF',
		// 		'weigth' => 'DBCDC0EA1B9041D7B75614F706448421'
		// 	);

		// For Push data
		/*
		$this->id = 'deff2a8796cc4670b2391b10427b906f';
		$this->secret = '62005931bd0044e4b9e0e8ffa77774e0';
		$this->APIName = 'OpenApiBP OpenApiUserInfo OpenApiSpO2 OpenApiWeight';
		$this->redirect_uri = 'https://iohealth.nimbeo.com/ihealth/register';
		$this->sc = ' 280c2617c80a406998bfb548d40b7257';
		$this->sv = array(
				'spo2' => '380fb0c19ce04f4890a925eabf53efb4',
				'bp' => ' 762cf4ca82f7486ba915cc8007085116',
				'user' => 'cc1bf47b400e4c4590a64c899f3c7bcf',
				'weigth' => 'dbcdc0ea1b9041d7b75614f706448421'
			);
			*/
		$this->id = '2b7a4aa62a9d4c0fbec0d79dfafda39e';
		$this->secret = '22c587e81f504c1ab48e11ce4756133d';
		$this->APIName = 'OpenApiBP OpenApiUserInfo OpenApiSpO2 OpenApiWeight OpenApiBG';
		$this->redirect_uri = 'https://iohealth.nimbeo.com/ihealth/register';
		$this->sc = '280c2617c80a406998bfb548d40b7257';
		$this->sv = array(
				'spo2' => '380fb0c19ce04f4890a925eabf53efb4',
				'bp' => '762cf4ca82f7486ba915cc8007s085116',
				'user' => 'cc1bf47b400e4c4590a64c899f3c7bcf',
				'weigth' => 'dbcdc0ea1b9041d7b75614f706448421',
				'bg' => 'ce61a4cafbcd4bdaad98adb4ce884361',
			);
	}


	public function register()
	{

		if ( ! isset($_GET['code']))
		{
			return IHealthController::tokenGrant();
		}
		else
		{
			$params = IHealthController::accessToken($_GET['code']);

			//$report = Report::where(DB::raw("datum#>>'{5889efb0944a90,value}'"), '=', '2')->first();
			//$report = Report::where(DB::raw("datum#>>'{5889efb0944a90,value}'"), '=', Auth::user()->getKey())->get();
			$report = Report::where(DB::raw("datum#>>'{5889efb0944a90,value}'"), '=', Auth::user()->id)->first();

			if($report != null){
				$datum = $report->datum;
				$datum->IHealthID = (object) array('id' => 'IHealthID', 'value' => $params->UserID);
				$report->datum = $datum;

				//CAST('" . $report->datum . "' AS json)::jsonb
						DB::select(DB::raw(
							"UPDATE reports
								SET datum = CAST('" . json_encode($report->datum) . "' AS json)::jsonb
								WHERE user_id = ".Auth::user()->id." and template_id = 10"
						));
						
				//$report->save();
			}

			Session::put('access_token', $params->AccessToken);
			Session::put('refresh_token', $params->RefreshToken);
			Session::put('user_id', $params->UserID);
			Session::put('expires', $params->Expires);

		  
			return  Redirect::to('/');

		}
	}


	public function sendRequest ($method, $url, $params){

		$client = new Client([
		// Base URI is used with relative requests
		'base_uri' => 'https://api.ihealthlabs.com:8443/OpenApiV2/OAuthv2/userauthorization/?'
		]);
		// $ca = public_path() . "/idscertificatePull.pem";
		$ca = public_path() . "/idscertificate.pem";

		//needs to be pem no p12
		// openssl pkcs12 -in path.p12 -out newfile.pem - nodes

		switch ($method)
		{
			case 'GET':
				// Need to switch to Request library, but need to test it on one that works
				$response = $client->get($url,
					[	'query' => $params,
						'verify' => false,
						'cert' => [$ca, 'kTTSVgpX']
						// 'cert' => [$ca, 'i5NxR5CF']
					])->getBody()->read(1024);

				$return = json_decode($response);

			break;

			case 'POST':
				$response = $client->post($url ,[
					// 'body'  => 	
					'json'  => $params,
					'verify' => false,
					'cert' => [$ca, 'kTTSVgpX']
					// 'cert' => [$ca, 'i5NxR5CF']
				])->getBody()->read(1024);

				$return = json_decode($response, true);

			break;

			default:
				throw new Exception('Method \'' . $this->method . '\' must be either GET or POST');
		}
		return $return;
	}

	public function accessToken($code){
		$params = array('client_id' => $this->id,
					'client_secret' => $this->secret,
					'redirect_uri' => $this->redirect_uri,
					'grant_type' => 'authorization_code',
					'code' => $code
				);

		$url = 'https://api.ihealthlabs.com:8443/OpenApiV2/OAuthv2/userauthorization/?';

		return IHealthController::sendRequest('GET', $url, $params);

	}

	public function tokenGrant(){
		$params = array('client_id' => $this->id,
					'client_secret' => $this->secret,
					'redirect_uri' => $this->redirect_uri,
					'response_type' => 'code',
					'APIName' => $this->APIName
				);

		$url = 'https://oauthuser.ihealthlabs.eu/OpenApiV2/OAuthv2/userauthorization/?'. http_build_query($params);
	//dd(($url));
		return Redirect::to($url);
	}

	public function refresh(){
		$params = array('client_id' => $this->id,
					'client_secret' => $this->secret,
					'redirect_uri' => $this->redirect_uri,
					'response_type' => 'refresh_token',
					'refresh_token' => Session::get('refresh_token'),
					'UserID' => Session::get('user_id')
				);
		$url = 'https://api.ihealthlabs.com:8443/OpenApiV2/OAuthv2/userauthorization/?'.http_build_query($params);
		$response = file_get_contents($url);
		$return = json_decode($response, true);

		return $return;
	}

	public function subscription (){

		$data = Input::json()->all();

		$curl = array('status' => 'OK', 'data' => $data );

		try{
			$person = Report::where(DB::raw("datum#>>'{IHealthID,value}'"), '=', $data['UserID'])->first();
			if ($person != null) {
				$user = User::find($person->datum->{'5889efb0944a90'}->value);

				if(Auth::onceUsingId($user->getKey())){
					switch ($data['CollectionType'])
					{
						case 'weight':
							IHealthController::createWeigth($data, $person, $user);
							break;
						case 'bp':
							IHealthController::createBP($data, $person, $user);
							break;
						case 'spo2':
							IHealthController::createSpo2($data, $person, $user);
							break;
						case 'glucose':
							IHealthController::createBG($data, $person, $user);
							break;
					}
				}
			}
			else{
				$curl['status'] = "error in finding user";
				$curl['message'] = "No user Found";
			}
		}catch (Exception $e) {

			$curl['status'] = "subscription error in create report";
			$curl['message'] = $e->getMessage();

		}
		
		$ch = curl_init('https://requestb.in/yqgicqyq');

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($ch);



		if($result == false)
			return curl_error($ch);

		curl_close($ch);

		return http_response_code(200);
	}

	public function getAccessToken(){
		return array('AccessToken' => Session::get('access_token'),
					'RefreshToken' => Session::get('refresh_token'),
					'UserID' => Session::get('user_id'),
					'Expires' => Session::get('expires')
		 );
	}


	public function input(){
		// return Session::get('input');
		return array ('sesion' =>  Session::get('input'), 'local' => $this->input);
	}

	public function removal (){

		return ;
	}

	

	public function getWeigth (){

		$url = 'https://openapi.ihealthlabs.eu/openapiv2/application/weight.json/';

		$params = array('client_id' => $this->id,
					'client_secret' => $this->secret,
					'redirect_uri' => $this->redirect_uri,
					'sc' => $this->sc,
					'sv' => $this->sv['weigth'],
					'access_token' => Session::get('access_token')
				);

		$return = IHealthController::sendRequest('GET', $url, $params);
		// dd($return);
		return (array)$return;
	}

	public function getData ($type, $UserID, $date){

		$url = 'https://openapi.ihealthlabs.eu/openapiv2/user/'.$UserID.'/'.$type.'.json/';

		$params = array('client_id' => $this->id,
					'client_secret' => $this->secret,
					'redirect_uri' => $this->redirect_uri,
					'sc' => $this->sc,
					'sv' => $this->sv['weigth'],
					'access_token' => Session::get('access_token'),
					'start_time' => $date
				);

		$return = IHealthController::sendRequest('GET', $url, $params);
		return (array)$return;
	}


	public function createBP ( $data , $person, $user){

		$report = new Report;

		$template = Template::find( 28 );
		
		// Asociamos relaciones externas
		$report->user()->associate( $user );
		$report->template()->associate( $template );
		
		//Extraer fecha principal
		if (!isset($template->settings->date))
			$newValue = null;
		else if ($template->settings->date == '_created_at')
			$newValue = DB::raw('now()');
		else
			$newValue = object_get($report->datum, $template->settings->date)->value;
		$report->main_date = $newValue;

		$datum = new stdClass();

		$datum->{'589056a213b6f0'} = (object)array('id' => '589056a213b6f0', 'value' => (string)$data['Data']['DataId']);	//ID
		// $datum->{'589056a213b6f1'} = (object)array('id' => '589056a213b6f1', 'value' => $data['UserID']);				//Persona
		$datum->{'589056a213b6f1'} = (object)array('id' => '589056a213b6f1', 'value' => $person->getKey());							//Persona
		$datum->{'589056a213b6f2'} = (object)array('id' => '589056a213b6f2', 'value' => $data['Data']['MeasureTime']);		//Fecha
		$datum->{'589056a213b6f3'} = (object)array('id' => '589056a213b6f3', 'value' => $data['Data']['Systolic']);			//Sistolica
		$datum->{'589056a213b6f4'} = (object)array('id' => '589056a213b6f4', 'value' => $data['Data']['Diastolic']);		//Diastolica
		$datum->{'589056a213b6f5'} = (object)array('id' => '589056a213b6f5', 'value' => $data['Data']['HeartRate']);		//Pulso

		$report->datum = $datum;

		if( $report->save() ){
			Event::fire('report.store', $report);
			return 0;
		}
		return -1;

	}

	public function createWeigth ( $data , $person, $user ){

		$report = new Report;

		$template = Template::find( 27 );
		
		// Asociamos relaciones externas
		$report->user()->associate( $user );
		$report->template()->associate( $template );
		
		//Extraer fecha principal
		if (!isset($template->settings->date))
			$newValue = null;
		else if ($template->settings->date == '_created_at')
			$newValue = DB::raw('now()');
		else
			$newValue = object_get($report->datum, $template->settings->date)->value;
		$report->main_date = $newValue;

		$datum = new stdClass();

		$datum->{'58905621bbd840'} = (object)array('id' => '58905621bbd840', 'value' => (string)$data['Data']['DataId']);	//ID
		// $datum->{'589056a213b6f1'} = (object)array('id' => '589056a213b6f1', 'value' => $data['UserID']);				//Persona
		$datum->{'58905621bbd841'} = (object)array('id' => '58905621bbd841', 'value' => $person->getKey());							//Persona
		$datum->{'58905621bbd842'} = (object)array('id' => '58905621bbd842', 'value' => $data['Data']['MeasureTime']);		//Fecha
		$datum->{'58905621bbd843'} = (object)array('id' => '58905621bbd843', 'value' => $data['Data']['WeightValue']);		//Peso
		$datum->{'58905621bbd844'} = (object)array('id' => '58905621bbd844', 'value' => $data['Data']['BMI']);				//Índice de masa corporal ( IMC )
		$datum->{'58905621bbd845'} = (object)array('id' => '58905621bbd845', 'value' => $data['Data']['VisceraFatLevel']);		//Tasa metabólica basal ( TMB )
		$datum->{'58905621bbd846'} = (object)array('id' => '58905621bbd846', 'value' => $data['Data']['WaterValue']);		//Porcentaje de agua
		$datum->{'58905621bbd847'} = (object)array('id' => '58905621bbd847', 'value' => $data['Data']['FatValue']);		//Porcentaje de grasa
		$datum->{'58905621bbd848'} = (object)array('id' => '58905621bbd848', 'value' => $data['Data']['BoneValue']);		//Densidad mineral ósea ( DMO )
		$datum->{'58905621bbd849'} = (object)array('id' => '58905621bbd849', 'value' => $data['Data']['MuscaleValue']);		//Índice de masa muscular ( IMM )				
		
		$report->datum = $datum;

		if( $report->save() ){
			Event::fire('report.store', $report);
			return 0;
		}
		return -1;

	}

	public function createSpo2 ( $data , $person, $user ){

		$report = new Report;

		$template = Template::find( 37 );
		
		// Asociamos relaciones externas
		$report->user()->associate( $user );
		$report->template()->associate( $template );
		
		//Extraer fecha principal
		if (!isset($template->settings->date))
			$newValue = null;
		else if ($template->settings->date == '_created_at')
			$newValue = DB::raw('now()');
		else
			$newValue = object_get($report->datum, $template->settings->date)->value;
		$report->main_date = $newValue;

		$datum = new stdClass();

		$datum->{'58e65e65d6d6a0'} = (object)array('id' => '58e65e65d6d6a0', 'value' => (string)$data['Data']['DataId']);	//ID
		// $datum->{'58e65e65d6d6a1'} = (object)array('id' => '58e65e65d6d6a1', 'value' => $data['UserID']);				//Persona
		$datum->{'58e65e65d6d6a1'} = (object)array('id' => '58e65e65d6d6a1', 'value' => $person->getKey());							//Persona
		$datum->{'58e65e65d6d6a2'} = (object)array('id' => '58e65e65d6d6a2', 'value' => $data['Data']['MeasureTime']);		//Fecha
		$datum->{'58e65e65d6d6a3'} = (object)array('id' => '58e65e65d6d6a3', 'value' => $data['Data']['HeartRate']);		//Pulso
		$datum->{'58e65e65d6d6a4'} = (object)array('id' => '58e65e65d6d6a4', 'value' => $data['Data']['BloodOxygen']);		//Oxigeno en Sangre

		$report->datum = $datum;


		if( $report->save() ){
			Event::fire('report.store', $report);
			return 0;
		}
		return -1;

	}

	public function createBG ( $data , $person, $user ){

		$report = new Report;

		$template = Template::find( 25 );
		
		// Asociamos relaciones externas
		$report->user()->associate( $user );
		$report->template()->associate( $template );
		
		//Extraer fecha principal
		if (!isset($template->settings->date))
			$newValue = null;
		else if ($template->settings->date == '_created_at')
			$newValue = DB::raw('now()');
		else
			$newValue = object_get($report->datum, $template->settings->date)->value;
		$report->main_date = $newValue;

		$datum = new stdClass();

		//Format date
		$date = date_create($data['Data']['MeasureTime']);
		$day = date_format($date, 'Y-m-d');
		//date_modify($date, '+2 Hours');
		$hour = date_format($date, 'H:i:s');

		$datum->{'5890542e1d0320'} = (object)array('id' => '5890542e1d0320', 'value' => (string)$data['Data']['DataId']);	//ID
		// $datum->{'58e65e65d6d6a1'} = (object)array('id' => '58e65e65d6d6a1', 'value' => $data['UserID']);				//Persona
		$datum->{'5890542e1d0321'} = (object)array('id' => '5890542e1d0321', 'value' => $person->getKey());					//Persona
		$datum->{'5890542e1d0322'} = (object)array('id' => '5890542e1d0322', 'value' => $day);		//día
		$datum->{'5a17109428e6b0'} = (object)array('id' => '5a17109428e6b0', 'value' => $hour);		//hora
		$datum->{'5890542e1d0323'} = (object)array('id' => '5890542e1d0323', 'value' => $data['Data']['BG']);				//Glucosa en sangre
		

		switch ($data['Data']['DinnerSituation']) 
		{
			case '10':
				$estado = '5b573c039e63a0'; break;
			case 'Before_breakfast':
				$estado = '5b573c039e63a1'; break;
			case 'After_breakfast':
				$estado = '5b573c039e63a2'; break;
			case 'Before_lunch':
				$estado = '5b573c039e63a3'; break;
			case 'After_lunch':
				$estado = '5b573c039e63a4'; break;
			case 'Before_dinner':
				$estado = '5b573c039e63a5'; break;
			case 'After_dinner':
				$estado = '5b573c039e63a6'; break;
			case 'At_midnight':
				$estado = '5b573c039e63a7'; break;
			case '8':
				$estado = '5b573c039e63a8'; break;
			case '9':
				$estado = '5b573c039e63a9'; break;
		}
		$datum->{'5a1e8b25e07480'} = (object)array('id' => '5a1e8b25e07480', 'value' => $estado);	

		$report->datum = $datum;


		if( $report->save() ){
			Event::fire('report.store', $report);
			return 0;
		}
		return -1;

	}

	public function generateTest0 (){

		for ($i=0; $i < 1 ; $i++) { 
			// Convert to timetamps
			$min = strtotime("now");
			$max = strtotime("-1 year");

			// Generate random number using above bounds
			$val = rand($min, $max);

			// Convert back to desired date format
			$day = date('Y-m-d H:i:s', $val);

			$jsonBP = '{
				"CollectionType":"bp",
				"CollectionUnit":0,
				"Data":{
					"DataId":"d1ea4f3bfd4e4043807c26e909f",
					"Diastolic":'.rand(60,100).',
					"HeartRate":'.rand(60,190).',
					"Lat":0,
					"Lon":0,
					"MeasureTime":"'.$day.'",
					"Note":"56",
					"Systolic":'.rand(100,160).',
					"TimeZone":"+0000",
					"time_zone":"+01:00",
					"LastChangeTime":"2014-05-12 17:25:30",
					"DataSource":"FromDevice"
				},
				"PushId":"9f543f2bca8343e2bc1f44b2d10",
				"UserID":"71a73a3cdc55443cb1086c259f3"
			}';

			$jsonWeight = '{
			  "CollectionType": "weight", 
			  "CollectionUnit": 0, 
			  "Data": {
				"BMI": '.rand(10,100).', 
				"BoneValue": '.rand(10,100).', 
				"DataId": "d3bf513182fb4b0eab7703e49f7", 
				"FatValue": '.rand(10,100).', 
				"Lat": 0, 
				"Lon": 0, 
				"MeasureTime": "'.$day.'", 
				"MuscleValue": '.rand(10,100).', 
				"Note": "", 
				"TimeZone": "+0000", 
				"time_zone": "+01:00", 
				"VisceraFatLevel": '.rand(10,100).', 
				"WaterValue": '.rand(10,100).', 
				"WeightValue": '.rand(3,140).', 
				"LastChangeTime": "2014-04-04 00:10:38", 
				"DataSource": "FromDevice"
			  }, 
			  "PushId": "91d975df4e384848bfa03c46487", 
			  "UserID": "6239f842dec4427f8c2c6dfd4b6"
			}';


			$jsonSpO2 = '{
			  "CollectionType": "spo2", 
			  "CollectionUnit": 0, 
			  "Data": {
				"BloodOxygen": '.rand(10,100) /*rand(90,100)*/.', 
				"DataId": "c58dd5f85f9f4dc59baa06d17f9", 
				"HeartRate": '.rand(60,190).', 
				"Lat": 0, 
				"Lon": 0, 
				"MeasureTime": "'.$day.'", 
				"Note": "", 
				"TimeZone": "+0000", 
				"time_zone": "+01:00", 
				"LastChangeTime": "2014-04-09 19:04:27", 
				"DataSource": "FromDevice"
			  }, 
			  "PushId": "a548d319997c47778a20a0b4fa6", 
			  "UserID": "6239f842dec4427f8c2c6dfd4b6"
			}';

			// var_dump($jsonBP, $jsonWeight, $jsonSpO2);
			// dd(json_decode($jsonBP), json_decode($jsonWeight), json_decode($jsonSpO2));

			$person = 17;
			IHealthController::createBP(json_decode($jsonBP,true), $person );
			IHealthController::createWeigth(json_decode($jsonWeight,true), $person );
			IHealthController::createSp02(json_decode($jsonSpo2,true), $person );

			return 'ok';

		}	    

		// $data = json_encode($input);

		// $headers = array(
		//     'Content-Type: application/json',
		//     'Content-Length: ' . strlen($data)
		// );

		// $ch = curl_init('http://requestb.in/1hnytlj1');

		// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// $result = curl_exec($ch);
		// // curl_close($ch);
	}

	public function generateTest (){

		// Convert to timetamps
		$val = strtotime("now");

		// Convert back to desired date format
		$day = date('Y-m-d H:i:s', $val);

		// $data = array(	'diastolic' => 70, 
		// 				'sistolic' => 120, 
		// 				'bmi' => 23, 
		// 				'spo2' => 97, 
		// 				'weight' => 82.5
		// 	);

		$data = array(	'diastolic' => 90, 
						'sistolic' => 140, 
						'bmi' => 27, 
						'spo2' => 87, 
						'weight' => 93.5
			);

		// $data = array(	'diastolic' => 100, 
		// 				'sistolic' => 150, 
		// 				'bmi' => 34, 
		// 				'spo2' => 75, 
		// 				'weight' => 146.5
		// 	);

		$jsonBP = '{
			"CollectionType":"bp",
			"CollectionUnit":0,
			"Data":{
				"DataId":"d1ea4f3bfd4e4043807c26e909f",
				"Diastolic":'. $data['diastolic'] .',
				"HeartRate":'. rand(60,190) .',
				"Lat":0,
				"Lon":0,
				"MeasureTime":"'.$day.'",
				"Note":"56",
				"Systolic":'. $data['sistolic'] .',
				"TimeZone":"+0000",
				"time_zone":"+01:00",
				"LastChangeTime":"2014-05-12 17:25:30",
				"DataSource":"FromDevice"
			},
			"PushId":"9f543f2bca8343e2bc1f44b2d10",
			"UserID":"71a73a3cdc55443cb1086c259f3"
		}';

		$jsonWeight = '{
		  "CollectionType": "weight", 
		  "CollectionUnit": 0, 
		  "Data": {
			"BMI": '. $data['bmi'] .', 
			"BoneValue": '. rand(10,100) .', 
			"DataId": "d3bf513182fb4b0eab7703e49f7", 
			"FatValue": '. rand(10,100) .', 
			"Lat": 0, 
			"Lon": 0, 
			"MeasureTime": "'.$day.'", 
			"MuscleValue": '. rand(10,100) .', 
			"Note": "", 
			"TimeZone": "+0000", 
			"time_zone": "+01:00", 
			"VisceraFatLevel": '. rand(10,100) .', 
			"WaterValue": '. rand(10,100) .', 
			"WeightValue": '. rand(3,180) .', 
			"LastChangeTime": "2014-04-04 00:10:38", 
			"DataSource": "FromDevice"
		  }, 
		  "PushId": "91d975df4e384848bfa03c46487", 
		  "UserID": "6239f842dec4427f8c2c6dfd4b6"
		}';


		$jsonSpO2 = '{
		  "CollectionType": "spo2", 
		  "CollectionUnit": 0, 
		  "Data": {
			"BloodOxygen": '. $data['spo2'] /*rand(90,100)*/.', 
			"DataId": "c58dd5f85f9f4dc59baa06d17f9", 
			"HeartRate": '. rand(60,190) .', 
			"Lat": 0, 
			"Lon": 0, 
			"MeasureTime": "'.$day.'", 
			"Note": "", 
			"TimeZone": "+0000", 
			"time_zone": "+01:00", 
			"LastChangeTime": "2014-04-09 19:04:27", 
			"DataSource": "FromDevice"
		  }, 
		  "PushId": "a548d319997c47778a20a0b4fa6", 
		  "UserID": "6239f842dec4427f8c2c6dfd4b6"
		}';

		// var_dump($jsonBP, $jsonWeight, $jsonSpO2);
		// dd(json_decode($jsonBP), json_decode($jsonWeight), json_decode($jsonSpO2));

		$person = 17;

		IHealthController::createBP(json_decode($jsonBP,true), $person);
		IHealthController::createWeigth(json_decode($jsonWeight,true), $person);
		IHealthController::createSp02(json_decode($jsonSpO2,true), $person);

		return 'ok';

	}


}

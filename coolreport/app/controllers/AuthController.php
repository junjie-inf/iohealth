<?php

class AuthController extends BaseController {
	
	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('guest', array('except' => array('check', 'logout')));
		$this->beforeFilter('signup', array('only' => array('create', 'store')));
		$this->beforeFilter('ajax', array('on' => 'post'));
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Redirect::to('/');
		// return $this->layout('pages.auth.login');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return $this->layout('pages.auth.signup');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// Creamos el objeto
		$user = new User;
		
		// Fields
		$fields = Input::all();

		// Email to lower
		if (isset($fields['email'])) {
			$fields['email'] = strtolower($fields['email']);
		}

		// Validamos y asignamos campos
		$v = $user->parse( 'signup', $fields );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );

		}
		
		// Asociamos otras variables
		if($fields['password'] != $fields['password2']){
			return $this->json( array('status' => 'ERROR', 'messages' => array('contraseña' => 'Las contraseñas no son iguales')), 400 );
		}
		$user->password = Hash::make( $fields['password'] );
		
		// Asociamos relaciones externas
		$user->group()->associate( Group::find(1) ); // TODO: poner configurable en settings
		$user->role()->associate( Role::find(3) ); // TODO: poner configurable en settings

		if ( Input::has('twitter') )
		{
			$user->twitter = Input::get('twitter');
		}

		// Guardamos el modelo
		if( $user->save() && Auth::attempt( array('email' => $fields['email'], 'password' => $fields['password']), true )  )
		{
			// Eventos de signup
			Event::fire('auth.signup', array($user));
			
			// Redirect to intended
			$redirect = Session::get('url.intended', URL::route('landing'));

			// Forget intended
			Session::forget('url.intended');

			$report = new Report;

			$report->user()->associate( $user );
			$report->template()->associate( Template::find( 10 ) );
			//Extraer fecha principal
			if (!isset($template->settings->date))
				$newValue = null;
			else if ($template->settings->date == '_created_at')
				$newValue = DB::raw('now()');
			else
				$newValue = object_get($report->datum, $template->settings->date)->value;
			$report->main_date = $newValue;

			$datum = new stdClass();
			$datum->{'5889efb0944a90'} = (object)array('id' => '5889efb0944a90', 'value' => $user->getKey());	//ID
			$datum->{'5889efb0944a91'} = (object)array('id' => '5889efb0944a91', 'value' => $fields['firstname']);	//Name
			$datum->{'5889efb0944a92'} = (object)array('id' => '5889efb0944a92', 'value' => $fields['gender']);		//gender
			$datum->{'5889efb0944a95'} = (object)array('id' => '5889efb0944a95', 'value' => $fields['height']);		//height
			$datum->{'5889efb0944a96'} = (object)array('id' => '5889efb0944a96', 'value' => $fields['birth']);		//birth
			$datum->{'59143940683680'} = (object)array('id' => '59143940683680', 'value' => $fields['surname']);	//surname

			$report->datum = $datum;


			if( $report->save() ){

			Event::fire('report.store', $report);

				$person = $report->id;
			}
			
			// Respuesta OK
			return $this->json( array('status' => 'OK', 'person'=> $person, 'redirect' => $redirect), 200 );
		}

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function login()
	{
		// Creamos el objeto
		$user = new User;
		
		// Fields
		$fields = Input::all();

		// Email to lower
		$fields['email'] = strtolower($fields['email']);

		// Reglas de modelo
		$rules = $user->getRules('login');
		
		// Obtenemos los valores de los inputs usando las claves de las reglas
		$fields = sanitize( array_group( $rules, $fields ) );
		
		// Validamos los campos
		$v = Validator::make( $fields, $rules );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			//return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
			return $this->json( array('status' => 'ERROR', 'messages' => array('Error' => 'Su correo electrónico/contraseña son incorrectas.')), 403 );
		}
		
		// Logueamos al usuario
		if( Auth::attempt( array('email' => $fields['email'], 'password' => $fields['password']), true ) )
		{
			// Eventos de login
			Event::fire('auth.login');
			
			// Redirect to intended
			$redirect = Session::get('url.intended', URL::route('landing'));

			// Forget intended
			Session::forget('url.intended');

			$user = DB::select(DB::raw("SELECT id FROM reports WHERE template_id = 10 AND (datum->'5889efb0944a90'->>'value')::integer = ".Auth::user()->id." AND deleted_at IS NULL LIMIT 1"));

			if(!empty($user)){
				$user = $user[0]->id;
			}
			
			// Respuesta OK
			return $this->json( array('status' => 'OK', 'id' => Auth::user()->getKey(), 'person' => $user, 'redirect' => $redirect), 200 );
		}

		// Login incorrecto
		return $this->json( array('status' => 'ERROR', 'messages' => array('password' => 'Su correo electrónico/contraseña son incorrectas.')), 403 );
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function check()
	{
		return Response::make( '', (Auth::check() ? 200 : 403) );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function logout()
	{
		Auth::logout();
		return Redirect::to('/');
	}
}
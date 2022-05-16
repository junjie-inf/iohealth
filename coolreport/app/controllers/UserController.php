<?php

class UserController extends BaseController {

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
		$this->beforeFilter('permission:VIEW_USERS,user', array('only' => array('show')));
		$this->beforeFilter('permission:GRANT_USERS,user', array('only' => array('create', 'store', 'edit', 'update', 'destroy')));
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$permission = Auth::user()->getPermissionValue('VIEW_USERS');
		
		// Permisos del usuario
		if( $permission == 'ALL' )
		{
			$data = User::all();
		}
		else if( $permission == 'GROUP' )
		{
			$data = Auth::user()->group->users;
		}
		else if( $permission == 'OWN' )
		{
			$data = array( Auth::user() );
		}
		else
		{
			return App::abort(403, 'Forbidden');
		}
		
		return $this->layout('pages.user.index')
					->with('data', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$permission = Auth::user()->getPermissionValue('GRANT_USERS');

		if( $permission != 'ALL' && $permission != 'GROUP')
		{
			return App::abort(403, 'Forbidden');
		}

		return $this->layout('pages.user.create');
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
		
		// Validamos y asignamos campos
		$v = $user->parse( 'store', Input::all() );
		
		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}
		
		// Asociamos otras variables
		$user->password = Hash::make( Input::get('password') );
		
		// Asociamos relaciones externas
		$user->role()->associate( Role::find( Input::get('role_id') ) );
		$user->group()->associate( Group::find( Input::get('group_id') ) );

		if ( Input::has('twitter') )
		{
			$user->twitter = Input::get('twitter');
		}

		// Suponemos que el rol de admin siempre tiene ID 1
		if ( $user->role->id == 1 )
			$user->admin = true;

		// Guardamos el modelo
		if( $user->save() )
		{
			return $this->json( array(				
				'status' => 'OK', 
				'redirect' => URL::route('user.show', array( $user->id )), 
				'id' => $user->id
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
	public function show(User $user)
	{
		if( Request::ajax() )
		{
			return $this->json( array('status' => 'OK', 'data' => $user->datesForHumans()->toArray()), 200 );
		}
		
		return $this->layout('pages.user.show')
					->with('data', $user->datesForHumans());
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(User $user)
	{
		return $this->layout('pages.user.edit')
					->with('data', $user->datesForHumans());
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(User $user)
	{
		// Validamos y asignamos campos
		$v = $user->parse( 'update', Input::all() );
		$redir =  Input::has('redir') ? !(Input::get('redir')  === 'false') : true;
		$fields = Input::all();

		// En caso de error, reportamos
		if( $v->fails() )
		{
			return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
		}

		// Hack force checkbox
		if( ! Input::has('gossip') ) $user->gossip = false;

		// Asociamos otras variables
		if( Input::has('password') )
		{
			$user->password = Hash::make( Input::get('password') );
		}
		
		// Asociamos relaciones externas
		if(Input::has('role_id'))
			$user->role()->associate( Role::find( Input::get('role_id') ) );
		if(Input::has('group_id'))
			$user->group()->associate( Group::find( Input::get('group_id') ) );

		// Suponemos que el rol de admin siempre tiene ID 1
		if ( $user->role->id == 1 )
			$user->admin = true;
		else
			$user->admin = false;

		if ( Input::has('twitter') )
		{
			$user->twitter = Input::get('twitter');
		}

		// Guardamos el modelo
		if( $user->save() )
		{
			$report = DB::select(DB::raw("SELECT id FROM reports WHERE template_id = 10 AND (datum->'5889efb0944a90'->>'value')::integer = ".Auth::user()->id." AND deleted_at IS NULL LIMIT 1"));

			if(isset($report)){

				$report = Report::find($report[0]->id);

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

				$datum->{'5889efb0944a91'} = (object)array('id' => '5889efb0944a91', 'value' => isset($fields['firstname']) ? $fields['firstname'] :  $report->datum->{'5889efb0944a91'}->value );		//Name
				$datum->{'5889efb0944a92'} = (object)array('id' => '5889efb0944a92', 'value' => isset($fields['gender']) ? $fields['gender'] :  $report->datum->{'5889efb0944a92'}->value );	//gender
				$datum->{'5889efb0944a95'} = (object)array('id' => '5889efb0944a95', 'value' => isset($fields['height']) ? $fields['height'] :  $report->datum->{'5889efb0944a95'}->value );	//height
				$datum->{'5889efb0944a96'} = (object)array('id' => '5889efb0944a96', 'value' => isset($fields['birth']) ? $fields['birth'] :  $report->datum->{'5889efb0944a96'}->value );	//birth
				$datum->{'59143940683680'} = (object)array('id' => '59143940683680', 'value' => isset($fields['surname']) ? $fields['surname'] :  $report->datum->{'59143940683680'}->value );	//surname

				$report->datum = $datum;


				if( $report->save() ){
					Event::fire('report.store', $report);

					$person = $report->id;

				}
			}

			if($redir){
				return $this->json( array(				
					'status' => 'OK', 
					'person' => isset($person) ? $person : null,
					'redirect' => URL::route('user.show', array( $user->id )), 
				), 200 );
			}else{
				return $this->json( array(				
					'status' => 'OK'
				), 200 );
			}
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
	public function destroy(User $user)
	{
		if( Auth::user()->getKey() != $user->getKey() )
		{
			$report = DB::select(DB::raw("SELECT id FROM reports WHERE template_id = 10 AND (datum->'5889efb0944a90'->>'value')::integer = ".Auth::user()->id." AND deleted_at IS NULL LIMIT 1"));

			if(isset($report)){
				$report = Report::find($report[0]->id);
				$report->delete();
			}

			$user->delete();
			
			return $this->json( array('status' => 'OK'), 200 );
		}
		
		return $this->json( array('status' => 'FATAL'), 400 );
	}

}

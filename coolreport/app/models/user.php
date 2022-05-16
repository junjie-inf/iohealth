<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User extends Eloquent implements UserInterface, RemindableInterface {
	
	/**
	 * Trait followable.
	 */
	use Followable;

	/**
	 * Trait visitable.
	 */
	use Visitable;

	/**
	 * Guarded fields to avoid MassAssignementExceptions
	 * 
	 * @var type 
	 */
	protected $guarded = array('id', 'group_id', 'role_id', 'password', 'admin', 'twitter');
	
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * The attributes appended to the model's JSON form.
	 * 
	 * @var type 
	 */
	protected $appends = array('publicUrl', 'readable', 'editable', 'removable');
	
	/**
	 * Enable soft deletes.
	 * 
	 * @var type 
	 */
	use SoftDeletingTrait;
	
	protected $dates = ['deleted_at'];
	
	/**
	 * Reglas de validación de campos.
	 * 
	 * @var type 
	 */
	protected $rules = array(
		'login' => array(
			'email' => 'required|email|exists:users,email',
			'password' => 'required|min:6',
		),
		'signup' => array(
			'firstname' => 'required|max:50',
			'surname' => 'required|max:50',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:6',
		),
		'store' => array(
			'firstname' => 'required|max:50',
			'surname' => 'required|max:50',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:6',
		),
		'update' => array(
			'role_id' => 'exists:roles,id',
			'group_id' => 'exists:groups,id',
			'firstname' => 'required|max:50',
			'surname' => 'required|max:50',
			'email' => 'required|email',
			'password' => 'confirmed|min:6',
		),
		'profile' => array(
			'firstname' => 'required|max:50',
			'surname' => 'required|max:50',
			'email' => 'required|email',
			'password' => 'confirmed|min:6',
		),
	);
	
	/**
	 * Devuelve el grupo del usuario.
	 * 
	 * @return object Group
	 */
	public function group()
	{
		return $this->belongsTo('Group', 'group_id');
	}
	
	/**
	 * Devuelve el role del usuario.
	 * 
	 * @return object Role
	 */
	public function role()
	{
		return $this->belongsTo('Role', 'role_id');
	}
	
	/**
	 * Devuelve los reportes del usuario.
	 * 
	 * @return Collection Report
	 */
	public function reports()
	{
		return $this->hasMany('Report');
	}
	
	/**
	 * Devuelve los comentarios del usuario.
	 * 
	 * @return Collection Comment
	 */
	public function comments()
	{
		return $this->hasMany('Comment');
	}
	
	/**
	 * Devuelve los templates que ha creado el usuario.
	 * 
	 * @return Collection Template
	 */
	public function templates()
	{
		return $this->hasMany('Template');
	}
	
	/**
	 * Devuelve los usuarios que han visitado a este usuario.
	 * 
	 * @return Collection Visit
	 */
	public function visitors()
	{
		return $this->morphMany('Visit', 'visitable');
	}

	/** 
	 * Devuelve los visitables (reports, attachments, users) que ha visitado el usuario.
	 * 
	 * @return Collection Visit
	 */
	public function visited()
	{
		return $this->hasMany('Visit');
	}

	public function allVisits()
	{
		$visits = DB::select(DB::raw("SELECT COUNT(*) as c1 FROM (SELECT visitable_id FROM visits WHERE visitable_id LIKE '". $this->id ."' AND visitable_type LIKE 'User' UNION ALL
SELECT visitable_id FROM visits INNER JOIN reports ON visits.visitable_id = reports.id::varchar WHERE visitable_type LIKE 'Report' AND reports.user_id = '". $this->id ."') as s1;"))[0]->c1;

		return $visits;
	}

	/** 
	 * Devuelve los followables (groups, reports, roles, users) que ha seguido el usuario.
	 * 
	 * @return Collection Follow
	 */
	public function followed()
	{
		return $this->hasMany('Follow');
	}

	public function allFollowers()
	{
		$follows = DB::select(DB::raw("SELECT COUNT(*) as c1 FROM (SELECT followable_id FROM follows WHERE followable_id = ". $this->id ." AND followable_type LIKE 'User' UNION ALL
SELECT followable_id FROM follows INNER JOIN reports ON follows.followable_id = reports.id WHERE followable_type LIKE 'Report' AND reports.user_id = '". $this->id ."') as s1;"))[0]->c1;
		
		return $follows;
	}
	
	/**
	 * Devuelve los dashboards del usuario.
	 * 
	 * @return Collection Dashboards
	 */
	public function dashboards()
	{
		return $this->hasMany('Dashboard');
	}
	
	/**
	 * Devuelve los dash items del usuario.
	 * 
	 * @return Collection DashItems
	 */
	public function dashItems()
	{
		return $this->hasMany('DashItem');
	}

	/**
	 * Devuelve las alertas del usuario.
	 * 
	 * @return Collection Alerts
	 */
	public function alerts()
	{
		return $this->hasMany('Alert');
	}

	/**
	 * Devuelve los mapas del usuario.
	 * 
	 * @return Collection Maps
	 */
	public function maps()
	{
		return $this->hasMany('Map');
	}

	/**
	 * Devuelve los votos del usuario.
	 * 
	 * @return Collection Votes
	 */
	public function votes()
	{
		return $this->hasMany('Vote');
	}

	/**
	 * Devuelve los votos al usuario
	 * @return Collection Votes
	 */
	public function voters()
	{
		$positives = DB::select(DB::raw("SELECT COUNT(*) as c1 FROM votes inner join (select * from (select id, user_id from reports union all select id, user_id from comments) as union1 where user_id = $this->id) as s1 ON votes.votable_id = s1.id WHERE value = TRUE;"))[0]->c1;
		$negatives = DB::select(DB::raw("SELECT COUNT(*) as c1 FROM votes inner join (select * from (select id, user_id from reports union all select id, user_id from comments) as union1 where user_id = $this->id) as s1 ON votes.votable_id = s1.id WHERE value = FALSE;"))[0]->c1;

		return array($positives, $negatives);
	}

	/**
	 * Devuelve las badges de un usuario
	 * @return Collection Badges
	 */
	public function badges()
	{
		return $this->hasMany('Badge');
	}

	public function badgesOrdered()
	{
		return $this->hasMany('Badge')->orderBy('created_at', 'desc');
	}

	public function score()
	{
		return $this->hasOne('Score');
	}

	/**
	 * Atributo de URL del perfil público del usuario.
	 * 
	 * @return boolean
	 */
	public function getPublicUrlAttribute()
	{
		return $this->getUrl();
	}
	
	/**
	 * Atributo de lectura de objeto.
	 * 
	 * @return boolean
	 */
	public function getReadableAttribute()
	{
		return ( Auth::check() && ( Auth::user() == $this || Auth::user()->can('VIEW_USERS', $this) ) );
	}
	
	/**
	 * Atributo de modificación de objeto.
	 * 
	 * @return boolean
	 */
	public function getEditableAttribute()
	{
		return ( Auth::check() && ( Auth::user() == $this || Auth::user()->can('GRANT_USERS', $this) ) );
	}
	
	/**
	 * Atributo de eliminación de objeto.
	 * 
	 * @return boolean
	 */
	public function getRemovableAttribute()
	{
		return ( Auth::check() && Auth::user()->can('GRANT_USERS', $this) && Auth::user()->getKey() != $this->getKey() );
	}
	
	/**
	 * Comprobación de regla de email ya existente.
	 * 
	 * @param type $action
	 * @return type
	 */
	public function getEmailRule($action)
	{
		if( $action == 'update' )
		{
			return 'unique:users,email,' . $this->id;
		}
	}
	
	// -----------------

	/**
	 * Constantes de género.
	 */
	const MALE = 0;
	const FEMALE = 1;
	
	/**
	 * Devuelve el nombre completo del usuario.
	 * 
	 * @return string
	 */
	public function getFullname()
	{
		return trim( $this->firstname . ' ' . $this->surname );
	}

	public function getFullnameWithRep()
	{
		return trim( $this->firstname . ' ' . $this->surname . ' (K: '. $this->getReputation() .')');
	}
	
	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}
	
	/**
	 * Devuelve la URL del perfil público del usuario.
	 * 
	 * @return string
	 */
	public function getUrl()
	{
		return URL::to('profile') . '/'. $this->getKey() . '-' . Util::cleanForUrl( $this->getFullname() );
	}

	/**
	 * Devuelve el usuario de twitter
	 * 
	 * @return string
	 */
	public function getTwitter()
	{
		return $this->twitter;
	}
	
	/**
	 * Consulta si el usuario tiene un permiso.
	 * 
	 * @param String $permission
	 * @return boolean
	 */
	public function hasPermission( $permission )
	{
		return ( $this->admin || ! is_null( $this->role->permissions()->whereType($permission)->first() ) );
	}
	
	/**
	 * Devuelve el valor de un permiso.
	 * 
	 * @param String $permission
	 * @return boolean
	 */
	protected function getPermission( $permission )
	{
		return $this->role->permissions()->whereType($permission)->first();
	}
	
	/**
	 * Devuelve el valor del permiso.
	 * 
	 * @param type $permission
	 * @return type
	 */
	public function getPermissionValue( $permission )
	{
		// Si es superadmin
		if( $this->admin )
		{
			return 'ALL';
		}
		
		$permission = $this->getPermission($permission);
		
		return ( ! is_null( $permission ) ? $permission->value : 'NONE' );
	}
	
	/**
	 * Devuelve si el usuario tiene permisos para el objeto.
	 * 
	 * @param String $permission
	 * @param Eloquent $object
	 * @return boolean
	 */
	public function can( $permission, Eloquent $object = null )
	{
		$value = $this->getPermissionValue($permission);

		// Si existe el objeto.
		if( ! is_null($object) )
		{
			// Si tiene el valor para todo.
			if( $value == 'ALL' )
			{
				return true;
			}
			// Se pueden ver las plantillas en comun
			else if($permission == "VIEW_REPORTS" && $object instanceof Report && isset($object->template->settings->common) && $object->template->settings->common ){
				return true;
			}
			// Valor de grupo.
			else if( $value == 'GROUP' )
			{
				// modelos que tengan grupo. p.e. users
				if( ! is_null( $object->group ) && $this->group->getKey() == $object->group->getKey() )
				{
					return true;
				} 

				// modelos asignados a usuarios. p.e. reports, comments, ...
				else if( ! is_null( $object->user ) && $this->group->getKey() == $object->user->group->getKey() )
				{
					return true;
				}
				//Se pueden ver los reports en comun
				else if(! is_null( $object->user ) && $permission == 'VIEW_REPORTS' && $object->user->group->getKey() == 2){
					return true;
				}
				// modelo group
				else if( $object instanceof Group && $this->group->getKey() == $object->getKey() )
				{
					return true;
				}
				// modelo role (no estan directamente asignados ni a un usuario ni a un grupo específicos)
				else if( $object instanceof Role && in_array( $object->getKey(), Auth::user()->group->users()->lists('role_id') ) )
				{
					return true;
				}
			}
			// Si tiene el valor de propio.
			else if( $value == 'OWN' )
			{
				// modelos que tengan grupo. p.e. users
				if( $object instanceof User && $this->getKey() == $object->getKey()){
					return true;
				}
				// modelos asignados a usuarios. p.e. reports, comments, ...
				else if( ! is_null( $object->user ) && $this->getKey() == $object->user->getKey() )
				{
					return true;
				}	
			}
		}
		// Si el permiso existe, pero el objeto no, true.
		// NOTA: El objeto no existe cuando es un permiso de creación.
		else if( $this->hasPermission($permission) )
		{
			return true;
		}
			
		return false;
	}

	public function getRememberToken()
	{
	    return $this->remember_token;
	}

	public function setRememberToken($value)
	{
	    $this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
	    return 'remember_token';
	}

	/**
	 * Devuelve el calculo del karma del usuario
	 * @return reputation
	 */
	
	public function getReputation()
	{
		/**
		$media_visitas = DB::select(DB::raw("SELECT CAST((SELECT COUNT(*) FROM visits LEFT JOIN reports ON visits.visitable_id = reports.id::varchar WHERE visits.visitable_id LIKE '". $this->getKey() ."' OR reports.user_id = ". $this->getKey() .")AS float)/(CASE WHEN COUNT(*) = 0 THEN 1 ELSE COUNT(*) END) as c1 FROM visits;"))[0]->c1;

		$media_follows_reports = DB::select(DB::raw("SELECT COUNT(*) as c1 FROM follows INNER JOIN reports ON follows.followable_id = reports.id WHERE reports.user_id = ". $this->getKey() .";"))[0]->c1;

		$follows_usuario = DB::select(DB::raw("SELECT COUNT(*) as c1 FROM follows WHERE followable_id = ". $this->getKey() .";"))[0]->c1;

		$media_comentarios = DB::select(DB::raw("SELECT CAST((SELECT COUNT(*) FROM comments INNER JOIN reports ON comments.report_id = reports.id WHERE reports.user_id = ". $this->getKey() .")AS float)/(CASE WHEN COUNT(*) = 0 THEN 1 ELSE COUNT(*) END) as c1 FROM comments"))[0]->c1;

		$reports_creados = DB::select(DB::raw("SELECT COUNT(*) as c1 FROM reports WHERE user_id = ". $this->getKey() .";"))[0]->c1;

		$reputacion = $media_visitas * ($reports_creados / 10) + $media_visitas + $media_follows_reports + $follows_usuario + $media_comentarios;
		
		//dd($reputacion);

		return number_format($reputacion, 3, '.','');
		**/
		if (is_null($this->score))
		{
			$reputation = 0;
		}
		else
		{
			$reputation = floor($this->score->score);
		}

		return $reputation;
	}

	/**
	 * Devuelve si el usuario ha votado el report
	 * @param  int $report
	 * @return boolean
	 */

	public function voted($id, $type)
	{
		$vote = $this->votes()->whereVotableId($id)->whereVotableType($type)->first();

		$voteValue = true;

		if (is_null($vote) )
		{
			$voteValue = false;
		}

		return $voteValue;
	}
}
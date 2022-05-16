<?php

class VoteController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
		$this->beforeFilter('permission:VIEW_REPORTS,report,report_id');
		$this->beforeFilter('permission:ADD_VOTES,report,report_id');
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($votable)
	{ 
		// Get user
		$user = Auth::user();
		// Get type
		$type = ucfirst( get_class($votable) );

		$vote = $user->votes()->whereVotableId($votable->getKey())->whereVotableType($type)->first();

		if (is_null($vote) )
		{
			// Creamos el objeto
			$vote = new Vote();

			// Validamos y asignamos campos
			$v = $vote->parse( 'store', Input::all() );
					
			// En caso de error, reportamos
			if( $v->fails() )
			{
				return $this->json( array('status' => 'ERROR', 'messages' => $v->messages()->toArray()), 400 );
			}

			$vote->votable()->associate( $votable );
			$vote->user()->associate( Auth::user() );

			// Guardamos el modelo
			if( $vote->save() )
			{
				// Puntuación Karma
				$idUserVoted = ($vote->votable_type == "Comment") ? Comment::find($vote->votable_id)->user_id : Report::find($vote->votable_id)->user_id;
				$userVoted = User::find($idUserVoted);
				
		        $score = $userVoted->score;

		        if (is_null($score)){
		        	$score = new Score();	
		        }

		        $scoreVoter = $user->score;

		        $scoreActual = is_null($score->score) ? 0 : $score->score;
		        $scoreNew = $scoreActual + is_null($scoreVoter) ? 0 : $scoreVoter->score;

				if ($vote->value)
				{
					$score->score = ($scoreNew > $scoreActual) ? $scoreActual * 0.8 + $scoreNew * 0.2 : $scoreActual * 0.95 + $scoreNew * 0.05;
				}
				else
				{
					$score->score = ($scoreNew > $scoreActual) ? $scoreActual * 0.8 - $scoreNew * 0.2 : $scoreActual * 0.95 - $scoreNew * 0.05;

					if ($score->score < 0)
					{
						$score->score = 0;
					}
				}

				$score->user()->associate( $userVoted );

				$score->save();

				return $this->json( array(
					'status' => 'OK', 
					'id' => $vote->id,
					'votes' => array($votable->votes()->whereValue(true)->count(), $votable->votes()->whereValue(false)->count())
				), 200 );
			}
		}
		else
		{
			return $this->json( array(
					'status' => 'OK', 
					'votes' => array($votable->votes()->whereValue(true)->count(), $votable->votes()->whereValue(false)->count())
				), 200 );
		}	

		// Error chungo, no debería pasar nunca
		return $this->json( array('status' => 'FATAL'), 400 );
	}
}
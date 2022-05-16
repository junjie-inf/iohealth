<?php

use Carbon\Carbon;

trait Visitable {

	/**
	 * Añade un follow al modelo.
	 */
	public function visit($user = null)
	{		
		// Última visita registrada
		$last_visit = $this->visitors()->orderBy('created_at', 'desc')->first();
		
		$user = is_null($user) ? Auth::user() : $user;

		// Si la diferencia con la última visita es mayor que el intervalo mínimo
		if( is_null($last_visit) || 
			( ! is_null($last_visit) && ( $last_visit->created_at->diffInSeconds(Carbon::now()) > Config::get('local.stats.visit.delay') ) ) )
		{
			$visit = new Visit();

			$visit->user()->associate( $user );

			// Puntuación Karma
			$user = (get_class($this) == 'User') ? $this : User::find($this->user_id);
	        $score = $user->score;
	        if (is_null($score)){
	        	$score = new Score();	
	        }
			
			$score->score = $score->score + 0.1;
			$score->user()->associate( $user );

			$score->save();

			return $this->visitors()->save( $visit );
		}
	}

}
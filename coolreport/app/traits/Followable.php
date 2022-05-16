<?php

trait Followable {

	/**
	 * Devuelve los usuarios (Follow) que han seguido a este report.
	 * 
	 * @return Collection Follow
	 */
	public function followers()
	{
		return $this->morphMany('Follow', 'followable');
	}

	/**
	 * Devuelve los usuarios (User) que han seguido a este report.
	 * 
	 * @return Collection Follow
	 */
	public function getUsersFromFollowers()
	{
		return $this->followers->map(function($item) 
		{
			return $item->user;
		});
	}

	/**
	 * Follow al modelo.
	 */
	public function follow($user = null)
	{
		// Get user
		$user = is_null($user) ? Auth::user() : $user;	

		// Get type
		$type = ucfirst( get_class() );

		// Get follow
		$follow = $user->followed()->whereFollowableId($this->getKey())->whereFollowableType($type)->first();	

		// If follow not exists
		if( is_null($follow) )
		{
			$follow = new Follow();

			$follow->user()->associate( $user );

			$this->followers()->save( $follow );	

			// Gestion de badges de creación
			$follows = $follow->user->allFollowers();
			$badge = new Badge();
			$badge->user_id = ($follow->followable_type == "User") ? $follow->followable_id : Report::find($follow->followable_id)->user_id;

			if ($follows == 5)
			{	
				$badge->badge = 7;
			}
			else if ($follows == 50)
			{
				$badge->badge = 8;
			}
			else if ($follows == 100)
			{
				$badge->badge = 9;
			}

			if (!is_null($badge->badge))
			{
				$badge->save();
			}

			// Puntuación Karma
			$user = User::find($badge->user_id);
	        $score = $user->score;
	        
	        if (is_null($score)){
	        	$score = new Score();	
	        }
			
			$score->score = ($type == 'Report') ? $score->score + 0.5 : $score->score + 1;
			$score->user()->associate( $user );

			$score->save();
		}

		return $follow;
	}

	/**
	 * Unfollow al modelo.
	 */
	public function unfollow($user = null)
	{
		// Get user
		$user = is_null($user) ? Auth::user() : $user;	

		// Get type
		$type = ucfirst( get_class() );

		// Get follow
		$follow = $user->followed()->whereFollowableId($this->getKey())->whereFollowableType($type)->first();	

		// If follow not exists
		if( ! is_null($follow) )
		{
			$follow->delete();

			// Puntuación Karma
			$user = User::find(($follow->followable_type == "User") ? $follow->followable_id : Report::find($follow->followable_id)->user_id);
	        $score = $user->score;
	        
	        if (is_null($score)){
	        	$score = new Score();	
	        }
			
			$score->score = ($type == 'Report') ? $score->score - 0.5 : $score->score - 1;
			if ($score->score < 0)
			{
				$score->score = 0;
			}
			
			$score->user()->associate( $user );

			$score->save();
		}
	}

}
<?php

class CommentEventHandler {
	
	/*
	|--------------------------------------------------------------------------
	| Event handlers
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * store.
	 */
	public function commentSaved($comment)
	{
		/**
		// Hack @iDEA mail a todos los usuarios con role 2 (directora comecial)
		$users = Role::find(2)->users;

		// Hack @iDEA mail al dueño del report
		$users = $users->merge( array($comment->report->user) );

		// Hack @iDEA mail a todos los usuarios del hilo
		foreach( $comment->report->comments as $c )
		{
			$users = $users->merge( array($c->user) );
		}

		// Quitamos al usuario que ha escrito el comentario.
		$users = $users->filter(function($user) 
		{
			return Auth::user()->getKey() != $user->getKey();
		});

		mailToRole('comment.store', $comment, $users);
		**/

		// Gestion de badges de creación
		$createdComments = $comment->user->comments->count();
		$badge = new Badge();
		$badge->user_id = $comment->user_id;

		if ($createdComments == 10)
		{	
			$badge->badge = 4;
		}
		else if ($createdComments == 50)
		{
			$badge->badge = 5;
		}
		else if ($createdComments == 100)
		{
			$badge->badge = 6;
		}

		if (!is_null($badge->badge))
		{
			$badge->save();
		}
	}

	/**
	 * destroy.
	 */
	public function destroy($comment)
	{
		/**
		// Hack @iDEA mail a todos los usuarios con role 2 (directora comecial)
		$users = Role::find(2)->users;

		// Hack @iDEA mail al dueño del report (si no es él mismo)
		$users = $users->merge( array($comment->report->user) );

		// Quitamos al usuario que ha escrito el comentario.
		$users = $users->filter(function($user) 
		{
			return Auth::user()->getKey() != $user->getKey();
		});

		mailToRole('comment.destroy', $comment, $users);
		**/
	}

	
	/*
	|--------------------------------------------------------------------------
	| Event listeners
	|--------------------------------------------------------------------------
	*/
	
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
		$events->listen('eloquent.saved: Comment', 'CommentEventHandler@commentSaved');
		//$events->listen('comment.destroy', 'CommentEventHandler@destroy');
    }
}

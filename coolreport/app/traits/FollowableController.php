<?php

trait FollowableController {

	/**
	 * Follow / unfollow model.
	 * 
	 * @param  Eloquent $model
	 * @return string
	 */
	public function follow(Eloquent $model)
	{
		if( get_class($model) == 'User' && $model->id == Auth::user()->getKey() ) return; // Un usuario no se puede seguir a si mismo

		if( Input::has('follow') )
		{
			if( Input::get('follow') == 'true' )
			{
				$model->follow();

				return $this->json( array('status' => 'OK'), 200 );
			}
			else
			{
				$model->unfollow();

				return $this->json( array('status' => 'OK'), 200 );
			}
		}

		return $this->json( array('status' => 'ERROR'), 400 );
	}

}
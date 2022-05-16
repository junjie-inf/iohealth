<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	/**
	 * Create objects for url by id.
	 * 
	 * @param type $value
	 * @param type $route
	 * @return null
	 * @throws NotFoundHttpException
	 */
	public static function bind($value, $route)
	{
		// Get class name
		$class = preg_replace( '/^(.*)Controller$/', '$1', get_called_class() );
		
		// Pattern
		$pattern = '/^([0-9]+).*$/';
		
		if( ! is_null($value) && preg_match($pattern, $value) )
		{
			// Filter value 
			$value = preg_replace( $pattern, '$1', $value );

			// For model binders, we will attempt to retrieve the model using the find
			// method on the model instance. If we cannot retrieve the models we'll
			// throw a not found exception otherwise we will return the instance.
			if ( ! is_null($model = with(new $class)->find($value)))
			{
				return $model;
			}
		}

		throw new NotFoundHttpException;
	}
	
	/**
	 * Setup view layout.
	 * 
	 * @param type $view
	 * @return type
	 */
	protected function layout($view)
	{
		$data = array();
		
		if( Auth::check() )
		{
			//
		}
		
		return View::make($view)->with($data);
	}
	
	/**
	 * Setup json view (without caching).
	 * 
	 * @param type $data
	 * @param int $code
	 * @return type
	 */
	protected function json($data, $code)
	{
		$response = Response::json( $data, $code );
		
		$response->header('Cache-Control', 'max-age=0, no-cache, no-store, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Expires', 'Wed, 11 Jan 1984 05:00:00 GMT');
		
		return $response;
	}
}
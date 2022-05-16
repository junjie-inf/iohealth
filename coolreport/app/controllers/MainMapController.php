<?php

class MainMapController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if( Auth::check() )
		{
			// Array of latitude bounds
			$latitudes = array( Config::get('local.map.default.latitude') - 0.001, Config::get('local.map.default.latitude') + 0.001 );
			
			// Array of longitude bounds
			$longitudes = array( Config::get('local.map.default.longitude') - 0.001, Config::get('local.map.default.longitude') + 0.001 );
			
			// Permissions
			$permission = Auth::user()->getPermissionValue('VIEW_REPORTS');

			// Permisos del usuario
			if( $permission == 'ALL' )
			{
				$data = Report::query();
			}
			else if( $permission == 'GROUP' )
			{
				$data = Report::whereIn('user_id', Auth::user()->group->users()->lists('id'));
			}
			else if( $permission == 'OWN' )
			{
				$data = Auth::user()->reports();
			}
			$data->select(DB::raw('st_asgeojson(st_extent(main_geo::geometry)) as geo'));
			$data->whereNotNull('main_geo');
			$result = $data->get();

			if ($result[0]->geo)
			{
				$coordinates = $result[0]->geo->coordinates[0];
			}
			else
			{
				$coordinates = array(
					0 => array(0,35),
					2 => array(40,60)
				);
			}

			//DEBUG: España
			$latitudes = array($coordinates[0][1],$coordinates[2][1]);
			$longitudes = array($coordinates[0][0],$coordinates[2][0]);

			// Variable para la leyenda del mapa, incluye: id, titulo y 'visible' del settings
			$templates_legend = DB::table('templates')->select(array('id', 'title', DB::raw("COALESCE((settings->>'visible')::boolean, true) as visible")))->where(DB::raw("settings->>'geolocation'"), '=', 'true')->whereNull('deleted_at')->get();
			
			return View::make('pages.mainmap')
					->withLatitudes($latitudes)
					->withLongitudes($longitudes)
					->with('templates', $templates_legend);
		}

		return View::make('pages.landing');
	}
	
	/**
	 * Create marker with number
	 */
	public function marker()
	{
		$text   = Input::has('n') ? intval(Input::get('n')) : 'R';
		$im     = imagecreatefrompng( public_path('img/' . 'flecha_void.png') );
		$color  = imagecolorallocate($im, 255, 255, 255);
		$px     = (imagesx($im) - 16 * strlen($text) / 1.5) / 2;
		$font   = public_path('font/OpenSans-Regular.ttf');
		
		imagesavealpha($im, true);
		imagettftext($im, 22 - strlen($text) * 4, 0, $px, 26, $color, $font, $text);
		imagepng($im);
		imagedestroy($im);
		
		return Response::make(null, 200, array("Content-type" => "image/png"));
	}
}

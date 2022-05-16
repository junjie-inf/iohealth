<?php

use Carbon\Carbon;

class SharedObjectController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth', array('except' => array('show')));
	}

    /**
     * Generate a hash for a given object if it does not exist
     * and return its hash.
     *
     * @param str $type
     * @param int $id
     */
	public function share($type, $id)
	{
		/* return Redirect::action('sharedobj.show', $this->getHash($type, $id)); */
		return $this->getHash($type, $id);
    }

	/**
	 * Generate a CSV file for the given item.
	 * Theoretically, should only be used with DashItems.
	 *
	 * @param str $type
	 * @param int $id
	 *
	 * @return CSV
	 */
	public function share_csv($type, $id)
	{
		switch ($type)
		{
			case 'dashitem':
				$obj = DashItem::find($id);
				$cont = new DashItemController();
				break;
		}

		$fname = $obj->title.'.csv';
		$output = storage_path().'/tmp/'.$fname;

		// Remove file when served
		register_shutdown_function('unlink', $output);

		$fp = fopen($output,'w');

		// Obtain data
		list($header, $data) = $cont->query($obj->datum);

		// Add header data

		fputcsv($fp, $header, ';');

		// Append all data rows
		foreach ($data as $row)
			fputcsv($fp, $row, ';');

		return Response::download($output, $fname, ['Content-Type: text/cvs']);
	}

	/**
	 * Show a snapshot of a given element.
	 * This method generates a database record, but simplifies things greatly.
	 *
	 * @param str $type
	 * @param int $id
	 *
	 * @return Response
	 */
	public function share_img($type, $id)
	{
		$hash = $this->getHash($type, $id);
		$output = $this->generateImg($hash);

		// Remove image when served
		register_shutdown_function('unlink', $output);

		$response = Response::make(File::get($output), 200);
		$response->header(
			'Content-type',
			'image/png'
		);

		return $response;
	}

	/**
	 * Display a specific object from the given hash.
	 *
	 * @param string $hash
	 * @return Response
	 */
	public function show($objhash)
	{
		// Retrieve object from DB
		$obj = DB::table('shared_objects')->where('hash', $objhash)->first();

		if (!$obj || ($obj && !$obj->enabled))
			throw new NotFoundHttpException;

		// Find type
		switch ($obj->object_type)
		{
			case "dashboard":
				return $this->showDashboard($obj->object_id);
				break;

			case "dashitem":
				return $this->showDashItem($obj->object_id);
				break;

			default:
				// Should not reach here
				throw new NotFoundHttpException;
				break;
		}
	}

	/**
	 * Return an embedded dashboard view
	 *
	 * @param int $id Dashboard id
	 * @return Response
	 */
	/* public function showDashboard(Dashboard $dashboard) */
	public function showDashboard($id)
    {
        $dashboard = Dashboard::find($id);
		$renders = [];
		$dic = new DashItemController;
		foreach ($dashboard->datum->items as $row)
		{
			foreach ($row as $element)
			{
				$renders[$element->id] = $dic->render(DashItem::find($element->id)->datum);
			}
		}

		return $this->layout('pages.dashboard.show')
			->with('dashboard', $dashboard->datesForHumans())
			->with('renders', $renders)
			->with('embed', true);
	}

	/**
	 * Return an embedded dashitem view
	 *
	 * @param int $id DashItem id
	 * @return Response
	 */
	public function showDashItem($id)
	{
		$dashitem = DashItem::find($id);
		$dic = new DashItemController;
		$render = $dic->render($dashitem->datum);

		// Parameter is 'dashboard' in the view because of reasons
		return $this->layout('pages.dashitem.show')
					->with('dashboard', $dashitem->datesForHumans())
					->with('render', $render)
					->with('embed', true);
	}

	/**
	 * Toggle link for given hash
	 *
	 * @param str $type
	 * @param str $id
	 * @param int $val
	 *
	 * @return value indicating state
	 */
	public function toggle_link($type, $id, $val)
	{
		$obj = DB::table('shared_objects')
			->where(['object_type' => $type, 'object_id' => $id])->first();

		if (!$obj)
			throw new NotFoundHttpException;

		if ($val == 0)
		{
			DB::table('shared_objects')
				->where('id', $obj->id)
				->update(['enabled' => false]);

			return 0;
		}
		else
		{
			DB::table('shared_objects')
				->where('id', $obj->id)
				->update(['enabled' => true]);

			return 1;
		}
	}

	/** Check the database and create object if needed.
	 *
     * @param str $type
	 * @param int $id
	 *
	 * @return str $hash
     */
	private function getHash($type, $id)
	{
		$obj = DB::table('shared_objects')
			->where(['object_type' => $type, 'object_id' => $id])->first();

		if (!$obj)
		{
			// Generate hash
			$hash = $this->generateHash();
			while (DB::table('shared_objects')->where('hash', $hash)->first())
				$hash = $this->generateHash();

			$tstamp = Carbon::now()->toDateTimeString();
			// Create object
			DB::table('shared_objects')->insert([
				'hash' => $hash,
				'object_type' => $type,
				'object_id' => $id,
				'enabled' => false,
				'created_at' => $tstamp,
				'updated_at' => $tstamp
			]);

			return $hash;
		}

		// Enable sharing
		DB::table('shared_objects')
			->where('id', $obj->id)
			->update(['enabled' => true]);

		return $obj->hash;
	}

	/**
	 * Generate a random hash
	 *
	 * @return str $hash
	 */
	private function generateHash()
	{
		$salt = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		// Specify hash length, max is 32 (defined in database table)
		$hashLength = 6;
		$i = 0;
		$hash = '';
		while ($i < $hashLength)
		{
			$num = rand() % strlen($salt);
			$tmp = substr($salt, $num, 1);
			$hash = $hash . $tmp;
			$i++;
		}
		return $hash;
	}

	/**
	 * Obtain a PNG from a given page.
	 *
	 * Requires PhantomJS >= 2.0
	 *
	 * This code is extracted from https://github.com/spatie/browsershot/ and
	 * adapted to better suit the specific needs of CoolReport.
	 *
	 * The original Browsershot code is released under the MIT license and
	 * belongs to Spatie
	 *
	 * @param $hash public hash to render
	 *
	 * @return $output path of exported image
	 * @throws Exception
	 */
	private function generateImg($hash)
	{
		// PhantomJS should be located in vendor/bin
		// Will search for 'phantomjs' or 'phantomjs.exe'
		$vendor = base_path('vendor/bin/');

		if (file_exists($vendor.'phantomjs'))
			// UNIX
			$phantom = $vendor.'phantomjs';

		else if (file_exists($vendor.'phantomjs.exe'))
			// WINDOWS
			$phantom = $vendor.'phantomjs.exe';

		else
			// No binary found
			throw new Exception('PhantomJS not found');

		$url = action('sharedobj.show', $hash);

		// Output file
		$output = storage_path().'/tmp/'.$hash.'.png';

		// Create temporary file for JS
		$tempJsFileHandle = tmpfile();

		$fileContent = "
			var page = require('webpage').create();
			page.settings.javascriptEnabled = true;
			page.viewportSize = {
				height: document.body.scrollHeight,
				width: 1024
			};
			page.open('".$url."', function() {

				window.setTimeout(function() {
					page.render('".$output."');
					phantom.exit();
				}, 5000); // give phantomjs  5 seconds to process all js
			});
		";

		// Simple onLoad is not enough
		/* $fileContent = " */
		/* 	var page = require('webpage').create(); */
		/* 	page.settings.javascriptEnabled = true; */
		/* 	page.viewportSize = { */
		/* 		height: document.body.scrollHeight, */
		/* 		width: 1024 */
		/* 	}; */
		/* 	page.open('".$url."'); */
		/* 	page.onLoadFinished = function() { */
		/* 		page.render('".$output."'); */
		/* 		phantom.exit(); */
		/* 	}; */
		/* "; */

		fwrite($tempJsFileHandle, $fileContent);
		$tempFileName = stream_get_meta_data($tempJsFileHandle)['uri'];
		$cmd = escapeshellcmd("{$phantom} --ssl-protocol=any " . $tempFileName);

		shell_exec($cmd);

		fclose($tempJsFileHandle);

		if (!file_exists($output) || filesize($output) < 1024)
			throw new Exception('chould not create screenshot');

		return $output;
	}
}

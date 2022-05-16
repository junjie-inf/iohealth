<?php

class AttachmentController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
		$this->beforeFilter('permission:VIEW_REPORTS,report,report_id', array('only' => array('check', 'display', 'download')));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function display(Report $report, Attachment $attachment)
	{
		$response = Response::download( storage_path( Config::get('local.uploads.path') . '/' . $attachment->hash ), $attachment->filename );
		
		$response->headers->remove('Content-Disposition'); // La cabecera "Content-Disposition" fuerza la descarga del archivo.
		
		return $response;
	}
	
	/**
	 * Download the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function download(Report $report, Attachment $attachment)
	{
		$attachment->visit();
		
		$filename = $attachment->hash;
		
		// TODO: mejorar este doble foreach.
		foreach( $report->datum as $datum )
		{
			if( is_array($datum->value) )
			{
				foreach( $datum->value as $file )
				{
					if( is_object($file) && $attachment->hash == $file->hash )
					{
						$filename = $file->filename;
					}
				}
			}
		}
		
		return Response::download( storage_path( Config::get('local.uploads.path') . '/' . $attachment->hash ), $filename );
	}
}
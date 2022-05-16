<?php

class ScoreController extends BaseController {

	/**
	 * Controller filters.
	 */
	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->beforeFilter('ajax', array('on' => 'post'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{	
		$data = Score::all()->sortBy('score', null, true);

		return $this->layout('pages.score.index')->with('data', $data);
	}	
}
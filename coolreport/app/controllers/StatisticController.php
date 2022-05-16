<?php

use Nimbeo\Analytics\Data;
use Nimbeo\Util;

class StatisticController extends BaseController {

	/**
	 * Statistic repository.
	 *  
	 * @var type 
	 */
	protected $statistic;
	
	/**
	 * Controller filters.
	 */
	public function __construct(StatisticRepository $statistic)
	{
		$this->statistic = $statistic;
		
		$this->beforeFilter('auth');
		$this->beforeFilter('admin');
		$this->beforeFilter('ajax', array('on' => 'post'));
	}

	/**
	 * Dashboard page.
	 *
	 * @return Response
	 */
	public function index()
	{
		
		return $this->layout('pages.statistic')
				->withDiskUsage( Util::getDiskUsage() )
				->withTemplatesUsedPercent( Util::getTemplatesUsedPercent() );
	}
	
	/**
	 * Query response.
	 */
	public function query()
	{
		switch( Input::get('type') )
		{
			case 'reports':
			{
				$datum = $this->queryReports();
			} break;
			case 'templates':
			{
				$datum = $this->queryTemplates();
			} break;
			case 'templates-perc':
			{
				$datum = $this->queryTemplatesPerc();
			} break;
			case 'visits':
			{
				$datum = $this->queryVisits();
			} break;
			case 'map':
			{
				$datum = $this->queryMap();
			} break;
			default:
			{
				return $this->json( array('status' => 'FATAL'), 400 );
			}
		}
		
		return $this->json( array('status' => 'OK', 'data' => $datum ), 200 );
	}
	
	/**
	 * Generamos N series de datos según los templates y los reportes.
	 * 
	 * @return array
	 */
	protected function queryReports()
	{
		$datum = array();
		
		// Hacemos una serie de datos por cada template.
		foreach( Template::all() as $template )
		{
			$datum[] = with(new Data($template->title, $this->statistic->getReportsByTime($template->id)))->toArray();
		}
		
		return $datum;
	}
	
	/**
	 * Generamos una serie de datos con los porcentajes de reportes.
	 * 
	 * @return array
	 */
	protected function queryTemplates()
	{
		$data = array();

		// Creamos un conjunto de datos en una sola serie.
		foreach( Template::all() as $template )
		{
			$data[] = array( $template->title, $this->statistic->getReportsByTemplate($template) );
		}

		return array( with(new Data('Templates', $data))->toArray() );
	}
	
	/**
	 * Generamos una serie de datos con los porcentajes de reportes.
	 * 
	 * @return array
	 */
	protected function queryTemplatesPerc()
	{
		$data = array();

		// Creamos un conjunto de datos en una sola serie.
		foreach( Template::all() as $template )
		{
			$data[] = array( $template->title, $this->statistic->getTemplatePerc($template) );
		}

		return array( with(new Data('Templates usage (%)', $data))->toArray() );
	}
	
	/**
	 * Generamos una serie con el top 10 de visitas de reportes.
	 * 
	 * @return type
	 */
	protected function queryVisits()
	{
		$data = $this->statistic->getVisitsByReport(10);

		foreach( $data as & $result )
		{
			$report = Report::find($result->x);
			
			$result->x = ( ! is_null($report) ? $report->title : $result->x );
		}
		
		return array( with(new Data('Top 10 reports', $data))->toArray() );
	}

	/**
	 * Generamos un array con los reportes por comunidades.
	 * 
	 * @return type
	 */
	protected function queryMap()
	{
		$data = $this->statistic->getReportsByGeodata();

		$results = array();

		foreach( $data as $result )
		{
			$results[$result->name] = $result->count;
		}

		return $results;
	}
	
		/*
		// Top 10 reports most popular
		$topReportsIds = Visit::where('visitable_type', 'Report')
								->groupBy('visitable_id')
								->orderBy(DB::raw('COUNT(visits.id)'), 'DESC')
								->limit(10)
								->lists('visitable_id');
		
		$topReports = Report::find($topReportsIds); // Collection de Reports encontrados
		
		$topReportsJson = array();
		foreach( $topReports as $report )
		{
			$d = new stdClass();
			$d->x = '#' . $report->id;
			$d->y = $report->visitors->count();
			$d->title = $report->title;
			$d->url = URL::route('report.show', array($report->id));
			$topReportsJson[] = $d;
		}
		
		return $this->layout('pages.dashboard')
				->withReports( $this->getData('reports', 'month') )
				->withTemplates( $this->getData('templates', 'month') )
				->withComments( $this->getData('comments', 'month') )
				->withVotes( $this->getData('votes', 'month') )
				->withData( $this->getData('reports', 'day') )
				->withTopReports( $topReports )
				->withTopReportsJson( $topReportsJson );
	}*/
	
	/**
	 * Calculate stats.
	 * 
	 * @param type $table
	 * @param type $filter
	 * @return type
	 *//*
	protected function getData($table, $filter)
	{
		switch($filter)
		{
			case 'month':
				$date = array( 
					Carbon::createFromDate(null, 1, 1),
					Carbon::createFromDate(null, 12, 31),
				);
			break;
			
			case 'day':
				$date = array( 
					Carbon::createFromDate(null, null, 1),
					Carbon::createFromDate(null, null, Carbon::now()->daysInMonth),
				);
			break;
		}
		
		$data = DB::table($table)->select( DB::raw('date'), DB::raw('COUNT(' . $table . '.created_at) AS count'))
				->join( DB::raw('generate_series(\'' . $date[0]->toDateString() . '\', \'' . $date[1]->toDateString() . '\', \'1 ' . $filter . '\'::interval) date'), DB::raw('date'), '=', DB::raw('date_trunc(\'' . $filter . '\', ' . $table . '.created_at)'), 'RIGHT' )
				->groupBy( DB::raw('date') )
				->orderBy( DB::raw('date') )
				->get();

		return array_map(function( $row ) { return $row->count; }, $data);
	}*/
}
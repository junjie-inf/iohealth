<?php

use Carbon\Carbon;

class StatisticRepository {
	
	/**
	 * Filter time from.
	 * 
	 * @var type 
	 */
	private $from = null;
	
	/**
	 * Filter time to.
	 * 
	 * @var type 
	 */
	private $to = null;
	
	/**
	 * Temporal query. 
	 * 
	 * @var type 
	 */
	private $query = null;
	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if( Input::has('to') ) 
		{
			$this->to = Carbon::createFromTimeStamp(Input::get('to'))->addDay()->subSecond();
		}
		
		if( Input::has('from') )
		{
			$this->from = Carbon::createFromTimeStamp( Input::get('from') );
		}
	}
	
	protected function applyFilters()
	{
		if( ! is_null($this->to) && ! is_null($this->from) )
		{
			$this->query->whereBetween('created_at', array($this->from, $this->to));
		}
		$this->query->whereNull('deleted_at');
	}

	protected function applyFiltersVisits()
	{
		if( ! is_null($this->to) && ! is_null($this->from) )
		{
			$this->query->whereBetween('created_at', array($this->from, $this->to));
		}
	}
	
	/**
	 * Get all reports by time
	 * 
	 * @return type
	 */
	public function getReportsByTime($template_id = null)
	{
		$filter = Input::get('filter');

		// Filter filter :D
		switch( $filter )
		{
			case 'year':
				$from = $this->from->copy()->startOfYear(); 
				$to = $this->to->copy()->endOfYear(); 
				break;
			case 'month':
				$from = $this->from->copy()->startOfMonth(); 
				$to = $this->to->copy()->endOfMonth();
				break; 
			case 'week':
				$from = $this->from->copy()->startOfWeek(); 
				$to = $this->to->copy()->endOfWeek();
				break; 
			case 'day':
				$from = $this->from->copy(); 
				$to = $this->to->copy();
				break;
			case 'hour':
				$from = $this->from->copy()->startOfDay(); 
				$to = $this->to->copy()->endOfDay()->addHour();
				break;
			default:
				$filter = 'day';
		}

		$this->query = DB::table(DB::raw('generate_series(\'' . $from->toDateString() . '\', \'' . $to->toDateString() . '\', \'1 ' . $filter . '\'::interval) x'))
				->select(DB::raw('EXTRACT(EPOCH FROM x) * 1000 as x, COUNT(id) as y'))
				->leftJoin('reports', function($join) use ($template_id, $filter)
		        {
		            $join->on(DB::raw('date_trunc(\'' . $filter . '\', x)'), '=', DB::raw('date_trunc(\'' . $filter . '\', created_at)'));
		            $join->on('deleted_at', 'is', DB::raw('null'));
		            if( ! is_null($template_id) )
		            {
		            	$join->where('template_id', '=', $template_id);
		            }
		        })
				->groupBy( DB::raw('x') )
				->orderBy( DB::raw('x') );

		return $this->query->get();
	}
	
	/**
	 * Get report count by template.
	 * 
	 * @param Template $template
	 * @return integer
	 */
	public function getReportsByTemplate($template)
	{
		$this->query = $template->reports();
		
		$this->applyFilters();
		
		return $this->query->count();
	}
	
	/**
	 * Get usage percent of a template.
	 * 
	 * @param Template $template
	 * @return integer
	 */
	public function getTemplatePerc($template)
	{
		$this->query = $template->reports();
		$this->applyFilters();
		$reports_this = $this->query->count();

		$this->query = DB::table('reports');
		$this->applyFilters();
		$reports_all = $this->query->count();
		

		return ( $reports_this * 100 ) / $reports_all;
	}
	
	/**
	 * Get reports top N by visits.
	 * 
	 * @return type
	 */
	public function getVisitsByReport($limit = null)
	{
		$this->query = DB::table('visits')
				->where('visitable_type', 'Report')
				->groupBy('visitable_id')
				->orderBy(DB::raw('COUNT(visits.id)'), 'DESC')
				->select(DB::raw('visitable_id as x, COUNT(id) as y'));
		
		if( ! is_null($limit) )
		{
			$this->query->limit($limit);
		}
		
		$this->applyFiltersVisits();
		
		return $this->query->get();
	}
	
	/**
	 * Get report count by geodata.
	 * 
	 * @return type
	 */
	public function getReportsByGeodata()
	{
		$this->query = DB::table('reports')
				->join(DB::raw('(SELECT id, jsonb_array_elements(geodata->\'address_components\') as loc FROM reports) subquery'), function($join)
		        {
		            $join->on('reports.id', '=', 'subquery.id');
		        })
				->select(DB::raw('loc->>\'short_name\' as name, COUNT(*) as count'))
				->where(DB::raw('loc#>>\'{types,0}\''), '=', 'administrative_area_level_1')
				->whereNull('deleted_at')
				->groupBy(DB::raw('loc->>\'short_name\''));

		$this->applyFilters();

		return $this->query->get();
	}


	
}

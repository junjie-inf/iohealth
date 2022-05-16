<?php

use Nimbeo\QueryGenerator;

class ReportEventHandler {
	
	public function createEditReport($action, $data, $srcReport, $new)
	{
		if ($new) {
			$report = new Report();
		}else{
			$report = $srcReport;
		}
		$report->user_id = $data->user_id;
		$report->template_id = $action->template;
		
		$datum = new stdClass();
		foreach($action->fields as $field)
		{
			$value = $field->value;

			if (! is_array($value)) {
				preg_match_all('/\$(\d+)\.([0-9a-fA-F]+|_[a-zA-Z]+)/', $value, $matches, PREG_SET_ORDER);
			}else{
				foreach($value as $v){
					preg_match_all('/\$(\d+)\.([0-9a-fA-F]+|_[a-zA-Z]+)/', $v, $m, PREG_SET_ORDER);
					$matches = array_merge($matches, $m);
				}
			}
			
			foreach($matches as $match)
			{
				$val = '';
				if ($match[2][0] == '_')
				{
					switch ($match[2])
					{
						case '_id':
							$val = $srcReport->id;
							break;
						case '_title':
							$val = $srcReport->title;
							break;
						case '_address':
							$val = $srcReport->geodata->formatted_address;
							break;
						case '_date':
							$val = $srcReport->main_date;
							break;
						default:
							break;
					}
				}
				else
				{
					if (isset($srcReport->datum->{$match[2]})) {
						$val = $srcReport->datum->{$match[2]}->value;
					}					
				}

				if (!is_array($val)) {
					$value = str_replace($match[0], $val, $value);
				}else{
					$value = array_merge($value, $val);
				}
			}

			// Realizar las operaciones
			if (! is_array($value)) {
				preg_match_all('/\[[+\-\/\*\d.() ]+\]/', $value, $matches, PREG_SET_ORDER);
			}else{
				foreach($value as $v){
					preg_match_all('/\$(\d+)\.([0-9a-fA-F]+|_[a-zA-Z]+)/', $v, $m, PREG_SET_ORDER);
					$matches = array_merge($matches, $m);
				}
			}

			foreach($matches as $match)
			{
				$str = substr($match[0],1,-1);
				$res = eval("return ".$str.";");
				$value = str_replace($match[0], $res, $value);
			}
			
			if (!empty($value)) {
				$datum->{$field->id} = (object)array('id' => $field->id, 'value' => $value);
			}
		}

		$report->datum = $datum;
		
		//FIXME
		$report->main_geo = $srcReport->main_geo;
		$report->geodata = $srcReport->geodata;
		

		if ($new) {
			//Extraer fecha principal
			$template = Template::find($action->template);
			if (!isset($template->settings->date))
				$newValue = null;
			else if ($template->settings->date == '_created_at')
				$newValue = DB::raw('now()');
			else
				$newValue = object_get($report->datum, $template->settings->date)->value;

		}else{
			$newValue = $srcReport->main_date;
		}

		$report->main_date = $newValue;

		$report->save();
	}
	
	/*
	|--------------------------------------------------------------------------
	| Event handlers
	|--------------------------------------------------------------------------
	*/
	public function reportSaved($report)
	{
		// ALERT CHECK
		$alerts = Alert::where(Db::raw("conditions->>'template'"), '=', $report->template_id)->get();

		$queryGen = new QueryGenerator();
		$queryGen->addTemplate($report->template_id);
		$queryGen->addWhere('$'.$report->template_id.'._id = '.$report->id);
		
		$conditions = [];
		foreach ($alerts as $a)
		{
			$cond = $a->conditions->condition;
			$queryGen->addSelectExpression($cond, 'a'.$a->id);
		}
		
		$sql = $queryGen->toSql();
		
		$result = Db::select(Db::raw($sql));
		
		foreach($result[0] as $alert=>$col)
		{
			if ($col)
			{
				$alertId = intval(substr($alert,1));
				$alert = $alerts->find($alertId);
				
				foreach($alert->actions as $action)
				{
					switch($action->type)
					{
						case 'report':
							$this->createEditReport($action, $alert, $report, true);
							break;
						default:
							break;
					}
				}
			}
		}
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
		$events->listen('eloquent.saved: Report', 'ReportEventHandler@reportSaved');
	}
}

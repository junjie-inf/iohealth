@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			<div class="box">
				<div class="box-header">
					<h2><i class="icon-bar-chart"></i>{{ trans('statistics.adminshortcuts') }}</h2>
				</div>
				<div class="box-content">
					<div class="col-sm-3">
						<a class="quick-button" href="{{ URL::to('report') }}">
							<i class="icon-copy"></i>
							<p>{{ trans('statistics.reports') }}</p>
							<span class="notification blue">{{ Report::count() }}</span>
						</a>
					</div>
					<div class="col-sm-3">
						<a class="quick-button" href="{{ URL::to('template') }}">
							<i class="icon-file-text-alt"></i>
							<p>{{ trans('statistics.templates') }}</p>
							<span class="notification orange">{{ Template::count() }} ({{ $templates_used_percent}}% {{ trans('statistics.used') }})</span>
						</a>
					</div>
					<div class="col-sm-3">
						<a class="quick-button" href="{{ URL::to('user') }}">
							<i class="icon-user"></i>
							<p>{{ trans('statistics.users') }}</p>
							<span class="notification green">{{ User::count() }}</span>
						</a>
					</div>
					<div class="col-sm-3">
						<a class="quick-button" href="{{ URL::to('group') }}">
							<i class="icon-group"></i>
							<p>{{ trans('statistics.groups') }}</p>
							<span class="notification red">{{ Group::count() }}</span>
						</a>
					</div>
					<div class="clearfix"></div>
				</div>	
			</div>	
		</div><!--/col-->

	</div><!--/row-->

	<div class="row">

		<div class="col-lg-12">
			<div class="box">
				<div class="box-header">
					<h2><i class="icon-bar-chart"></i>{{ trans('statistics.reportcount') }}</h2>
				</div>
				
				<div class="box-content">
		
					<style>
					#chart-reports svg {
					  height: 400px;
					}
					</style>	
					
					<div id="chart-reports"><svg></svg></div>
					
					<div>
						<select id="select-filter">
							<option value="hour">{{ trans('time.mhour') }}</option>
							<option value="day" selected>{{ trans('time.mday') }}</option>
							<option value="week">{{ trans('time.mweek') }}</option>
							<option value="month">{{ trans('time.mmonth') }}</option>
							<option value="year">{{ trans('time.myear') }}</option>
						</select>
					</div>

					<div class="clearfix"></div>
				</div>
			</div>	
		</div><!--/col-->
		
	</div><!--/row-->
	
	<div class="row">

		<div class="col-lg-4">
			<div class="box">
				<div class="box-header">
					<h2><i class="icon-bar-chart"></i>{{ trans('statistics.templatesusagereports') }}</h2>
				</div>
				
				<div class="box-content">
					<style>#chart-templates svg{ height: 300px; }</style>
					
					<div id="chart-templates"><svg></svg></div>
					
					<div class="clearfix"></div>
				</div>
			</div>
		</div><!--/col-->

		<div class="col-lg-4">
			<div class="box">
				<div class="box-header">
					<h2><i class="icon-bar-chart"></i>{{ trans('statistics.templatesusageper') }}</h2>
				</div>
				
				<div class="box-content">
					<style>#chart-templates-perc svg{ height: 300px; }</style>
					
					<div id="chart-templates-perc"><svg></svg></div>
					
					<div class="clearfix"></div>
				</div>
			</div>
		</div><!--/col-->
		
		<div class="col-lg-4">
			<div class="box">
				<div class="box-header">
					<h2><i class="icon-dashboard"></i>{{ trans('statistics.meters') }}</h2>
				</div>
				
				<div class="box-content">
		
					<div class="col-md-5 col-sm-3 col-md-offset-3 col-sm-offset-3" style="height: 300px;padding-top:50px">
						<div class="text-center">
							<h2 class="text-center inh_bold text-muted">{{ trans('statistics.harddrive') }}</h2>
						</div>
						<div class="circleStatsItem blue">
							<i class="icon-hdd"></i>
							<span class="plus">+</span>
							<span class="percent">%</span>
							<input type="text" value="{{ $disk_usage }}" class="circleChart" />
						</div>
					</div>
					
					<div class="clearfix"></div>
				</div>
			</div>	
		</div><!--/col-->
		
	</div><!--/row-->
	
	<div class="row">

		<div class="col-lg-12">
			<div class="box">
				<div class="box-header">
					<h2><i class="icon-bar-chart"></i>{{ trans('statistics.topreports') }}</h2>
				</div>
				
				<div class="box-content">
		
					<style>
					#chart-visits svg {
					  height: 400px;
					}
					</style>	
					
					<div id="chart-visits"><svg></svg></div>
					
					<div class="clearfix"></div>
				</div>
			</div>	
		</div><!--/col-->
		
	</div><!--/row-->
	
	<div class="row">
		<style>
		.jvectormap-zoomin, .jvectormap-zoomout {
			position: absolute;
			background: #292929;
			padding: 4px;
			width: 22px;
			height: 22px;
			cursor: pointer;
			line-height: 10px;
			text-align: center;
			font-size: 14px;
			border-radius: 2px;
			box-shadow: inset 0 -2px 0 rgba(0, 0, 0, 0.05);
			-webkit-box-shadow: inset 0 -2px 0 rgba(0, 0, 0, 0.05);
			background-color: #FFF;
			border: 1px solid #BFBFBF;
			color: #333;
		}
		</style>
		<div class="col-md-12">
				<div class="box">
					<div class="box-header">
						<h2><i class="icon-th"></i>{{ trans('statistics.maps') }}</h2>
					</div>
					<div class="box-content">
						<div id="spain-map" data-name="es_mill_en" style="width: 600px; height: 400px; margin: 0 auto"></div>
					</div>
				</div>
			</div><!--/col-->
	</div><!--/row-->
		
@stop


@section('specific-javascript-plugins')
@stop


@section('custom-javascript')
<script>
var chart_reports, chart_templates, chart_templates_perc, chart_visits;

var jsonReport = function() {
	d3.json( $SITE_PATH + "statistic/query?type=reports&filter=" + $('#select-filter').val() + "&from=" + CoolReport.start.format('X') + '&to=' + CoolReport.end.format('X') , function(response) {
		d3.select('#chart-reports svg')
		  .datum(response.data)
		  .transition().duration(500)
		  .call(chart_reports);
	});
};

var datecallback = function()
{
	// hack para borrar los datos si no hay datum
	/*if( response.data.length === 0 || response.data[0].values.length === 0 ){
			d3.select('#chart svg').select('.nvd3').remove();
		}else{*/
	
	jsonReport();
	
	d3.json( $SITE_PATH + "statistic/query?type=templates&from=" + CoolReport.start.format('X') + '&to=' + CoolReport.end.format('X') , function(response) {
		d3.select("#chart-templates svg")
			.datum(response.data[0].values)
			.transition().duration(1200)
			.call(chart_templates);
	});
	
	d3.json( $SITE_PATH + "statistic/query?type=templates-perc&from=" + CoolReport.start.format('X') + '&to=' + CoolReport.end.format('X') , function(response) {
		d3.select("#chart-templates-perc svg")
			.datum(response.data[0].values)
			.transition().duration(1200)
			.call(chart_templates_perc);
	});
	
	d3.json( $SITE_PATH + "statistic/query?type=visits&from=" + CoolReport.start.format('X') + '&to=' + CoolReport.end.format('X') , function(response) {
		d3.select("#chart-visits svg")
			.datum(response.data)
			.transition().duration(1200)
			.call(chart_visits);
	});

	$.getJSON( $SITE_PATH + "statistic/query?type=map&from=" + CoolReport.start.format('X') + '&to=' + CoolReport.end.format('X') , function(response) {
		data_array = response.data;
		$('#spain-map').vectorMap('get', 'mapObject').series.regions[0].clear();
		$('#spain-map').vectorMap('get', 'mapObject').series.regions[0].params.min = null;
		$('#spain-map').vectorMap('get', 'mapObject').series.regions[0].params.max = null;
		$('#spain-map').vectorMap('get', 'mapObject').series.regions[0].setValues( data_array );
		
	});
	
};

function formatDate(d) {

	var format = '%B %e, %Y';

	switch( $('#select-filter').val() ) {
		case 'hour':
			format = '%H:%M %B %e';
			break;
		case 'day':
			format = '%B %e';
			break;
		case 'week':
			format = 'Week %e %B, %Y';
			break;
		case 'month':
			format = '%B, %Y';
			break;
		case 'year':
			format = '%Y';
			break;
	}

	return d3.time.format(format)(new Date(parseInt(d)));
}

$(document).on('change', '#select-filter', function() {
	jsonReport();
});

$(document).ready(function(){

	/* ---------- Init jQuery Knob ---------- */
	$('.circleChart').each(function(){
		var $this = $(this);
		$this.knob({
			'min':0,
			'max':100,
			'readOnly': true,
			'width': 120,
			'height': 120,
			'fgColor': $this.parent().css('color'),
			'dynamicDraw': true,
			'thickness': 0.2,
			'tickColorizeValues': true,
			'skin':'tron'
		});
	});

	nv.addGraph(function() {
		chart_reports = nv.models.multiBarChart();

		chart_reports.xAxis
			.showMaxMin(false)
			.tickFormat(formatDate);

		chart_reports.yAxis
			.tickFormat(d3.format(',.0f'));

		nv.utils.windowResize(chart_reports.update);

		return chart_reports;
	});
		
	nv.addGraph(function() {
		chart_templates = nv.models.pieChart().showLabels(true);

		return chart_templates;
	});
		
	nv.addGraph(function() {
		chart_templates_perc = nv.models.pieChart().showLabels(true);

		return chart_templates_perc;
	});
	
	nv.addGraph(function() {
		chart_visits = nv.models.multiBarHorizontalChart()
			.margin({top: 30, right: 20, bottom: 50, left: 175})
			.showValues(true)
			.tooltips(false)
			.showControls(false);

		chart_visits.yAxis
			.tickFormat(d3.format(',.2f'));

		nv.utils.windowResize(chart_visits.update);

		return chart_visits;
	});

	/*
	 * VECTOR MAP
	 */

	data_array = {};

	var map_name = $('#spain-map').attr('data-name');

	$('#spain-map').vectorMap({
		map: map_name,
		backgroundColor: 'transparent',
		regionStyle: {
			initial: {
				fill: '#bce8f0',
				"fill-opacity": 0.8
			},
			hover: {
				stroke: '#126892',
				"stroke-width": 2,
				"fill-opacity": 1
			}
		},
	    series: {
	        regions: [{
	            values: data_array,
	            scale: ['#8CCFF0', '#126892'],
	            normalizeFunction: 'polynomial'
	        }]
	    },
		onRegionLabelShow: function (e, el, code) {
	        if (typeof data_array[code] == 'undefined') {
	            e.preventDefault();
	        } else {
	            var countrylbl = data_array[code];
	            el.html(el.html() + ': ' + countrylbl);
	        }
	    }
	});

});
</script>
@stop

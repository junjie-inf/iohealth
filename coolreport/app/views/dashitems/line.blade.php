<div id='chart{{ $chartId }}'>
  @if ( isset($options->dFilter) )
    <select id='filter{{ $chartId }}'>
        <option value="-1">All</option>
        @foreach ( $dFilterValues as $v )
            <option value="{{$v}}">{{$v}}</option>
        @endforeach
    </select>
  @endif
  <svg style="height: 500px;"> </svg>
</div>

<script>
crHooks.push(function()
{
    @if ( isset($options->dFilter) )
        var datum = {{json_encode($datum)}};
        var filterSelect = $('#filter{{ $chartId }}');
        
        filterSelect.on('change', function() {
            var filterVal = filterSelect.val();
            crHooks = [];
            if (filterVal != -1)
                datum.data.filter = "${{ $options->dFilter->template }}.{{ $options->dFilter->field }} = '" + filterVal + "'";
            
            $.post($SITE_PATH + '/dashitem/preview', {'d': JSON.stringify(datum)}, function(d){
                var newHtml = $(d);
                $('#chart{{ $chartId }}').replaceWith(newHtml);
                for (var i = 0; i < crHooks.length; i++)
                {
                    crHooks[i]();
                }
                newHtml.find('select').val(filterVal);
            });
        });
    @endif
    
	var dateformat; 

	var lineData = [
		@foreach ( $options->series as $s )
		{
			'key': '{{ $s->title }}',
			'color': '{{ $s->color }}',
			'area': {{ $s->area ? 'true' : 'false' }},
			'values': [
				@foreach ( $data as $row )
					@if ( $row[$options->axis->x->data] && $row[$s->data] !== null)
						{
							x:
								@if ($options->axis->x->datatype == 'date')
									new Date('{{ $row[$options->axis->x->data] }}'.replace(' ','T')),
								@else
									{{ $row[$options->axis->x->data] }},
								@endif
							y:
								{{ $row[$s->data] }}
						},
					@endif
				@endforeach
			]
		},
		@endforeach
	];
	
	nv.addGraph(function() {
		var chart = nv.models.lineChart()
				.margin({left: 100})  //Adjust chart margins to give the x-axis some breathing room.
				.useInteractiveGuideline(true)  //We want nice looking tooltips and a guideline!
				.transitionDuration(350)  //how fast do you want the lines to transition?
				.showLegend(true)       //Show the legend, allowing users to turn on/off line series.
				.showYAxis(true)        //Show the y-axis
				.showXAxis(true)        //Show the x-axis
		;

		chart.xAxis.axisLabel('{{ $options->axis->x->label }}');
		
		@if ($options->axis->x->datatype == 'date')

		switch( '{{ $xCol->datetype }}' )
		{
			case 'hour':
				dateformat = '%H %e';
				break;
			case 'day':
				dateformat = '%d %b';
				break;
			case 'week':
				dateformat = '%W %b';
				break;
			case 'month':
				dateformat = '%b %Y';
				break;
			case 'year':
				dateformat = '%Y';
				break;
			default:
				dateformat = '%d %b %y';
				break;
		}

		chart.xAxis.tickFormat(function(dd) {
			return d3.time.format(dateformat)(new Date(dd));
		});
		@endif

		chart.yAxis     //Chart y-axis settings
		.axisLabel('{{ $options->axis->y->label }}');

		/* Done setting the chart up? Time to render it!*/
		d3.select('#chart{{ $chartId }} svg')
		.datum(lineData)
		.call(chart);

		//Update the chart when window resizes.
		nv.utils.windowResize(function() { chart.update() });
			return chart;
		});
	});
</script>
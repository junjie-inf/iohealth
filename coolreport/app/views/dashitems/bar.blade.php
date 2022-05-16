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

	var barData = [
		@foreach ( $options->series as $s )
		{
			'key': '{{ $s->title }}',
			'color': '{{ $s->color }}',
			'values': [
				@foreach ( $data as $row )
					@if ( $row[$options->axis->x->data] && $row[$s->data] !== null)
						{
							x:
								@if ($options->axis->x->datatype == 'date')
									new Date('{{ $row[$options->axis->x->data] }}'.replace(' ','T')),
								@else
									"{{ $row[$options->axis->x->data] }}",
								@endif
							y:
								parseInt('{{ $row[$s->data] }}')
						},
					@endif
				@endforeach
			]
		},
		@endforeach
	];

	nv.addGraph(function() {
		var chart = nv.models.multiBarChart()
			.reduceXTicks(false)   //If 'false', every single x-axis tick label will be rendered.
			// TO-DO controlar labels en multiples casos para mejorar la visualizacion
			.rotateLabels(-45)      //Angle to rotate x-axis labels.
			.showControls(true)   //Allow user to switch between 'Grouped' and 'Stacked' mode.
			.groupSpacing(0.1)    //Distance between each group of bars.
		;

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

		chart.xAxis.axisLabel('{{ $options->axis->x->label }}');
		console.log({{json_encode($options)}});
		// TO-DO controlar el margin en multiples casos para mejorar la visualizacion
		chart.margin({bottom: 125});

		chart.yAxis
			.axisLabel('{{ $options->axis->y->label }}')
		    .tickFormat(d3.format(',.1f'));

		d3.select('#chart{{ $chartId }} svg')
		    .datum(barData)
		    .transition().duration(500)
		    .call(chart)
		    ;

		nv.utils.windowResize(chart.update);

		return chart;
	});
});
</script>

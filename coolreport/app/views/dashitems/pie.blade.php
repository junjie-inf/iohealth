<div id='chart{{ $chartId }}'>
  <svg style="height: 500px;"> </svg>
</div>

<script>
crHooks.push(function()
{
	var pieData = {{ json_encode($data) }};
	
	nv.addGraph(function() {
	var chart = nv.models.pieChart()
	.x(function(d) { return d.l })
	.y(function(d) { return d.v })
	.showLabels(true);

	d3.select("#chart{{ $chartId }} svg")
		.datum(pieData)
		.transition().duration(350)
		.call(chart);

	return chart;
	});
});
</script>
<div id='chart{{ $chartId }}'>
</div>

<script>
crHooks.push(function()
{
	var indicatorData = {{ json_encode($data) }};
    var i;

    for (i = 0; i < indicatorData.length; i++)
	{
		// Get pie
		var pieData = indicatorData[i].pie;
		var pieLabel = indicatorData[i].label;

		// Add new svg element
		var newId = "chart{{ $chartId }}_pie_" + i;
		d3.select('#chart{{ $chartId }}')
			.append("svg")
			.attr("id", newId)
			.attr("height", 275)
			.attr("class", "col-4");

		// Show graph

		nv.addGraph(function() {
			var chart = nv.models.pieChart()
				.x(function(d) { return d.label })
				.y(function(d) { return d.value })
				.showLabels(false);

			d3.select('#' + newId)
				.datum(pieData)
				.transition().duration(350)
				.call(chart);

			d3.select('#' + newId)
				.append("text")
					.attr("x", 60)
					.attr("y", 40)
					.attr("text-anchor", "middle")
					.text(pieLabel);

			nv.utils.windowResize(function() { chart.update() });

			return chart;
		});
	}
});
</script>

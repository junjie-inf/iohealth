{{-- Sólo funciona si el div se llama map_canvas --}}
<div id='chart{{$chartId}}'>
	<div id='map3D_canvas' style="height: 500px;">
	</div>
	<div id='map_menu_container' style='position: absolute; top: 0; right: 0;'>
	</div>
</div>

<script>
crHooks.push(function()
{
	var data = {{ json_encode($data) }};

	/* if ($mapType == 'regular') */
	/* { */
	/* 	var map = new CRMaps('map_canvas',$('#map_canvas').width(),$('#map_canvas').height()); */
	/* 	map.dasymetric3D(data,"{{ $map_url }}"); */
	/* } */
	/* else if ($mapType == 'political') */
	/* { */
		var map3D = new CRMaps3D('map3D_canvas',$('#map3D_canvas').width(),$('#map3D_canvas').height());
		map3D.choropleth3D(data, "{{ $map_url }}");
	/* } */
});
</script>

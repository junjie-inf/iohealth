{{-- Sólo funciona si el div se llama map_canvas --}}
<div id='chart{{$chartId}}'>
	<div id='map_canvas' style="height: 500px;">
	</div>
	<div id='map_menu_container' style='position: absolute; top: 0; right: 0;'>
	</div>
</div>

<script>
crHooks.push(function()
{
	var data = {{ json_encode($data) }};
    var map = new CRMaps('map_canvas',$('#map_canvas').width(),$('#map_canvas').height()); 
    map.dasymetric3D(data,"{{ $map_url }}");
});
</script>
@extends('templates.default')

@section('content')
	@include( 'templates.mustache.infowindow' )
	<div class="container custom-fluid-container">

		<div class="cr row">
			<div class="cr col-sm-12">
				<div id="map_canvas"></div>
			</div>
		</div>

	</div>
	<canvas id="markerCanvas" width="50" height="50" style="visibility: hidden;"></canvas>
	
	{{-- [Modal] View Report --}}
	@include( 'templates.mustache.report' )

@stop


@section('specific-css-plugins')
	@stylesheets('bootstrap-modal')
@stop


@section('custom-css')
@stop

@section('specific-javascript-plugins')
	@javascripts('google-maps-cr')
	@javascripts('bootstrap-modal')
	@javascripts('cr_template')
@stop


@section('custom-javascript')
<script>
var $MAP = {
	latitudes: {{ json_encode( $latitudes ) }},
	longitudes: {{ json_encode( $longitudes ) }}
};

var templates_data = {{ json_encode($templates) }};

var datecallback = function(){ CoolReport.updateMarkers(true) };

</script>
@stop
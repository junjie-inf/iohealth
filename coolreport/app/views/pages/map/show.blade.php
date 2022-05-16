@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

{{-- [Modal] View Report --}}
	@include( 'templates.mustache.expreditor' )


<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-plus"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('map.index') }}"><i class="icon-arrow-left"></i><span>Back</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<form class="form-horizontal form-store fix-margins" action="" data-type="map">

					<fieldset class="col-sm-12">

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="title">Title</label>
									<div class="controls">
										<input class="form-control" id="title" name="title" type="text" value="{{ $data->title }}" required />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="title">Map file</label>
									<div class="box-content" style="height: 520px">
										<div class="row">
											<div class="col-sm-12">
												<div id="map" class="well well-small inh_nopadding inh_nomargin"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</form>			
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->
	
@stop

@section('specific-css-plugins')
@stop

@section('specific-javascript-plugins')
@javascripts('cr_template')
@stop


@section('custom-javascript')
	{{-- inline scripts related to this page --}}
	<script>
		$(document).ready(function(){
			Templates.initDropzone();

			// Relleno mapa del report
			var canvas = $('#map'), dimensions = { width: canvas.width(), height: canvas.height() };
			
			var map = new google.maps.Map(document.getElementById('map'), {
				center: new google.maps.LatLng(0,0),
				zoom: 1});
			
			var geojson = {{ json_encode($geojson) }};

			if (typeof geojson.type === 'undefined' )
			{
				for (var i=0; i<geojson.length; i++)
				{
					// Compruebo que tenga geometry para mostrar el mapa
					if ( geojson[i].geometry !== null )
					{
						CoolReport.drawFromGeoJSON(map, geojson[i].geometry);	
					}					
				}
				//CoolReport.zoomToGeoJSON(map, {type: 'MultiPolygon'});
			}
			else
			{
				CoolReport.drawFromGeoJSON(map, geojson);
				CoolReport.zoomToGeoJSON(map, geojson);
			}
			
			$('#map').height(450).width('auto'); 
		});

	</script>
@stop
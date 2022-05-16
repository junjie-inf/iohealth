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
					<a href="{{ URL::route('map.index') }}"><i class="icon-arrow-left"></i><span>{{ trans('maps.back') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<form class="form-horizontal form-store fix-margins" action="" data-type="map">

					<fieldset class="col-sm-12">

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="title">{{ trans('maps.title') }}</label>
									<div class="controls">
										<input class="form-control" id="title" name="title" type="text" value="" required />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="title">{{ trans('maps.mapfile') }}</label>
									<div class="controls">
										<div class="dropzone dz-clickable" id="dropzone-geojsonfile">
											<div class="dz-default dz-message"><span>{{ trans('maps.dropupload') }}</span></div>
										</div>
										<input class="form-control form-file" url="map/file" accept=".json,.geojson" name="geojsonfile" id="geojsonfile" type="file" disabled="" style="display: none;" max=1>
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
						</div>
			
					</fieldset>

					<div class="clearfix">
						<div class="col-lg-12 inh_nopadding">
							<div class="form-actions clearfix text-center">
								<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('maps.sending') }}">{{ trans('maps.savechanges') }}</button>
							</div>
						</div>
					</div>

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
		});
	</script>
@stop

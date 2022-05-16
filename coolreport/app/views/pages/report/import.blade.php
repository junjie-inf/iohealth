@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

@if (! is_null($errors) )
	<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert">×</button><strong style="text-transform:capitalize">Error</strong>: {{ $errors }}
	</div>
@endif

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span>{{ trans('sections.report.import') }}</h2>
			</div>
			
			<div class="box-content">
				{{ Form::open(array('route' => array('report.populate'), 'files' => true)); }}
				<ul class="nav tab-menu nav-tabs">
					<li><a href="{{ URL::to('report') }}"><i class="icon-arrow-left"></i> Back to list</a></li>
				</ul>
				
				<div class="row margin-top-20">
					<div class="col-xs-12">
		
						<div class="box">
							<div class="box-header level-2 blue">
								<h2><i class="icon-exclamation"></i><span class="break"></span>{{ trans('pagination.important') }}</h2>
								<div class="box-icon">
									<a href="javascript:void(0)" class="btn-minimize"><i class="icon-chevron-up"></i></a>
								</div>
							</div>

							<div class="box-content report-data-view">
								<form class="report-content">
									<p>Asegúrese de escoger el <b>encoding</b> correspondiente al fichero a importar. Los datos se deben ver correctamente en la previsualización.</p>
								</form>
							</div>
						</div>

						<fieldset class="col-sm-12">
						
						<div class="form-group">
							<label class="control-label" for="data-select">Data Origin <i class="icon-spinner icon-spin" style="display:none"></i></label>
							<div class="controls">
								<select id="data-select" name="data-select" class="form-control" data-rel="chosen" data-placeholder="Choose a Origin...">
									<option></option>
									@foreach( $origins as $origin )
										<option value="{{ $origin }}">{{ $origin }}</option>
									@endforeach
								</select>
							</div>
						</div>

						</fieldset>
					
						<fieldset class="col-sm-12">
						
						<div class="form-group">
							<label class="control-label" for="template-select">Template <i class="icon-spinner icon-spin" style="display:none"></i></label>
							<div class="controls">
								<select id="template-select" name="template" class="form-control" data-rel="chosen" data-placeholder="Choose a Template...">
									<option></option>
									@foreach( $templates as $template )
										<option value="{{ $template->id }}">{{ $template->title }}</option>
									@endforeach
								</select>
							</div>
						</div>

						</fieldset>

						<fieldset class="col-sm-12">
						
						<div class="form-group config-group" style="display: none">
							<label class="control-label">Configuration</label>
						</div>

						</fieldset>
						
						<div class="file-group" style="display: none">
							<div class="box-content">
								<ul class="nav nav-tabs">
									<li class="active"><a href="#file" data-toggle="tab">File</a></li>
									{{-- <li><a href="#url" data-toggle="tab">URL</a></li> --}}
									<li><a href="#paste" data-toggle="tab">Paste</a></li>
								</ul>

								<div id="myTabContent" class="tab-content">
									<div class="tab-pane fade active in" id="file">
										<fieldset class="col-sm-12 file-group" style="display: none">

										<div class="form-group col-sm-6">
											{{ Form::label('file-import',trans('validation.attributes.file'), array('for' => 'file-import', 'class' => 'control-label')); }}
											<div class="controls clearfix margin-bottom-10">
												{{ Form::file('file-import', array('class' => 'form-control', 'required')); }}
											</div>
										</div>
										<div class="form-group col-sm-6">
											{{ Form::label('encoding', 'Encoding') }}
											<div class="controls">
												<select id="encoding-select" name="encoding" class="form-control" data-rel="" data-placeholder="Choose a Encoding...">
													<option></option>
													@foreach( $encodes as $encode )
														<option value="{{ $encode }}">{{ $encode }}</option>
													@endforeach
												</select>
											</div>
										</div>

										</fieldset>
									</div>
									<div class="tab-pane fade" id="url">
										<fieldset class="col-sm-12 file-group" style="display: none">	
											<div class="form-group col-sm-8">
											{{ Form::label('csv-url','CSV from URL', array('for' => 'csv-url', 'class' => 'control-label')); }}
											<div class="controls clearfix margin-bottom-10">
												<div class="controls"><input class="form-control" name="csv-url" id="csv-url" type="text" value=""></div>
											</div>
											</div>
											<div class="form-group col-sm-3">
											{{ Form::label('encoding', 'Encoding') }}
											<div class="controls">
												<select id="encoding-select-csvurl" name="encoding" class="form-control" data-rel="" data-placeholder="Choose a Encoding...">
													<option></option>
													@foreach( $encodes as $encode )
														<option value="{{ $encode }}">{{ $encode }}</option>
													@endforeach
												</select>
											</div>
											</div>
											<div class="form-group col-sm-1">
												<div class="controls clearfix margin-bottom-10 margin-top-25">
													<button class="btn btn-small tryparse"><i class="icon-chevron-sign-right"></i> Try connection</button>
												</div>
											</div>
										</fieldset>
									</div>
									<div class="tab-pane fade" id="paste">
										<div class="controls">
											<textarea id="pastearea" rows="6" style="width: 100%; overflow: hidden; word-wrap: break-word; height: 126px; margin-left: 0px; margin-right: 0px;"></textarea>
									  	</div>
									  	<div class="form-group col-sm-12">
												<div class="controls clearfix margin-bottom-10 margin-top-25">
													<button class="btn btn-small tryparse"><i class="icon-chevron-sign-right"></i> Parse</button>
												</div>
										</div>
										<input type="hidden" name="csv-type" id="csv-type" value="" style="width: 100%"/>
										<input type="hidden" name="csv-paste" id="csv-paste" value="" style="width: 100%"/>
									</div>
								</div>
								<fieldset class="col-sm-12 header-checkbox" style="display: none">				
										
										<div class="form-group col-sm-12">
											{{ Form::label('assigment-import', 'Assignment', array('for' => 'assigment-import', 'class' => 'control-label')); }}
											<label class="checkbox"><input name="header" type="checkbox" id="import-header" value="true">First line is the header</label>
										</div>

								</fieldset>
							</div>
							
						</div>
						
						<fieldset class="col-sm-12 db-group" style="display: none">

						<div class="form-group col-sm-2">
							{{ Form::label('system-db', 'Database system', array('for' => 'system-db', 'class' => 'control-label')); }}
							<div class="controls">
								<select id="dbsystem-select" name="system" class="form-control" data-rel="" data-placeholder="Choose a Database...">
									<option></option>
									@foreach( $db as $system )
										<option value="{{ $system }}">{{ $system }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="form-group col-sm-3">
							{{ Form::label('host-db','Host', array('for' => 'host-db', 'class' => 'control-label')); }}
							<div class="controls clearfix margin-bottom-10">
								<div class="controls"><input class="form-control" name="host" id="host-db" type="text" value=""></div>
							</div>
						</div>

						<div class="form-group col-sm-1">
							{{ Form::label('port-db','Port', array('for' => 'port-db', 'class' => 'control-label')); }}
							<div class="controls clearfix margin-bottom-10">
								<div class="controls"><input class="form-control" name="port" id="port-db" type="text" value=""></div>
							</div>
						</div>

						<div class="form-group col-sm-2">
							{{ Form::label('database-db','Database', array('for' => 'database-db', 'class' => 'control-label')); }}
							<div class="controls clearfix margin-bottom-10">
								<div class="controls"><input class="form-control" name="database" id="database-db"   type="text" value=""></div>
							</div>
						</div>
						
						<div class="form-group col-sm-1">
							{{ Form::label('user-db','User', array('for' => 'user-db', 'class' => 'control-label')); }}
							<div class="controls clearfix margin-bottom-10">
								<div class="controls"><input class="form-control" name="user" id="user-db" type="text" value=""></div>
							</div>
						</div>
						
						<div class="form-group col-sm-1">
							{{ Form::label('password-db','Password', array('for' => 'password-db', 'class' => 'control-label')); }}
							<div class="controls clearfix margin-bottom-10">
								<input class="form-control" name="password" id="password-db" type="password">
							</div>
						</div>

						<div class="form-group col-sm-2">
							<div class="controls clearfix margin-bottom-10 margin-top-25">
								<button class="btn btn-small tryparse"><i class="icon-chevron-sign-right"></i> Try connection</button>
							</div>
						</div>

						</fieldset>

						<fieldset class="col-sm-12" id="tables-db" style="display: none">
						
						<div class="form-group col-sm-3">
							{{ Form::label('tables', 'Tables') }}
							<div class="controls">
								<select id="tables-select" name="table" class="form-control" data-rel="" data-placeholder="Choose a Table...">
									<option></option>
								</select>
							</div>
						</div>

						</fieldset>

						<div class="form-group col-sm-12" id="import-table" style="overflow: auto">
						</div>
						<div class="clearfix">
							<div class="col-lg-12 inh_nopadding">
								<div class="form-actions clearfix text-center">
									{{ Form::submit(trans('validation.attributes.import'), array('class' => 'btn btn-primary btn-lg btn-block', 'data-loading-text' => '<i class="icon-spinner icon-spin"></i> Sending...')); }}
								</div>
							</div>
						</div>
					</div>
				</div>	
				{{ Form::close(); }}			
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->
@stop


@section('specific-css-plugins')
@stop


@section('specific-javascript-plugins')
@javascripts('cr_template')
@javascripts('cr_import')
@stop


@section('custom-javascript')
<script>
var fields;
$(document).ready(function(){
	/* jQuery Chosen */
	$('[data-rel="chosen"],[rel="chosen"]').chosen();

	/* Get Template */
	$(document).on('change', '#template-select', function(){
		
		//$("#import-table").children().remove();
		$('#file').val(null);

		var $this = $(this),
			template_id = $this.val(),
			$fieldset = $('.insert-template'),
			$spinner = $this.closest('.form-group').find('.icon-spinner');

		$spinner.show(); // SPINNER on

		$.getJSON( $SITE_PATH + 'template/'+template_id, {readonly: false}, function(d){

			if( (d !== null) && (typeof d.status !== "undefined") && (d.status !== null) && (d.status === "OK") ){
				fields = d.data.fields;

		        // Añado opciones para direccion y lat/long
		        var date = {id:'date', label:'date', type:'date'};
		        var address = {id:'address', label:'address', type:'address'};
		        var latitude = {id:'latitude', label:'latitude', type:'latitude'};
		        var longitude = {id:'longitude', label:'longitude', type:'longitude'};
		        var main_geo = {id:'main_geo', label:'GeoJson', type:'main_geo'};
	
				fields.push(date);
				fields.push(address);
				fields.push(latitude);
				fields.push(longitude);
				fields.push(main_geo);

				$('.insert-template select').chosen();
				
				Templates.initDropzone();
			}

			$spinner.hide(); // SPINNER off

			$('#encoding-select').val('ISO-8859-1');
			$('.insert-encoding select').chosen();

			if ( $('#data-select').val() != '' )
			{
				$('.config-group').show();
				show_type_block();
			}
			if ( $('#file-import').val() != '' )
			{
				readSingleFile();
			}

			return false;
		})
		.fail(function(d){
			$spinner.hide(); // SPINNER off

			d = d.responseJSON;

			if( d.status === 'ERROR' ){
				$.each(d.messages, function(field, message){
					Forms.addAlert( field, message );
				});
			}else{ // status === FATAL
				Forms.addAlert( 'error', $_LANG.GENERIC.error_msg );
			}
		});
	});
});
</script>
@stop
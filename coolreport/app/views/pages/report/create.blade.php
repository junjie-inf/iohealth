@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

@include( 'templates.mustache.select_report' )
@include( 'templates.mustache.expreditor' )

<div class="row">

	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-plus"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
				<div class="box-icon">
					<a href="{{ URL::previous() }}"><i class="icon-arrow-left"></i><span>{{ trans('reports.createmode.back')}}</span></a>
				</div>
				<div id="signs" class="box-icon hidden">
					<a href="{{ URL::route('report.signs') }}" target="_blank"><i class="icon-hospital"></i><span>Síntomas</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<form class="form-horizontal form-store form-report" action="" data-type="report" enctype="multipart/form-data">
					{{-- https://developers.google.com/maps/documentation/javascript/v2/reference?hl=es&csw=1#GGeoAddressAccuracy --}}
					<input type="hidden" name="accuracy" value="8" />
					
					<fieldset class="col-sm-12">
						
						<div class="form-group">
							<label class="control-label" for="template-select">{{ trans('reports.createmode.template')}} <i class="icon-spinner icon-spin" style="display:none"></i></label>
							<div class="controls">
								<select id="template-select" name="template" class="form-control" data-rel="chosen" data-placeholder={{ trans('reports.createmode.chooseatemplate')}}>
									<option></option>
									@foreach( $templates as $template )
										<option value="{{ $template->id }}">{{ $template->title }}</option>
									@endforeach
								</select>
							</div>
						</div>

					</fieldset>
					
					<hr />
					
					<fieldset class="col-sm-12 insert-template">
						
						<div class="alert alert-info">
							<i class="icon-arrow-up"></i> {{ trans('reports.createmode.chooseatemplatetofill')}}.
						</div>
						
					</fieldset>
					
					<hr />
					
					<fieldset id="geolocation" class="col-sm-12 hidden">
						<div class="form-group">
							<label class="control-label" for="address">{{ trans('reports.createmode.address')}}</label>
							<div class="controls clearfix margin-bottom-10">
								<input class="form-control location-picker-address" id="address" name="address" type="text" value="" />
							</div>
							<input type="hidden" name="geo" value="" style="width: 100%"/>
							<div id="mapgeocoding" class="well well-small inh_nopadding inh_nomargin" style="height:300px"></div>
						</div>

						<div class="form-group">
							<div class="controls row">
								<div class="input-group col-sm-3">
									<label class="control-label" for="latitude">{{ trans('reports.createmode.latitude')}}</label>
									<input class="form-control location-picker-latitude" id="latitude" name="latitude" type="text" value="{{ $latitude }}" />
								</div>
								<div class="input-group col-sm-3">
									<label class="control-label" for="longitude">{{ trans('reports.createmode.longitude')}}</label>
									<input class="form-control location-picker-longitude" id="longitude" name="longitude" type="text" value="{{ $longitude }}" />
								</div>
							</div>
						</div>
					</fieldset>

					<div class="clearfix">
						<div class="col-lg-12 inh_nopadding">
							<div class="form-actions clearfix text-center">
								<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('reports.createmode.sending')}}">{{ trans('reports.createmode.savechanges')}}</button>
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
@javascripts('cr_expreditor')
@javascripts('cr_dashitem')
@stop


@section('custom-javascript')
<script>
$(document).ready(function(){
	/* jQuery Chosen */
	$('[data-rel="chosen"],[rel="chosen"]').chosen();

	/* Get Template */
	$(document).on('change', '#template-select', function(){
		var $this = $(this),
			template_id = $this.val(),
			$fieldset = $('.insert-template'),
			$spinner = $this.closest('.form-group').find('.icon-spinner');

		$spinner.show(); // SPINNER on

		$.getJSON( $SITE_PATH + 'template/'+template_id, {readonly: false}, function(d){

			$('#emailControl').remove();
			$('#passwordControl').remove();
			$('#passwordConfirmControl').remove();
			
			if( (d !== null) && (typeof d.status !== "undefined") && (d.status !== null) && (d.status === "OK") ){
				$fieldset.html(d.form);

				if (d.data.settings.geolocation)
					$('#geolocation').removeClass('hidden');
				else
					$('#geolocation').addClass('hidden');

				$('.insert-template select').chosen();

				if ($this.find("option:selected").text() === 'Síntomas')
				{
					$('#signs').removeClass('hidden');
				}
				else
				{
					$('#signs').addClass('hidden');	
				}
				
				Templates.init();
				Templates.initDropzone();
				
				// Ya hago setPickers en el init :/
				//Templates.setPickers(); // Pickers

				if(template_id == 10){	 //Persona
					var email = $('<div id="emailControl"><label>Email <i class="icon-asterisk red" title="Required"></i></i></label><div>'+
						'<input class="form-control" id="email" type="text" value=""></div></div>');
					var password = $('<div id="passwordControl"><label>Contraseña <i class="icon-asterisk red" title="Required"></i></i></label><div class="controls">'+
						'<input class="form-control" id="password" type="password" value=""></div></div>');
					var passwordConfirm = $('<div id="passwordConfirmControl"><label>Confirmar Contraseña <i class="icon-asterisk red" title="Required"></i></i></label><div class="controls">'+
						'<input class="form-control" id="passwordConfirm" type="password" value=""></div></div>');

					$('.insert-template').before(email);
					$('.insert-template').before(password);
					$('.insert-template').before(passwordConfirm);

					$('input[name="5889efb0944a90"]').closest('.form-group').hide();
					$('input[name="5889efb0944a90"]').val(0);
				}
				if(template_id == 6){ //sintoma

					console.log({{$registerTemplates}});
					$('textarea[name=592c2c33469e30]').on('click', function(){
						ExprEditor.showExprEditor(this, {{$registerTemplates}});
					});
				}
				@if ( !Auth::user()->admin ) {
					if(template_id == 11 ){ //Paciente
						//$('input[name="5889f0a9092ea3"]').closest('.form-group').hide();
					}
				}
				@endif				
			}
			$spinner.hide(); // SPINNER off

            @if ($link !== null && $linkTemplate !== null && $linkTitle !== null )
	        /* Gestion de la preseleccion de template */
                $('input[data-template="{{ $linkTemplate }}"]').val({{$link}});
                $('input[data-template="{{ $linkTemplate }}"]').next().next().text("{{$linkTitle}}");


            @endif

			return false;
		},function(data){
			console.log(data);
			$('input[name="5889efb0944a90"]').val(data['id']);
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

	$('button[type="submit"]').click(function(event){


		if ($('#template-select').val() == 10) //Persona
		{
			if($('form.form-report')[0].checkValidity()) { //Si no se han rellenado los campos necesarios, lo enviamos con el de defecto para que de error la validación del browser por defecto o la nuestra (según el browser la soporte o no)

				event.preventDefault();

				if(!Forms.validateRequired($('form.form-report'))) {
					return false;
				}

				if(!($('#password').val() == $('#passwordConfirm').val())){
					$('#general-alert').remove();
					Forms.addAlert( 'Error', 'Las contraseñas no coinciden', null, 'general-alert');
					document.querySelector('#cr-navbar').scrollIntoView({block: "end", behavior: "smooth"});
					return false;
				}

				// Post user
				$.post($SITE_PATH + 'user',{
					'email': $('#email').val(),
					'password': $('#password').val(),
					'password2': $('#passwordConfirm').val(),
					'firstname': $('input[name="5889efb0944a91"]').val(),
					'surname': $('input[name="59143940683680"]').val(),
					'role_id': 3,
					'group_id':1
				},function(data){
					console.log(data);
					$('input[name="5889efb0944a90"]').val(data['id']);

					// Post Report
					Forms.post( $SITE_PATH + 'report', $('.form-report').serialize()+'&'+$.param({ 'user': data['id'] }), $('.form-report').find(':submit'));
					return true;
					
				}).fail(function(){
					$('#general-alert').remove();
					Forms.addAlert( 'Error', 'Error creando el usuario', null, 'general-alert');
					document.querySelector('#cr-navbar').scrollIntoView({block: "end", behavior: "smooth"});
					return false;
				});
			}
		}
	});

	LocationPicker.init( $('form.form-report'), 'report' );

    @if ($selectedTemplate !== null)
    /* Gestion de la preseleccion de template */
        $('#template-select').val({{$selectedTemplate->id}});
        console.log({{$selectedTemplate->id}}, $('#template-select'));
        $('#template-select').trigger("chosen:updated");
        $('#template-select').change();
        $('#template-select').parents('fieldset').hide();
    @endif

});
</script>
@stop
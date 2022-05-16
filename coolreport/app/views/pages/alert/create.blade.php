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
					<a href="{{ URL::route('alert.index') }}"><i class="icon-arrow-left"></i><span>{{trans('alerts.back')}}</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<form class="form-horizontal form-store fix-margins" action="" data-type="alert">

					<fieldset class="col-sm-12">

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="title">{{trans('alerts.title')}}</label>
									<div class="controls">
										<input class="form-control" id="title" name="title" type="text" value="" required />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="template-select">{{trans('alerts.template')}} <i class="icon-spinner icon-spin" style="display:none"></i></label>
									<div class="controls">
										<select id="template-select" name="template" class="form-control" data-rel="chosen" data-placeholder="Choose a Template...">
											<option></option>
											@foreach( $templates as $template )
												<option value="{{ $template->id }}">{{ $template->title }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="conditions">{{trans('alerts.conditions')}}</label>
									<div class="controls">
										<input class="form-control" id="conditions" name="conditions" type="text" value="" required />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="actions">{{trans('alerts.actions')}}</label>
									<div class="controls">
										<select id="actions-select" name="actions" class="form-control" data-rel="chosen" data-placeholder="Choose a Template...">
											<option></option>
											@foreach( $templates as $template )
												<option value="{{ $template->id }}">{{ $template->title }}</option>
											@endforeach
										</select>
										<fieldset class="col-sm-12 insert-template well">
											<div class="alert alert-info">
												<i class="icon-arrow-up"></i> {{trans('alerts.selectTemplate')}}.
											</div>			
										</fieldset>
									</div>
								</div>
							</div>
						</div>
			
					</fieldset>

					<div class="clearfix">
						<div class="col-lg-12 inh_nopadding">
							<div class="form-actions clearfix text-center">
								<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending...">{{trans('alerts.save')}}</button>
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
@stylesheets('cr_expreditor')
@stop

@section('specific-javascript-plugins')
{{-- Dejo cr_dashitem porque de momento es necesario para el cr_expreditor por uso de constantes comunes, pdte de crear un js con ctes comunes --}}
@javascripts('cr_dashitem')
@javascripts('cr_expreditor')
@stop


@section('custom-javascript')
	{{-- inline scripts related to this page --}}
	<script>
		var CR_TEMPLATES = {{ json_encode(array_combine(array_column($templates->toArray(),'id'), $templates->toArray())) }};
		var CR_TEMPLATE;

		$(document).ready(function(){
			
			$(document).on('click', '#conditions', function(){
				ExprEditor.showExprEditor(this, CR_TEMPLATE, true);
			});

			$(document).on('change', '#template-select', function(){
				CR_TEMPLATE = [];
				CR_TEMPLATE.push(CR_TEMPLATES[$('#template-select').val()]);
			});

			$(document).on('change', '#actions-select', function(){
				var $this = $(this),
					template_id = $this.val(),
					$fieldset = $('.insert-template'),
					$spinner = $this.closest('.form-group').find('.icon-spinner');

				$spinner.show(); // SPINNER on

				$.getJSON( $SITE_PATH + 'template/'+template_id, {readonly: false}, function(d){

					if( (d !== null) && (typeof d.status !== "undefined") && (d.status !== null) && (d.status === "OK") ){
						$fieldset.html(d.form);	

						$('.insert-template .controls:has(.btn-select-report)').each(function(){
							ExprEditor.addSwitchReportSelect( this, true);
						});
					} 
					/** COMENTO POR SI HICIERA FALTA EN OTROS CASOS
					if (d.data.settings.geolocation)
						$('#geolocation').removeClass('hidden');
					else
						$('#geolocation').addClass('hidden');

					$('.insert-template select').chosen();
					
					Templates.initDropzone();
					
					Templates.setPickers(); // Pickers
					**/

					$spinner.hide(); // SPINNER off
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
				
			$(document).on('click', '.insert-template > .form-group :text, .insert-template > .form-group textarea' , function(){
				ExprEditor.showExprEditor(this, CR_TEMPLATE, false);
			});

		});

	</script>
@stop
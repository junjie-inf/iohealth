@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

@include( 'templates.mustache.select_report' )
@include( 'templates.mustache.expreditor' )

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-pencil"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('report.show', $data->id) }}"><i class="icon-arrow-left"></i><span>{{ trans('reports.editmode.back')}}</span></a>
				</div>
			</div>
			<div class="box-content">

				@if( $data->template_id == 10 )
				<form id="user" class="form-horizontal form-edit" action="" data-type="user" data-id="{{ $user->id }}">
					<input type="hidden" name="_method" value="PUT" />

					<fieldset class="col-sm-12">

						<div class="row hidden">
							<div class="form-group">
								<input class="hidden" name="redir" value=false />
								<div class="col-sm-6">
									<label class="control-label" for="firstname">{{ trans('users.firstname') }}</label>
									<div class="controls">
										<input class="form-control" id="firstname" name="firstname" type="text" value="{{ $user->firstname }}" required />
									</div>
								</div>

								<div class="col-sm-6">
									<label class="control-label" for="surname">{{ trans('users.surname') }}</label>
									<div class="controls">
										<input class="form-control" id="surname" name="surname" type="text" value="{{ $user->surname }}" required />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-6">
									<label class="control-label" for="email">{{ trans('users.email') }}<i class="icon-asterisk red" title="Required"></i></label>
									<div class="controls">
										<input class="form-control" id="email" name="email" type="email" value="{{ $user->email }}" required />
									</div>
								</div>
								
								<div class="col-sm-3">
									<label class="control-label" for="password">{{ trans('users.password') }}<i class="icon-asterisk red" title="Required"></i></label>
									<div class="controls">
										<input class="form-control" id="password" name="password" type="password" value="" />
									</div>
								</div>

								<div class="col-sm-3">
									<label class="control-label" for="password2">{{ trans('users.confirmpassword') }}<i class="icon-asterisk red" title="Required"></i></label>
									<div class="controls">
										<input class="form-control" id="password_confirmation" name="password_confirmation" type="password" value="" />
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</form>
				@endif
				
				<form id="report" class="form-horizontal form-edit form-report action-data" action="" data-type="report" data-id="{{ $data->id }}" data-redirect-url="{{ URL::route('report.index') }}">
					<input type="hidden" name="_method" value="PUT" />
					<input type="hidden" name="accuracy" value="8" />
					
					<fieldset class="col-sm-12">
						
						{{ $data->content }}
						
						@if ($data->template->settings->geolocation) 
						<div class="form-group">
							<label class="control-label" for="address">{{ trans('reports.editmode.address')}}</label>
							<div class="controls clearfix margin-bottom-10">
								<input class="form-control location-picker-address" id="address" name="address" type="text" value="{{ $data->address }}" required />
							</div>
							<input type="hidden" name="geo" value="" style="width: 100%"/>
							<div id="mapgeocoding" class="well well-small inh_nopadding inh_nomargin" style="height:300px"></div>
						</div>
						
						@if ($data->geo->type == 'Point')
						<div class="form-group">
							<div class="controls row">
								<div class="input-group col-sm-3">
									<label class="control-label" for="latitude">{{ trans('reports.editmode.latitude')}}</label>
									<input class="form-control location-picker-latitude" id="latitude" name="latitude" type="text" value="{{ $data->geo->coordinates[1] }}" required />
								</div>
								<div class="input-group col-sm-3">
									<label class="control-label" for="longitude">{{ trans('reports.editmode.longitude')}}</label>
									<input class="form-control location-picker-longitude" id="longitude" name="longitude" type="text" value="{{ $data->geo->coordinates[0] }}" required />
								</div>
							</div>
						</div>
						@else
						<div class="form-group">
							<div class="controls row">
								<div class="input-group col-sm-3">
									<label class="control-label" for="latitude">{{ trans('reports.editmode.latitude')}}</label>
									<input class="form-control location-picker-latitude" id="latitude" name="latitude" type="text" value="{{ ($data->geo->bbox[1] + $data->geo->bbox[3]) / 2  }}" required />
								</div>
								<div class="input-group col-sm-3">
									<label class="control-label" for="longitude">{{ trans('reports.editmode.longitude')}}</label>
									<input class="form-control location-picker-longitude" id="longitude" name="longitude" type="text" value="{{ ($data->geo->bbox[0] + $data->geo->bbox[2]) / 2  }}" required />
								</div>
							</div>
						</div>
						@endif
						@endif
					</fieldset>

					<div class="clearfix">
						@if( $data->removable )
							<div class="col-lg-6 inh_nopadding-left">
								<div class="form-actions clearfix text-center">
									<button type="button" class="btn btn-danger btn-lg btn-block action-delete" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('reports.editmode.sending')}}">{{ trans('reports.editmode.deletereport')}}</button>
								</div>
							</div>
							<div class="col-lg-6 inh_nopadding-right">
								<div class="form-actions clearfix text-center">
									<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('reports.editmode.sending')}}">{{ trans('reports.editmode.savechanges')}}</button>
								</div>
							</div>
						@else
							<div class="col-lg-12 inh_nopadding">
								<div class="form-actions clearfix text-center">
									<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('reports.editmode.sending')}}">{{ trans('reports.editmode.savechanges')}}</button>
								</div>
							</div>
						@endif
					</div>
					
				</form>
				
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->

@stop


@section('specific-javascript-plugins')
@javascripts('cr_template')
@javascripts('cr_expreditor')
@javascripts('cr_dashitem')
@stop

@section('specific-css-plugins')
@stop


@section('custom-javascript')
	<script>
	
	var geojson = {{ json_encode($data->geo) }};

	$(document).ready(function(){
		@if ($data->template->settings->geolocation)
			LocationPicker.init( $('form.form-report'), 'report' );
		@endif
		
		$('[data-rel="chosen"],[rel="chosen"]').chosen();
		
		
		$('.bootstrap-timepicker').closest('.form-group').find('label').first().append('<a href="javascript:void(0)" class="blue action-timepicker-clear">[clear]</a>');
		
		$(document).on('click', '.action-timepicker-clear', function(){
			$(this).closest('label').siblings('.controls').find('.time-picker').val('');
		});
		
		Templates.initDropzone();

		Templates.setPickers(); // Pickers
		
		Templates.drawAttachments( true, {{ $data->id }} );

		@if( $data->template_id == 10 )
			$('input[name="5889efb0944a90"]').closest('.form-group').hide();

			$('button[type="submit"]').click(function(event){
				event.preventDefault();
				$('#firstname').val($('input[name="5889efb0944a91"]').val());
				$('#surname').val($('input[name="59143940683680"]').val());
				$('#user').submit();
				$('#report').submit();
			});
		@endif

		@if ($data->template->id == 6)
			$('textarea[name=592c2c33469e30]').on('click', function(){
				ExprEditor.showExprEditor(this, {{$registerTemplates}});
			});
			ExprEditor.viewExpr($('textarea[name=592c2c33469e30]'), {{$registerTemplates}});
		@endif

	});
	</script>
	
@stop
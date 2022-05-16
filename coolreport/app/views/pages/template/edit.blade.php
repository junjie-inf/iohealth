@extends('templates.default')

@section('title', trans('sections.template.edit' ))

@section('content')

	{{-- [Modal] View Report --}}
	@include( 'templates.mustache.template' )

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-pencil"></i><span class="break"></span>{{ trans('sections.template.edit') }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('template.show', $data->id) }}"><i class="icon-arrow-left"></i><span>{{ trans('templates.back') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<form class="form-horizontal form-template-update fix-margins" action="" data-type="template" data-id="{{ $data->id }}">
					<fieldset class="col-sm-12">
						
						<div class="form-group">
							<label class="control-label" for="title">{{ trans('templates.title') }}</label>
							<div class="controls">
								<input class="form-control" id="title" name="title" type="text" value="{{ $data->title }}" required autofocus />
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label">{{ trans('templates.fields') }}</label>
						</div>

						<div class="well">
							
							<div class="fields-container"></div>

							<div class="controls">
								<div class="btn-group">
									<button class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
										<i class="icon-plus"></i> {{ trans('templates.addfield') }} <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="checkbox"><i class="icon-check"></i> {{ trans('templates.checkbox') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="color"><i class="icon-tint"></i> {{ trans('templates.color') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="date"><i class="icon-calendar"></i> {{ trans('templates.date') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="decimal"><i class="icon-euro"></i> {{ trans('templates.decimal') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="email"><i class="icon-envelope-alt"></i> {{ trans('templates.email') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="file"><i class="icon-paper-clip"></i> {{ trans('templates.file') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="number"><i class="icon-tag"></i> {{ trans('templates.number') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="radio"><i class="icon-circle-blank"></i> {{ trans('templates.radio') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="range"><i class="icon-resize-horizontal"></i> {{ trans('templates.range') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="select"><i class="icon-collapse"></i> {{ trans('templates.select') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="text"><i class="icon-terminal"></i> {{ trans('templates.text') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="textarea"><i class="icon-align-left"></i> {{ trans('templates.textarea') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="time"><i class="icon-time"></i> {{ trans('templates.time') }}</a>
										</li>
										<li>
											<a href="javascript:void(0)" class="action-add-field" data-type="report"><i class="icon-file-text-alt"></i> {{ trans('templates.report') }}</a>
										</li>
									</ul>
								</div>
							</div>
							
						</div>
						
						<div class="form-group">
							<label class="control-label">{{ trans('templates.settings') }}</label>
						</div>
						<div class="well row">
							<div class="col-sm-3">
								<label for="geolocation"><i class="icon-map-marker"></i> {{ trans('templates.geolocation') }}</label>
								<label class="switch switch-primary">
										      <input id="geolocation" name="geolocation" type="checkbox" 
											@if (isset($data->settings->geolocation) && $data->settings->geolocation)
												checked
											@endif
											class="switch-input">
										      <span data-off="Off" data-on="On" class="switch-label"></span>
										      <span class="switch-handle"></span>
										    </label>
							</div>
							<div class="col-sm-3">
								<label for="visible"><i class="icon-eye-open"></i> {{ trans('templates.viewinmap') }}</label>
								<label class="switch switch-primary">
										      <input id="visible" name="visible" type="checkbox" 
											@if (isset($data->settings->visible) && $data->settings->visible)
												checked
											@endif
											class="switch-input">
										      <span data-off="Off" data-on="On" class="switch-label"></span>
										      <span class="switch-handle"></span>
										    </label>
							</div>
							<div class="col-sm-3">
								<label>{{ trans('templates.filterbydate') }}</label>
								<button id="template-date-filter-none" type="button" class="btn btn-sm btn-primary template-date-filter
								@if (!isset($data->settings->date))
								active
								@endif
								" data-toggle="button" title="Mark this field as date filter"><i class="icon-calendar-empty"></i> {{ trans('templates.none') }}</button>
								<button id="template-date-filter-created-at" type="button" class="btn btn-sm btn-primary template-date-filter
								@if (isset($data->settings->date) && $data->settings->date == '_created_at')
								active
								@endif" data-toggle="button" title="Mark this field as date filter"><i class="icon-calendar"></i> {{ trans('templates.creation') }}</button>
							</select></div>
							<div class="col-sm-3">
								<label for="register"><i class="icon-list"></i> {{ trans('templates.register') }}</label>
								<label class="switch switch-primary">
								      <input id="register" name="register" type="checkbox" 
									@if (isset($data->settings->register) && $data->settings->register)
										checked
									@endif
									class="switch-input">
								      <span data-off="Off" data-on="On" class="switch-label"></span>
								      <span class="switch-handle"></span>
								    </label>
							</div>
							<div class="col-sm-3">
								<label for="common"><i class="icon-list"></i> {{ trans('templates.common') }}</label>
								<label class="switch switch-primary">
								      <input id="common" name="common" type="checkbox" 
									@if (isset($data->settings->common) && $data->settings->common)
										checked
									@endif
									class="switch-input">
								      <span data-off="Off" data-on="On" class="switch-label"></span>
								      <span class="switch-handle"></span>
								    </label>
							</div>
						</div>
						
					
					</fieldset>

					<div class="clearfix">
						<div class="col-lg-12 inh_nopadding">
							<div class="form-actions clearfix text-center">
								<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('templates.sending') }}">{{ trans('templates.savechanges') }}</button>
							</div>
						</div>
					</div>
					
				</form>
				
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->
@stop


@section('specific-javascript-plugins')
@javascripts('cr_template')
@stop


@section('custom-javascript')
<script>
var CR_TEMPLATES = {{ json_encode($templates) }};
$_TEMPLATE = {{ json_encode( $data->fields ) }};
console.log($_TEMPLATE);
$(document).ready(function(){
	$($_TEMPLATE).each(function(k, field){
		Templates.addFieldByObject( field, '{{isset($data->settings->date) ? $data->settings->date : ''}}' );
	});
});
$(document).on('click', '.template-date-filter', function(e){
    $('.template-date-filter').removeClass("active");
    $(e.target).addClass("active");
});
$(document).on('change', '#geolocation', function(e){
	var visible = $('#visible');

    if ( this.checked )
    {
    	visible.prop('disabled', false);
    }
    else
    {
    	visible.attr('checked', false)
    		.prop('disabled', true);
    }
});
</script>
@stop
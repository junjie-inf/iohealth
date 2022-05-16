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
					<a href="{{ URL::route('alert.edit', $alert->id) }}"><i class="icon-pencil"></i><span>{{trans('alerts.edit')}}</span></a>
					<a href="{{ URL::route('alert.index') }}"><i class="icon-arrow-left"></i><span>{{trans('alerts.back')}}</span></a>
				</div>
			</div>
			<div class="box-content">
				<form class="form-horizontal form-edit fix-margins" action="" data-type="alert" data-id="{{ $alert->id }}" data-redirect-url="{{ URL::route('alert.index') }}">
				<input type="hidden" name="_method" value="PUT" />

					<fieldset class="col-sm-12">

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="title">{{trans('alerts.title')}}</label>
									<div class="controls">
										<input class="form-control" id="title" name="title" type="text" value="{{ $alert->title }}" required readonly />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="template-select">{{trans('alerts.template')}} <i class="icon-spinner icon-spin" style="display:none" ></i></label>
									<div class="controls">
										<select id="template-select" name="template" class="form-control" data-rel="chosen" data-placeholder="Choose a Template..." disabled>
											<option></option>
											@foreach( $templates as $template )
												<option value="{{ $template->id }}" 
													@if( $template->id == $alert->conditions->template)
														selected
													@endif>{{ $template->title }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="conditions" >{{trans('alerts.conditions')}}</label>
									<div class="controls">
										<input class="form-control" id="conditions" name="conditions" type="text" value="{{ $alert->conditions->condition }}" required readonly/>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<label class="control-label" for="actions">{{trans('alerts.actions')}}</label>
									<div class="controls">
										<select id="actions-select" name="actions" class="form-control" data-rel="chosen" data-placeholder="Choose a Template..." disabled>
											<option></option>
											@foreach( $templates as $template )
												<option value="{{ $template->id }}"
													@if( $template->id == $alert->actions[0]->template)
														selected
													@endif>{{ $template->title }}</option>
											@endforeach
										</select>
										<fieldset class="col-sm-12 insert-template well">
											{{ TemplateBuilder::with(Template::find($alert->actions[0]->template)->fields)->answer($alert->actions[0]->fields)->readonly()->build() }}
										</fieldset>
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
{-- Añado cr_template para usar el parseado de json del report --}
@javascripts('cr_dashitem')
@javascripts('cr_expreditor')
@stop


@section('custom-javascript')
	{{-- inline scripts related to this page --}}
	<script>

		var CR_TEMPLATES = {{ json_encode(array_combine(array_column($templates->toArray(),'id'), $templates->toArray())) }};
		var CR_TEMPLATE = [];

		$(document).ready(function(){
		
			CR_TEMPLATE.push(CR_TEMPLATES[$('#template-select').val()]);

			ExprEditor.viewExpr($('#conditions'), CR_TEMPLATE);

			$('.insert-template > .form-group :text, .insert-template > .form-group textarea').each(function(){
				ExprEditor.viewExpr(this, CR_TEMPLATE);
			})

			
		});

	</script>
@stop
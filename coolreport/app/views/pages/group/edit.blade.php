@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-pencil"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('group.show', $data->id) }}"><i class="icon-arrow-left"></i><span>{{ trans('groups.back') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<form class="form-horizontal form-edit" action="" data-type="group" data-id="{{ $data->id }}">
					<input type="hidden" name="_method" value="PUT" />
					
					<fieldset class="col-sm-12">
						
						<div class="form-group">
							<label class="control-label" for="title">{{ trans('groups.title') }}</label>
							<div class="controls">
								<input class="form-control" id="title" name="title" type="text" value="{{ $data->title }}" required autofocus />
							</div>
						</div>	
					
					</fieldset>

					<div class="clearfix">
						<div class="col-lg-12 inh_nopadding">
							<div class="form-actions clearfix text-center">
								<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('groups.sending') }}">{{ trans('groups.savechanges') }}</button>
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
@stop


@section('custom-javascript')
	{{-- inline scripts related to this page --}}
@stop
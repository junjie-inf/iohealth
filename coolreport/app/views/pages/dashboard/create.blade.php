@extends('templates.default')

@section('title', trans('sections.dashboard.create'))

@section('content')

	{{-- [Modal] View Report --}}
	@include( 'templates.mustache.dashboard' )

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-plus"></i><span class="break"></span>{{ trans('sections.dashboard.create') }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('dashboard.index') }}"><i class="icon-arrow-left"></i><span>{{ trans('dashboard.back') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				<form class="form-horizontal form-dashboard-store fix-margins" action="" data-type="template">
					<fieldset class="col-sm-12">
						
						<div class="form-group">
							<label class="control-label" for="title">{{ trans('dashboard.title') }}</label>
							<div class="controls">
								<input class="form-control" id="title" name="title" type="text" value="" required autofocus />
							</div>
						</div>
					
					</fieldset>
					
					<fieldset class="col-sm-12">
						<div class="form-group">
							<label class="control-label">{{ trans('dashboard.layout') }}</label>
						</div>
						
						<div class="well">
							<div class="dashboard-container"></div>

							<div class="controls">
								<div class="btn-group">
									<button class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
										<i class="icon-plus"></i> {{ trans('dashboard.addrow') }} <span class="caret"></span>
									</button>
									<ul class="dropdown-menu" id="db-add-field-dropdown">
										<li><a href="javascript:void(0)" class="action-add-row" data-cols="1">1 {{ trans('dashboard.column') }}</a></li>
										<li><a href="javascript:void(0)" class="action-add-row" data-cols="2">2 {{ trans('dashboard.columns') }}</a></li>
										<li><a href="javascript:void(0)" class="action-add-row" data-cols="3">3 {{ trans('dashboard.columns') }}</a></li>
										<li><a href="javascript:void(0)" class="action-add-row" data-cols="4">4 {{ trans('dashboard.columns') }}</a></li>
									</ul>
								</div>
							</div>
							
						</div>
						
					</fieldset>

					<div class="clearfix">
						<div class="col-lg-12 inh_nopadding">
							<div class="form-actions clearfix text-center">
								<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('dashboard.sending') }}">{{ trans('dashboard.savechanges') }}</button>
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
@javascripts('cr_dashboard')
@stop


@section('custom-javascript')
<script>
var DASH_ITEMS = [
@foreach( $dashitems as $item )
	{'id' : '{{ $item->id }}', 'title' : '{{ $item->title }}'},
@endforeach
];
</script>
@stop
@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-dashboard"></i><span class="break"></span>{{ trans('sections.dashboard.index') }}</h2>
				<div class="box-icon">
					<a href="{{ URL::to('dashboard/create') }}"><i class="icon-plus"></i><span>{{ trans('sections.dashboard.create') }}</span></a>
				</div>
			</div>
			{{ Util::dashboard() }}
		</div>
	</div><!--/col-->

</div><!--/row-->

@stop


@section('specific-javascript-plugins')
@javascripts('cr_dashboard')
@stop


@section('custom-javascript')
	{{-- inline scripts related to this page --}}
@stop
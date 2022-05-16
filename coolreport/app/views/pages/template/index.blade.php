@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
				<div class="box-icon">
					<a href="{{ URL::to('template/create') }}"><i class="icon-plus"></i><span>{{ trans('sections.template.create') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<table class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">

					<thead>
						<tr>
							<th>{{ trans('templates.title') }}</th>
							<th class="hidden-xs hidden-sm">{{ trans('templates.user') }}</th>
							<th class="hidden-xs hidden-sm width-min default_sort" data-sort-dir="desc">{{ trans('templates.createdat') }}</th>
							<th class="width-50 text-center">{{ trans('templates.reports') }}</th>
							<th class="width-min no_sort"></th>
						</tr>
					</thead>

					<tbody>

						@if( $data )
							@foreach( $data as $obj )
								<tr data-id="{{ $obj->id }}" data-type="template" class="action-data">
									<td><a href="{{ URL::route('template.show', $obj->id) }}">{{{ $obj->title }}}</a></td>
									<td class="hidden-xs hidden-sm"><a href="{{ $obj->user->getUrl() }}">{{ $obj->user->getFullname() }}</a></td>
									<td class="hidden-xs hidden-sm inh_nowrap">{{ $obj->getRawAttribute('created_at') }}</td>
									<td class="text-center"><span class="badge badge-info">{{ $obj->reports()->count() }}</span></td>

									@component('components.table_crud', array('url' => URL::to('template/' . $obj->id), 'obj' => $obj ))
								</tr>
							@endforeach
						@endif

					</tbody>

				</table>
				
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->

@stop


@section('specific-javascript-plugins')
@javascripts('cr_template')
@stop


@section('custom-javascript')
@stop
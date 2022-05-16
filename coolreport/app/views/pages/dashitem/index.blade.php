@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span>{{ trans('sections.dashitem.index') }}</h2>
				<div class="box-icon">
					<a href="{{ URL::to('dashitem/create') }}"><i class="icon-plus"></i><span>{{ trans('sections.dashitem.create') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<table class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">

					<thead>
						<tr>
							<th>{{ trans('dashitems.dashitem') }}</th>
							<th>{{ trans('dashitems.user') }}</th>
							<th class="width-min default_sort" data-sort-dir="desc">{{ trans('dashitems.createdat') }}</th>
							<th class="width-min no_sort"></th>
						</tr>
					</thead>

					<tbody>

						@if( $data )
							@foreach( $data as $obj )
								<tr data-id="{{ $obj->id }}" data-type="dashitem" class="action-data">
									<td><a href="{{ URL::route('dashitem.show', $obj->id) }}">{{ $obj->title }}</a></td>
									
									<td><a href="{{ $obj->user->getUrl() }}">{{ $obj->user->getFullname() }}</a></td>
									<td class="inh_nowrap">{{ $obj->getRawAttribute('created_at') }}</td>

									@component('components.table_crud', array('url' => URL::to('dashitem/' . $obj->id), 'obj' => $obj ))
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
@stop


@section('custom-javascript')
	{{-- inline scripts related to this page --}}
@stop
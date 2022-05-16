@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-group"></i><span class="break"></span>{{ trans('sections.group.index') }}</h2>
				@if( Auth::user()->hasPermission('GRANT_GROUPS') )
				<div class="box-icon">
					<a href="{{ URL::to('group/create') }}"><i class="icon-plus"></i><span>{{ trans('sections.group.create') }}</span></a>
				</div>
				@endif
			</div>
			<div class="box-content">
				
				<table class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">

					<thead>
						<tr>
							<th>{{ trans('groups.title') }}</th>
							<th class="width-min default_sort" data-sort-dir="desc">{{ trans('groups.createdat') }}</th>
							<th class="width-50 text-center"><i class="icon-user"></i></th>
							<th class="width-min no_sort"></th>
						</tr>
					</thead>

					<tbody>

						@if( $data )
							@foreach( $data as $obj )
								
								<tr data-id="{{ $obj->id }}" data-type="group" class="action-data">
									<td><a href="{{ URL::route('group.show', $obj->id) }}">{{ $obj->title }}</a></td>
									<td class="inh_nowrap">{{ $obj->getRawAttribute('created_at') }}</td>
									<td class="text-center">
										<a href="{{ URL::to('group/' . $obj->id) }}">
											<span class="badge badge-info">{{ $obj->users->count() }}</span>
										</a>
									</td>

									@component('components.table_crud', array('url' => URL::to('group/' . $obj->id), 'obj' => $obj ))
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
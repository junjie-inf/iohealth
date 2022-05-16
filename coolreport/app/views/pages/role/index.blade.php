@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-key"></i><span class="break"></span>{{ trans('sections.role.index') }}</h2>
				@if( Auth::user()->admin )
				<div class="box-icon">
					<a href="{{ URL::to('role/create') }}"><i class="icon-plus"></i><span>{{ trans('sections.role.create') }}</span></a>
				</div>
				@endif
			</div>
			<div class="box-content">
				
				<table class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">

					<thead>
						<tr>
							<th>{{ trans('role.title') }}</th>
							<th class="width-min default_sort" data-sort-dir="desc">{{ trans('role.createdat') }}</th>
							<th class="width-50 text-center"><i class="icon-user"></i></th>
							<th class="width-min no_sort"></th>
						</tr>
					</thead>

					<tbody>

						@if( $data )
							@foreach( $data as $obj )
								
								<tr data-id="{{ $obj->id }}" data-type="role" class="action-data">
									<td><a href="{{ URL::route('role.show', $obj->id) }}">{{ $obj->title }}</a></td>
									<td class="inh_nowrap">{{ $obj->getRawAttribute('created_at') }}</td>
									<td class="text-center">
										<a href="{{ URL::to('role/' . $obj->id) }}">
											<span class="badge badge-info">{{ $obj->users->count() }}</span>
										</a>
									</td>

									@component('components.table_crud', array('url' => URL::to('role/' . $obj->id), 'obj' => $obj ))
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
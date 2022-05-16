@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-group"></i><span class="break"></span>{{ $data->title }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('group.index') }}"><i class="icon-arrow-left"></i><span>{{ trans('groups.back') }}</span></a>
					@if( $data->editable )
						<a href="{{ URL::route('group.edit', $data->id) }}"><i class="icon-pencil"></i><span>{{ trans('groups.edit') }}</span></a>
					@endif
				</div>
			</div>
		</div>
		
	</div><!--/col-->

</div><!--/row-->

<div class="row">		
	<div class="col-lg-12">
		
		<div class="box">
			<div class="box-header level-2 blue">
				<h2><i class="icon-user"></i><span class="break"></span>{{ trans('groups.usersingroup') }}</h2>
			</div>
			
			<div class="box-content">
				
				<table class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">

					<thead>
						<tr>
							<th>{{ trans('groups.name') }}</th>
							<th>{{ trans('groups.email') }}</th>
							<th>{{ trans('groups.role') }}</th>
							<th class="width-min">{{ trans('groups.registered') }}</th>
							<th class="width-min inh_nowrap">{{ trans('groups.lastlogin') }}</th>
							<th class="width-min no_sort"></th>
						</tr>
					</thead>

					<tbody>

						@if( $data->users->count() > 0 )
							@foreach( $data->users as $obj )
								<tr data-id="{{ $obj->id }}" data-type="user" class="action-data">
									<td><a href="{{ $obj->getUrl() }}">{{ $obj->getFullname() }}</a></td>
									<td>{{ $obj->email }}</td>
									<td>
										@if( $obj->role->readable )
											<a href="{{ URL::route('role.show', $obj->role->id) }}">{{ $obj->role->title }}</a>
										@else
											{{ $obj->role->title }}
										@endif
									</td>
									<td class="inh_nowrap">{{ $obj->created_at }}</td>
									<td class="inh_nowrap">{{ $obj->lastlogin }}</td>

									@component('components.table_crud', array('url' => URL::to('user/' . $obj->id), 'obj' => $obj ))
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
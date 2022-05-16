@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-key"></i><span class="break"></span>{{ $data->title }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('role.index') }}"><i class="icon-arrow-left"></i><span>{{ trans('role.back') }}</span></a>
					@if( $data->editable )
						<a href="{{ URL::route('role.edit', $data->id) }}"><i class="icon-pencil"></i><span>{{ trans('role.edit') }}</span></a>
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
				<h2><i class="icon-key"></i><span class="break"></span>{{ trans('role.permissionsrole') }}</h2>
				<div class="box-icon">
					@if( $data->editable )
						<a href="{{ URL::route('role.edit', $data->id) }}"><i class="icon-pencil"></i><span>{{ trans('role.edit') }}</span></a>
					@endif
				</div>
			</div>
			
			<div class="box-content">
				
				<div class="row">
					
					<table class="table vertical-middle" style="width:auto; margin:0 auto">
						<thead>
							<tr>
								<th class="text-right">{{ trans('role.permission') }}</th>
								<th class="text-center width-min">{{ trans('role.all') }}</th>
								<th class="text-center width-min">{{ trans('role.group') }}</th>
								<th class="text-center width-min">{{ trans('role.own') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach( Config::get('local.permission.types') as $permission )
								@php( $rolePermission = $data->getPermissionValue($permission) )
								<tr>
									<td class="text-right">{{ ucwords(strtolower(str_replace('_', ' ', $permission))) }}</td>
									<td class="text-center">{{ $rolePermission === 'ALL' ? '<i class="icon-ok darkGreen"></i>' : '<i class="icon-remove red"></i>' }}</td>
									<td class="text-center">{{ $rolePermission === 'GROUP' ? '<i class="icon-ok darkGreen"></i>' : '<i class="icon-remove red"></i>' }}</td>
									<td class="text-center">{{ $rolePermission === 'OWN' ? '<i class="icon-ok darkGreen"></i>' : '<i class="icon-remove red"></i>' }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				
				</div>
					
			</div>
			
		</div>
	</div><!--/col-->

</div><!--/row-->

<div class="row">		
	<div class="col-lg-12">
		
		<div class="box">
			<div class="box-header level-2 blue">
				<h2><i class="icon-user"></i><span class="break"></span>{{ trans('role.userswithrole') }}</h2>
			</div>
			
			<div class="box-content">
				
				<table class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">

					<thead>
						<tr>
							<th>{{ trans('role.name') }}</th>
							<th>{{ trans('role.email') }}</th>
							<th>{{ trans('role.group') }}</th>
							<th class="width-min default_sort">{{ trans('role.registered') }}</th>
							<th class="width-min inh_nowrap">{{ trans('role.lastlogin') }}</th>
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
										@if( $obj->group->readable )
											<a href="{{ URL::route('group.show', $obj->group->id) }}">{{ $obj->group->title }}</a>
										@else
											{{ $obj->group->title }}
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
	<script>
		$(document).ready(function(){
			//$('.btn-group').button();
		});
	</script>
@stop
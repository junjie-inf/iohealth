@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-user"></i><span class="break"></span>{{ trans('sections.user.index') }}</h2>
				@if( Auth::user()->hasPermission('GRANT_USERS') )
				<div class="box-icon">
					<a href="{{ URL::to('user/create') }}"><i class="icon-plus"></i><span>{{ trans('sections.user.create') }}</span></a>
				</div>
				@endif
			</div>
			<div class="box-content">
				
				<table class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">

					<thead>
						<tr>
							<th>{{ trans('users.name') }}</th>
							<th>{{ trans('users.email') }}</th>
							<th>{{ trans('users.role') }}</th>
							<th>{{ trans('users.group') }}</th>
							<th class="width-min default_sort" data-sort-dir="desc">{{ trans('users.registered') }}</th>
							<th class="width-min inh_nowrap">{{ trans('users.lastlogin') }}</th>
							<th class="width-min no_sort"></th>
						</tr>
					</thead>

					<tbody>
						
						@if( $data )
							@foreach( $data as $obj )
								<tr data-id="{{ $obj->id }}" data-type="user" class="action-data">
									<td><a href="{{ URL::route('user.show', $obj->id) }}">{{ $obj->getFullname() }}</a><a href="{{ $obj->getUrl() }}" class="pull-right"><i class="icon-search"></i></a></td>
									<td>{{ $obj->email }}</td>
									<td>
										@if( $obj->role->readable )
											<a href="{{ URL::route('role.show', $obj->role->id) }}">{{ $obj->role->title }}</a>
										@else
											{{ $obj->role->title }}
										@endif
									</td>
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
@stop
@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-user"></i><span class="break"></span>{{ $data->getFullname() }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('user.index') }}"><i class="icon-arrow-left"></i><span>{{ trans('users.back') }}</span></a>
					@if( $data->editable )
						<a href="{{ URL::route('user.edit', $data->id) }}"><i class="icon-pencil"></i><span>{{ trans('users.edit') }}</span></a>
					@endif
				</div>
			</div>
			
			<div class="box-content">
	
				<div class="row">

					<div class="col-sm-7">

						<dl class="dl-horizontal">
							<dt>{{ trans('users.firstname') }}</dt>
							<dd>{{ $data->firstname }}</dd>

							<dt>{{ trans('users.surname') }}</dt>
							<dd>{{ $data->surname }}</dd>

							<dt>{{ trans('users.email') }}</dt>
							<dd>{{ $data->email }}</dd>

							<dt>{{ trans('users.role') }}</dt>
							@if( $data->role->readable )
								<dd><a href="{{ URL::route('role.show', $data->role->id) }}">{{ $data->role->title }}</a></dd>
							@else
								<dd>{{ $data->role->title }}</dd>
							@endif

							<dt>{{ trans('users.group') }}</dt>
							@if( $data->group->readable )
								<dd><a href="{{ URL::route('group.show', $data->group->id) }}">{{ $data->group->title }}</a></dd>
							@else
								<dd>{{ $data->group->title }}</dd>
							@endif

							<dt>{{ trans('users.createdat') }}</dt>
							<dd>{{ $data->created_at }}</dd>

							<dt>{{ trans('users.lastlogin') }}</dt>
							<dd>{{ $data->lastlogin ? $data->lastlogin : 'Never' }}</dd>

							<dt>{{ trans('users.gossipmode') }}</dt>
							<dd>
							@if( $data->gossip )
								<span class="label label-success">Yes</span>
							@else
								<span class="label label-warning">No</span>
							@endif
							</dd>
							<dt>{{ trans('users.karma') }}</dt>
							<dd>{{ $data->getReputation() }}</dd>
						</dl>
						
					</div>

					<div class="col-sm-2">
						<a class="quick-button disabled margin-top-20" href="javascript:void(0)">
							<i class="icon-file-text-alt"></i>
							<p>{{ trans('users.reports') }}</p>
							<span class="notification blue">{{ $data->reports->count() }}</span>
						</a>
					</div>

					<div class="col-sm-2">
						<a class="quick-button disabled margin-top-20" href="javascript:void(0)">
							<i class="icon-comment-alt"></i>
							<p>{{ trans('users.comments') }}</p>
							<span class="notification green">{{ $data->comments->count() }}</span>
						</a>
					</div>
					
				</div>
				
			</div>
			
		</div>
	</div><!--/col-->

</div><!--/row-->



{{-- USER REPORTS --}}
<div class="row">		
	<div class="col-lg-12">
		
		<div id="tabContent" class="tab-content inh_nopadding">
			<div class="tab-pane active" id="user_reports">

				<div class="box">
					<div class="box-header level-2 blue">
						<h2><i class="icon-user"></i><span class="break"></span>{{ trans('users.userreports') }}</h2>
					</div>

					<div class="box-content">


						<table id="table_reports" class="table table-striped table-bordered table-condensed bootstrap-datatable vertical-middle">
							<thead>
								<tr>
									<th>{{ trans('users.report') }}</th>
									<th>{{ trans('users.template') }}</th>
									<th>{{ trans('users.location') }}</th>
									<th class="width-min default_sort" data-sort-dir="desc">{{ trans('users.createdat') }}</th>
									<th class="width-50 text-center"><i class="icon-comment-alt"></i></th>
									<th class="width-min no_sort"></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>

					</div>
				</div>
			</div>
			
		</div><!--/tabContent-->

	</div><!--/col-->
</div><!--/row-->



{{-- ACTIVE REPORTS --}}
<div class="box">
	<div class="box-header level-2 blue">
		<h2><i class="icon-bullhorn"></i><span class="break"></span>{{ trans('users.activenotifications') }}</h2>
	</div>
	<div class="box-content">
		
		<table class="table table-striped table-bordered table-condensed vertical-middle">
			<tr>
				<th>{{ trans('users.type') }}</th>
				<th>{{ trans('users.title') }}</th>
				<th style="width:10px"></th>
			</tr>

			@if( ! $data->followed->isEmpty() )
				@foreach( $data->followed as $followed )
					<tr>
						<td>{{ $followed->followable_type }}</td>

						<td>
							<a href="{{ URL::route(lcfirst($followed->followable_type).'.show', $followed->followable_id) }}">
								@if( $followed->followable_type == 'User' )
									{{ $followed->followable->getFullname() }}
								@else
									{{ $followed->followable->title }}
								@endif
							</a>
						</td>

						<td class="text-right">
							<a class="btn btn-danger action-unfollow" href="javascript:void(0)"
								data-followable-type="{{ lcfirst($followed->followable_type) }}"
								data-followable-id="{{ $followed->followable_id }}"
								data-toggle="false"
								data-loading-text="<i class='icon-spinner icon-spin'></i>">
									<i class="icon-trash"></i>
							</a>
						</td>
					</tr>
				@endforeach
			@else
				<tr>
					<td colspan="3">{{ trans('users.nodataavailable') }}</td>
				</tr>
			@endif

		</table>
		
	</div>
</div>

@stop


@section('specific-javascript-plugins')
@stop


@section('custom-javascript')
	@include( 'templates.mustache.datatables' )
	<script>
		$(document).ready(function() {
			$('#table_reports').dataTable({
				serverSide: true,
				ajax: '{{URL::route('report.table')}}?mode=user&user={{$data->id}}',
				dom: "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-12'i><'col-lg-12 center'p>>",
				columns: [
					{ 
						name: 'id', 
						data: function( row ) { 
							return '<a href="' + row.DT_RowData.url + '">' + row.title + '</a>'; 
						}
					},
					{
						name: 'template_id',
						data: 'template' 
					},
					{
						name: 'address',
						data: 'address'
					},
					{
						name: 'created_at',
						className: 'inh_nowrap',
						data: 'created_at'
					},
					{
						name: 'comments',
						orderable: false,
						className: 'text-center',
						data: function( row ) {
							return '<a href="{{ URL::route('report.{report}.comment.index', '_id') }}">'.replace('_id', row.DT_RowData.id) +
									'<span class="badge badge-success">' + row.comments + '</span>' +
								'</a>';
						}
					},
					{
						name: 'crud',
						className: 'inh_nowrap',
						orderable: false,
						data: function( row ) {
							return Handlebars.compile( $("#tpl-table-crud-report").html() )({
								id : row.DT_RowData.id,
								url : row.DT_RowData.url,
								followed : row.DT_RowData.followed,
								removable : row.DT_RowData.removable,
								editable: row.DT_RowData.editable
							});
						}
					},
				]
			});
		});
	</script>
@stop
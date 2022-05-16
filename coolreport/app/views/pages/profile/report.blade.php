@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
				<div class="box-icon">
					<!--<a href="{{ URL::to('report/import') }}"><i class="icon-arrow-up"></i><span>{{ trans('sections.report.import') }}</span></a>-->
					<a href="{{ URL::to('report/create') }}"><i class="icon-plus"></i><span>{{ trans('sections.report.create') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<table id="dataTable" class="table table-striped table-bordered table-condensed bootstrap-datatable vertical-middle">

					<thead>
						<tr>
							<th>{{trans('reports.report')}}</th>
							<th>{{trans('reports.template')}}</th>
							<th>{{trans('reports.location')}}</th>
							<th class="width-min default_sort" data-sort-dir="desc">{{trans('reports.createdat')}}</th>
							<th class="width-50 text-center"><i class="icon-comment-alt"></i></th>
							<th class="width-min no_sort"></th>
						</tr>
					</thead>

					<tbody>
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
	@include( 'templates.mustache.datatables' )
	<script>
		$(document).ready(function() {
			$('#dataTable').dataTable({
				serverSide: true,
				ajax: '{{URL::route('report.table')}}?mode=my',
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
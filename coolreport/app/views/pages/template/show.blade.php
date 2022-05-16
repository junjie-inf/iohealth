@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span>{{ $data->title }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('template.index') }}"><i class="icon-arrow-left"></i><span>{{ trans('templates.back') }}</span></a>
					@if( $data->editable )
						<a href="{{ URL::route('template.edit', $data->id) }}"><i class="icon-pencil"></i><span>{{ trans('templates.edit') }}</span></a>
					@endif
				</div>
			</div>
			
			<div class="box-content">
	
				<div class="row">
					<div class="col-sm-12">

							<dl class="dl-horizontal">
								<dt>{{ trans('templates.title') }}</dt>
								<dd>{{ $data->title }}</dd>

								<dt>Author</dt>
								<dd>
									<a href="{{ URL::route('user.show', $data->user->id) }}" title="View extended profile">{{ $data->user->getFullname() }}</a>
									&nbsp;&nbsp;<a href="{{ $data->user->getUrl() }}" class="inh_small" title="View public profile">{{ trans('templates.publicprofile') }} <i class="icon-external-link"></i></a>
								</dd>

								<dt>{{ trans('templates.createdat') }}</dt>
								<dd>{{ $data->created_at }}</dd>

								<dt>{{ trans('templates.updatedat') }}</dt>
								<dd>{{ $data->updated_at }}</dd>
								<br>

								<dt><dd>
								<a href="{{ URL::route('template.clean', $data->id) }}"><button class="btn btn-mini btn-success"><i class="icon-spinner"></i> {{ trans('templates.cleantemplate') }}</button></a>
								</dd></dt>
							</dl>
						
					</div>
				</div>
	
				<hr />
				
				<h3 class="inh_bold">{{ trans('templates.preview') }}</h3>
	
				<div class="row">
					<div class="col-sm-12">
				
						<div class="well">
							{{ $form }}
						</div>
						
					</div>
				</div>
				
			</div>
			
		</div>
	</div><!--/col-->

</div><!--/row-->

<div class="row">	
	<div class="col-lg-12">
		
		<div class="box">
			<div class="box-header level-2 blue">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span>{{ trans('templates.reportswithtemplate') }}</h2>
			</div>
			
			<div class="box-content">
				
				<table id="dataTable" class="table table-striped table-bordered table-condensed bootstrap-datatable vertical-middle">

					<thead>
						<tr>
							<th>{{ trans('templates.title') }}</th>
							<th>{{ trans('templates.location') }}</th>
							<th class="width-min default_sort" data-sort-dir="desc">{{ trans('templates.createdat') }}</th>
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

@section('specific-css-plugins')
@stop

@section('specific-javascript-plugins')
@javascripts('cr_template')
@stop


@section('custom-javascript')
<script>
$(document).ready(function(){
	/* ---------- Choosen ---------- */
	$('[data-rel="chosen"],[rel="chosen"]').chosen();

	/* ---------- Auto Height texarea ---------- */
	$('textarea').autosize();
});
</script>
	@include( 'templates.mustache.datatables' )
	<script>
		$(document).ready(function() {
			$('#dataTable').dataTable({
				serverSide: true,
				ajax: '{{URL::route('report.table')}}?mode=all&template={{$data->id}}',
				dom: "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-12'i><'col-lg-12 center'p>>",
				columns: [
					{ 
						name: 'id', 
						data: function( row ) { 
							return '<a href="' + row.DT_RowData.url + '">' + row.title + '</a>'; 
						}
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
@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

@if( Input::get('template') == 1 )
<div class="row">	
	<div class="col-lg-1 col-md-2 col-xs-6" style="float: right;padding-bottom: 10px;">
		<a href="{{ URL::to('alert') }}" class="quick-button-small">
			<i class="icon-cogs"></i>
			<p>Triggers</p>
			<span class="notification green">{{ Alert::count() }}</span>
		</a>
	</div>
</div>
@endif

<div class="row">		
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2>
					<i class="icon-file-text-alt"></i>
				@if (Lang::has('sections.template.' . Input::get('template')))
					<span class="break">{{ trans_choice('sections.template.' . Input::get('template'), 2) }}</span></h2>
				@else 
					<span class="break">{{$title}}</span></h2>
				@endif
				@if (Input::get('template') == '2')
				<div class="box-icon">

				</div>
				@elseif (Input::get('template') == '11')
				<div class="box-icon">
					<a href="{{ Input::get('template') ? URL::to('report/create') . '/' . Input::get('template') : URL::to('report/create') }}" data-toggle="tooltip" data-placement="top" title="{{ trans('sections.report.create') . ' ' . (Lang::has('sections.template.' . Input::get('template'))? trans_choice('sections.template.' . Input::get('template'), 1) : $title)}}"><i class="icon-plus"></i><span>{{ trans('sections.report.assing') . ' ' . (Lang::has('sections.template.' . Input::get('template'))? trans_choice('sections.template.' . Input::get('template'), 1) : $title)}}</span></a>
				</div>
				@else
				<div class="box-icon">
					<a href="{{ Input::get('template') ? URL::to('report/create') . '/' . Input::get('template') : URL::to('report/create') }}" data-toggle="tooltip" data-placement="top" title="{{ trans('sections.report.create') . ' ' . (Lang::has('sections.template.' . Input::get('template'))? trans_choice('sections.template.' . Input::get('template'), 1) : $title)}}"><i class="icon-plus"></i><span>{{ trans('sections.report.create') . ' ' . (Lang::has('sections.template.' . Input::get('template'))? trans_choice('sections.template.' . Input::get('template'), 1) : $title)}}</span></a>
				</div>
				@endif


			</div>
			<hr/>
			<div class="box-content">
				<table id="dataTable" class="table table-striped table-bordered table-condensed bootstrap-datatable vertical-middle">
					<thead>
						<tr>
							@if( Input::has('template') ) 
								@foreach ($campos as $campo)
									<th>{{ $campo['label'] }}</th>
								@endforeach
							@else
								<th>{{trans('reports.report')}}</th>
								<th class="hidden-sm hidden-xs">{{ trans('reports.template') }}</th>
							@endif
							<th>{{ trans('reports.user') }}</th>
							<th class="width-min default_sort" data-sort-dir="desc">{{ trans('reports.createdat') }}</th>
							<th class="width-50 text-center"><i class="icon-comment-alt"></i></th>
							<th class="width-min no_sort text-center"></th>							
						</tr>
					</thead>
					<tbody></tbody>
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
	@if(Input::get('template') == 39) {
		@include( 'templates.mustache.datatablesProbe' )
	} @else {
		@include( 'templates.mustache.datatables' )
	}
	@endif

	<script>
		$(document).ready(function() {
			var template = '{{ Input::get('template') }}';
			var datum = '{{ Input::get('datum') }}';
			var equals = '{{ Input::get('equals') }}';

			// Permite filtrar los reports en base a un template
			var template_filter = (typeof template !== '') ? '&template=' + template : '';
			
			template_filter += (typeof datum !== '') ? '&datum=' + datum : '';
			template_filter += (typeof equals !== '') ? '&equals=' + equals : '';


			$('#dataTable').dataTable({
				serverSide: true,
				ajax: '{{URL::route('report.table')}}?mode=all' + template_filter,
				oLanguage: {{trans('datatables.language')}},
				// oLanguage: {sSearch: ""}, 
				dom: "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-12'i><'col-lg-12 center'p>>",
				columns: [
					@if( Input::has('template') ) 
						@foreach ($campos as $key => $campo)
							{ 
								@if ($campo['type'] == 'decimal' || $campo['type'] == 'number' || $campo['type'] == 'range')
									name: "(datum->'{{ $key }}'->>'value')::Integer",
								@else
									name: "datum->'{{ $key }}'->>'value'",
								@endif

								@if(Input::get('template') == '22' && Auth::user()->role_id == 3)
								data: function( row )  {
									return row.campos['{{ $key }}']? (row.campos['{{ $key }}']['url']? '<a class="linked-report">' + row.campos['{{ $key }}']['value'] + '</a>' : row.campos['{{ $key }}']['value']): "";
								}
								@else
									data: function( row )  {
									return row.campos['{{ $key }}']? (row.campos['{{ $key }}']['url']? '<a class="linked-report" href="' + row.campos['{{ $key }}']['url'] + '" class="blue">' + row.campos['{{ $key }}']['value'] + '</a>' : row.campos['{{ $key }}']['value']): "";
								}
								@endif
							},
						@endforeach
					@else
						{ 
							name: 'id', 
							data: function( row ) { 
								return '<a href="' + row.DT_RowData.url + '" class="blue">' + row.title + '</a>'; 
							}
						},
						{
							name: 'template_id',
							data: 'template' 
						},
					@endif
					
					@if( Input::get('template') == 1 && false)
						{
							name: 'state',
							data: 'state',
							orderable: false
						},
					@endif
					{
						name: 'user_id',
						data: 'user',
						data: function( row )  {
							return '<a href="' + row.DT_RowData.user_url + '" class="linked-user blue">' + row.user + '</a>';
						}
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
						className: 'inh_nowrap text-center',
						orderable: false,
						data: function( row ) {
							return Handlebars.compile( $("#tpl-table-crud-report").html() )({
								id : row.DT_RowData.id,
								url : row.DT_RowData.url,
								followed : row.DT_RowData.followed,
								removable : row.DT_RowData.removable,
								editable: row.DT_RowData.editable,
								dashboardsensorURL: $SITE_PATH + 'waterTimeSeries/' + row.DT_RowData.id,
								epanetanalysisURL: $SITE_PATH + 'epanet/' + row.DT_RowData.id,
								actionstooltips: {{ json_encode(trans('sections.actions')) }},
							});
						}
					},
				],
				initComplete: function(){
						/*$('.dataTables_filter input').addClass('form-control');*/
						$('#dataTable').on('click', 'tbody tr', function(e) {
							//To avoid propagation if the click is in an action button
							if ($(event.target).is('i') || $(event.target).is('.btn')){
						         return;
						    } else {
							   	if (e.metaKey){
							    	window.open($(this).find('.btn-success')[0].href)
							   	} else {
							  		window.location.href = $(this).find('.btn-success')[0].href;
								}
							}
						});

						$('[data-toggle="tooltip"]').tooltip({ trigger : 'hover' });
					}
			});
		});
	</script>
@stop
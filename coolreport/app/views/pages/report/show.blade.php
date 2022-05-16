@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">

	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span><span class="hidden-sm hidden-xs">{{ trans('sections.report.show') }}</span></h2>
			</div>
			
			<div class="box-content">				
				<ul class="nav tab-menu nav-tabs">
					@if( $data->editable )
						<li><a href="{{ URL::route('report.edit', $data->id) }}"><i class="icon-pencil"></i> {{trans('reports.edit')}}</a></li>
					@endif

					@if( Auth::user()->role_id == '3' && (DB::select(DB::raw("SELECT id FROM reports WHERE template_id=10 and deleted_at IS NULL AND datum->'5889efb0944a90'->>'value' = '".Auth::user()->id."'"))[0]->id != $data->id) )
					<li><a href="{{ URL::previous() }}"><i class="icon-arrow-left"></i>{{ trans('reports.backtolist') }}</a></li>
					@endif

					<!--<li><a href="{{ URL::route('report.{report}.comment.index', $data->id) }}">{{trans('reports.comments')}}</a></li>
					-->
					<li class="active"><a href="javascript: void(0)">{{trans('reports.info')}}</a></li>
				</ul>
				<div class="row margin-top-20">
					<div class="col-xs-12">
						<span class="pull-left label label-info" style="margin-right:10px">
							<span style="font-weight:normal">{{trans('reports.published')}}</span> <a href="{{ URL::route('profile.show', $data->user->id) }}" class="white">{{ $data->user->getFullnameWithRep() }}</a>
						</span>
						<span class="pull-left label label-info">
							<span style="font-weight:normal">{{trans('reports.template')}}</span>
							@if( Auth::user()->admin )
								<a href="{{ URL::route('template.show', $data->template->id) }}" class="white">{{ $data->template->title }}</a>
							@else
								{{ $data->template->title }}
							@endif
						</span>
<!--
						<span class="pull-right">
							<span class="label label-warning">{{trans('reports.created_ago')}} {{ $data->created_at }}</span>
							<span class="label label-info">{{ $data->visitors->count() }} @choice( trans('reports.visit')|trans('reports.visits'), $data->visitors->count() )</span>
						</span>
-->
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12">
						<h1>
							<small>{{ $data->title }}</small>

							<span class="pull-right">
							@if( $data->template->id == 10 )
								@if( property_exists($data->datum , 'IHealthID') )
									<a class="btn btn-success"><i></i>Sincronizado con IHealth</a>
								@else
									<a class="btn btn-warning" href="{{URL::route('ihealth.register')}}"><i></i>Registrar cuenta de IHealth</a>
								@endif
							@endif
<!--
								<a class="btn btn-success action-vote {{ lcfirst(get_class($data)) }}-{{ $data->id }}" style="margin-right: -5px" href="javascript:void(0)" {{ (Auth::user()->id == $data->user->id || Auth::user()->voted($data->id, 'Report')) ? 'disabled' : '' }} 
									data-vote-type="{{ lcfirst(get_class($data)) }}"
									data-vote-id="{{ $data->id }}"
									data-loading-text="<i class='icon-spinner icon-spin'></i>"
									title="{{trans('reports.clickvotepos')}}">
										<i class="icon-thumbs-up"></i> {{ (Auth::user()->id == $data->user->id || Auth::user()->voted($data->id, 'Report')) ? $data->votes()->whereValue(true)->count() : '' }}
								</a>
								<a class="btn btn-danger action-unvote {{ lcfirst(get_class($data)) }}-{{ $data->id }}" href="javascript:void(0)" {{ (Auth::user()->id == $data->user->id || Auth::user()->voted($data->id, 'Report')) ? 'disabled' : '' }}
									data-vote-type="{{ lcfirst(get_class($data)) }}"
									data-vote-id="{{ $data->id }}""
									data-loading-text="<i class='icon-spinner icon-spin'></i>"
									title="{{trans('reports.clickvoteneg')}}">
										<i class="icon-thumbs-down"></i> {{ (Auth::user()->id == $data->user->id || Auth::user()->voted($data->id, 'Report')) ? $data->votes()->whereValue(false)->count() : '' }}
								</a>
								<a class="btn btn-danger action-unfollow" href="javascript:void(0)" style="{{ $data->getUsersFromFollowers()->contains(Auth::user()->getKey()) ? '' : 'display:none' }}"
									data-followable-type="{{ lcfirst(get_class($data)) }}"
									data-followable-id="{{ $data->id }}"
									data-toggle="true"
									data-loading-text="<i class='icon-spinner icon-spin'></i>">
										<i class="icon-eye-close"></i> {{trans('reports.unfollow')}}
								</a>
								<a class="btn btn-success action-follow" href="javascript:void(0)" style="{{ $data->getUsersFromFollowers()->contains(Auth::user()->getKey()) ? 'display:none' : '' }}"
									data-followable-type="{{ lcfirst(get_class($data)) }}"
									data-followable-id="{{ $data->id }}"
									data-toggle="true"
									data-loading-text="<i class='icon-spinner icon-spin'></i>">
										<i class="icon-eye-open"></i> {{trans('reports.follow')}}
								</a>
-->
							</span>

						</h1>
					</div>
				</div>
				
				<div class="row margin-top-20">
					<div class="col-xs-12">
		
						<div class="box">
							<div class="box-header level-2">
								<h2><i class="icon-file-text-alt"></i><span class="break"></span>{{trans('reports.reportcontent')}}</h2>
								<div class="box-icon">
									<a href="javascript:void(0)" class="btn-minimize"><i class="icon-chevron-up"></i></a>
								</div>
							</div>

							<div class="box-content report-data-view">
								<form class="report-content">
									{{ $data->content }}
								</form>

                                
							</div>
						</div>

					</div>
				</div>

				@if ($data->template->id == 10)
				<div class="row">
					<div class="col-xs-12">
		
						<div class="box">
							<div class="box-header level-2">
								<h2><i class="icon-file-text-alt"></i><span class="break"></span>Enfermedades asociadas</h2>
								<div class="box-icon">
									<a href={{ URL::route('report.create', [ 'template' => 11, 'link' => $data->id, 'linkTitle'=> str_replace(array("/",":","-","\\"), " ", $data->title), 'linkTemplate'=>$data->template_id  ]) }} class="btn-add"><i class="icon-plus"></i></a>
									<a href="javascript:void(0)" class="btn-minimize"><i class="icon-chevron-up"></i></a>
								</div>
							</div>
							<div class="box-content">
								<label class="control-label"> </label>
								<table id="diagnosis-table" class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">
									<thead>
											<tr>
												<th>Enfermedad</th>
												<th>Fecha de inicio</th>
											</tr>
										</thead>
										<tbody>	
										@foreach($enfermedadesAsociadas as $enfermedad)
											<tr>
													<th><span class="label label-success">{{ $enfermedad->nombre }}</span></th>
													<th>{{ $enfermedad->fecha }}</th>
											</tr>
										 @endforeach
										 </tbody>
								</table>
							</div>	
						</div>

					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
		
						<div class="box">
							<div class="box-header level-2">
								<h2><i class="icon-file-text-alt"></i><span class="break"></span>Registro de Marcadores de Estado de Salud</h2>
								<div class="box-icon">
									<a href="javascript:void(0)" class="btn-minimize"><i class="icon-chevron-up"></i></a>
								</div>
							</div>
							
								<div class="row margin-top-20" style="margin-left:7px; margin-right:7px; margin-bottom: 15px">
									@foreach ($linkedFields as $field)
										@if ($field->template_id != 11)
										<div class="col-lg-3 col-md3 col-xs-6">
											<a class="quick-button-small" href="{{ URL::route('report.create', [ 'template' => $field->template_id, 'link' => $data->id, 'linkTitle'=> str_replace(array("/",":","-","\\"), " ", $data->title), 'linkTemplate'=>$data->template_id  ]) }}">
											<!--<i class="icon-heart"></i>-->
												<p style="font-size:13px">{{$field->template_title}}</p>
											</a>
										</div>
										@endif                       
									@endforeach
								</div>
							</div>
						</div>

					</div>
				</div>
				@endif



				



				@if ($data->template->id == 2)
				<div class="row margin-top-20">
					<div class="col-xs-12">
<!--
						<div class="box">
							<div class="box-header level-2">
								<h2><i class="icon-stethoscope"></i><span class="break"></span>Auto-Diagnóstico</h2>
								<div class="box-icon">
									<a href="javascript:void(0)" class="btn-minimize"><i class="icon-chevron-up"></i></a>
								</div>
							</div>

							<div class="box-content">
								<div class="row">
									<div class="col-xs-12">
										<div style="margin-bottom:5px">
										<span>Síntomas reconocidos: </span>
										@foreach ($signs as $sign)
											<span class="label label-default">{{$sign}}</span>
										@endforeach
										</div>
										
										<table id="diagnosis-table" class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">
											<thead>
												<tr>
													<th>Diagnóstico</th>
													<th>Especialista recomendado</th>
												</tr>
											</thead>
											<tbody>	
												@foreach ($diseases as $disease)
													<tr>
														<th>{{$disease['name']}}</th>
														<th>{{$disease['doctor']}} ({{$disease['specialty']}})</th>
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
-->
					</div>
				</div>
				@endif
				
				@if ($data->template->settings->geolocation)
				<div class="row">
					<div class="col-xs-12">
		
						<div class="box">
							<div class="box-header level-2">
								<h2><i class="icon-map-marker"></i><span class="break"></span>{{trans('reports.geolocation')}}</h2>
								<div class="box-icon">
									<a href="javascript:void(0)" class="btn-minimize"><i class="icon-chevron-up"></i></a>
								</div>
							</div>

							<div class="box-content">
								
									<div class="row">
										<div class="col-xs-4">
											<h2 class="inh_bold">{{trans('reports.geoloc.geoinformation')}}</h2>
											<dl class="dl-horizontal inh_nomargin-top">
												<dt>{{trans('reports.geoloc.country')}}</dt><dd>{{ $data->country }}</dd>
												<dt>{{trans('reports.geoloc.city')}}</dt><dd>{{ $data->city }}</dd>
												<dt>{{trans('reports.geoloc.address')}}</dt><dd>{{ $data->address }}</dd>
												<br />
												@if ($data->geo->type == 'Point')
												<dt>{{trans('reports.geoloc.latitude')}}</dt><dd>{{ $data->geo->coordinates[1] }}</dd>
												<dt>{{trans('reports.geoloc.longitude')}}</dt><dd>{{ $data->geo->coordinates[0] }}</dd>
												@endif
											</dl>
										</div>
										<div class="col-xs-8">
											<h2 class="inh_bold">{{trans('reports.geoloc.location')}} <a href="{{ URL::to('/').'#'.$data->id }}" class="blue inh_small pull-right"><i class="icon-search"></i>{{trans('reports.geoloc.viewlarger')}}</a></h2>

											<div id="map" class="well well-small inh_nopadding inh_nomargin"></div>
										</div>
									</div>
							</div>
					</div>
				</div>
				@endif
				
				@if ($data->template->id != 10)
					@if ($linkedFields)
					<div class="row">
						<div class="col-xs-12">
			
							<div class="box">
								<div class="box-header level-2 blue">
									<h2><i class="icon-link"></i><span class="break"></span>@if(sizeof($linkedFields) ==1 ){{$linkedFields[0]->template_title}}s asociados @else Informes asociados @endif</h2>
									<div class="box-icon">
										<a href="javascript:void(0)" class="btn-minimize"><i class="icon-chevron-up"></i></a>
									</div>
								</div>

								<div class="box-content">
									@foreach($linkedFields as $field)
									<div class="form-group">
										<label class="control-label">{{trans('reports.field')}} <i>{{$field->field_title}}</i> {{trans('reports.oftemplate')}} <i><a href="{{URL::route('template.show', $field->template_id)}}">{{$field->template_title}}</a></i></label>
										<table id="linked-{{$field->template_id}}-{{$field->field_id}}" class="table-linked table table-striped table-bordered table-condensed bootstrap-datatable vertical-middle">
										<thead>
											<tr>
												<th>{{trans('reports.title')}}</th>
												<th class="width-min default_sort" data-sort-dir="desc">{{trans('reports.createdat')}}</th>
												<th class="width-50 text-center"><i class="icon-comment-alt"></i></th>
											</tr>
										</thead>

										<tbody>
										</tbody>
										</table>
									</div>
									@endforeach
								</div>
						</div>
					</div>
					@endif
				@endif
					
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->

<style>
#map{height:450px}
</style>
@stop


@section('specific-javascript-plugins')
@javascripts('cr_template')
@javascripts('cr_expreditor')
@stop


@section('custom-javascript')
<script>
$(document).ready(function(){

	@if ($data->template->settings->geolocation)
		// Mapas estáticos de adjuntos
		$('img.static-map').each(function(){
			var $this = $(this),
				id = $this.data('id'),
				latitude = $this.data('latitude'),
				longitude = $this.data('longitude');

			// Relleno mapa del report
			url = GMaps.staticMapURL({
				size: [1000, 200],
				lat: latitude,
				lng: longitude,
				zoom: 16,
				scale: 2,
				markers: [
					{lat: latitude, lng: longitude, color: 'blue'}
				]
			});

			$this.attr('src', url);
		});

		// Relleno mapa del report
		var canvas = $('#map'), dimensions = { width: canvas.width(), height: canvas.height() };
		
		var map = new google.maps.Map(document.getElementById('map'), {
			center: new google.maps.LatLng(0,0),
			zoom: 1});
		
		var geojson = {{ json_encode($data->geo) }};
		CoolReport.drawFromGeoJSON(map, geojson);
		CoolReport.zoomToGeoJSON(map, geojson);
		$('#map').height(450).width('auto'); 
	@endif

	@if ($data->template->id == 2)
		$('#diagnosis-table').DataTable();
	@endif

	@if ($data->template->id == 10)
		$("input[name=5889efb0944a90]").closest('.form-group').replaceWith('<div class="form-group"><label class="control-label">Email <i class="icon-asterisk red" title="Required"></i></label><div class="controls"><input class="form-control" required="required" readonly="readonly" type="text" value="{{$user->email}}"></div></div>');
	@endif

	@if ($data->template->id == 6)
		ExprEditor.viewExpr($('textarea[name=592c2c33469e30]'), {{$registerTemplates}});
	@endif
	
	Templates.drawAttachments( false, {{ $data->id }} );

	$('form.report-content input').attr('readonly', 'readonly');

	// Cargar tablas de informes enlazados
	$('.table-linked').each(function(i, table) {
		var id_parts = table.id.split('-');
		$(table).dataTable({
			serverSide: true,
			ajax: '{{URL::route('report.table')}}?mode=linked&template=' + id_parts[1] + '&field=' + id_parts[2] + '&report=' + {{$data->id}},
			dom: "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-12'i><'col-lg-12 center'p>>",
			columns: [
				{ 
					name: 'id', 
					data: function( row ) { 
						return '<a href="' + row.DT_RowData.url + '">' + row.title + '</a>';
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
						return '<a href="">'.replace('_id', row.DT_RowData.id) +
								'<span class="badge badge-success">' + row.comments + '</span>' +
							'</a>';
					}
				},
			]
		});
	});
});

</script>
@stop
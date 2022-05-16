@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')


	<!--=== Content Part ===-->
	<div class="container">

		<!-- Este apartado estará oculto, salvo para la app móvil -->
		<div class="row" id="notifications" style="display: none;">
			<div class="col-lg-12">
				<div class="box">
					<div class="box-header">
						<h2><i class="icon-tasks"></i> Notificaciones </h2>
					</div>

					<div class="box-content">
						<div class="row">
							<fieldset class="col-lg-12 col-sm-12" >
								<div class="well">
									<label>
										<input
											data-token=""
											type="checkbox"
											name="push"
											id="notifications-handler"
											disabled
										/>
										Notificaciones activadas
									</label>
								</div>
							</fieldset>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="box">
					<div class="box-header">
						<h2><i class="icon-tasks"></i> Seguimiento </h2>
						<div class="box-icon">
							<button id="refresh" class="btn background" style="padding: 0px;"><i class="icon-refresh backgroundColor backColor"></i></button>
						</div>
					</div>

					<div class="box-content">
						<div class="row">
							<form class="form-horizontal col-lg-12 col-sm-12" enctype="multipart/form-data">

								<fieldset class="col-lg-12 col-sm-12" >

									<div class="form-group">
										<label class="control-label" for="select-person"> Pacientes <i class="icon-spinner icon-spin" style="display:none"></i></label>
										<div class="controls">
											<select id="select-person" name="person" class="form-control" data-rel="chosen" data-placeholder="Elija un paciente ...">
												<option></option>
												@foreach( $persons as $key => $person )
													<option value="{{ $person->id }}">{{ $person->surname.", ".$person->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
								</fieldset>
							</form>
						</div>

						<div class="well">
							<ul class="nav nav-pills nav-justified enfermedad-seleccion">

								@foreach ($enfermedades as $t )
								<!--Sólo para Pacientes-->

		  						@endforeach
							</ul>
						</div>

						<ul class="nav nav-tabs" id="tab">
							<li class="active">
								<a data-toggle="tab" href="#actual"><i class="icon-eye-open"></i>  Actual</a>
							</li>
							<li>
								<a data-toggle="tab" href="#history"><i class="icon-calendar"></i>  Histórico</a>
							</li>
						</ul>
					</div>

					<div class="tab-content" id="tabContent" style="padding: 0px !important">
						<!--  Actual  dashboardHealth.b -->
						<div class="box-content tab-pane active" id="actual">

							<div class="well">
									<div class="row">
										<div class="col-xs-12">
											<div class="box margin-top-20">
												<div class="box-header level-2">
													<!-- Diagramas -->
													<h2 class= "reg-name"><i class="icon-bar-chart"></i> Registro </h2>
													<div class="box-icon">
														<a href="javascript:void(0)" class="btn background btn-minimize" style="padding: 0px;"><i class="icon-chevron-up backgroundColor backColor"></i></a>
													</div>
												</div>

												<!-- Diagramas/content -->
												<div class="box-content">
													<div class="row">
														<div class="col-lg-3 col-sm-6 gBPD"><div id="gBPD" class="sz1"></div></div><!--/col-->
														<div class="col-lg-3 col-sm-6 gBPS"><div id="gBPS" class="sz1"></div></div><!--/col-->
														<div class="col-lg-3 col-sm-6 gSo2"><div id="gSo2" class="sz1"></div></div><!--/col-->
														<div class="col-lg-3 col-sm-6 gW"><div id="gW" class="sz1"></div></div><!--/col-->
														<div class="col-lg-3 col-sm-6 glucosa"><div id="glucosa" class="sz1"></div></div><!--/col-->
														<div class="col-lg-3 col-sm-6 frecResp"><div id="frecResp" class="sz1"></div></div><!--/col-->
														<div class="col-lg-3 col-sm-6 temp"><div id="temp" class="sz1"></div></div><!--/col-->
													</div>
												</div>
												<!--<div class="row"></div>-->
											</div>
										</div>
									</div><!--/row-->

									<div class="row">
<!-- Mensaje del médico -->
										<!--<div class="col-xs-12 col-lg-12">-->
										<div class="col-xs-12 col-lg-6">
											<div class="box margin-top-20">
												<div class="box-header level-2">
													<h2><i class="icon-bar-chart"></i> Recomendación del médico </h2>
													<div class="box-icon">
														<a href="javascript:void(0)" class="btn background btn-minimize" style="padding: 0px;"><i class="icon-chevron-up backgroundColor backColor"></i></a>
													</div>
												</div>
												<div class="box-content">
													<div id="doctorMp" class="col-lg-12"></div>
													<div id="personDoctorReco" class="col-lg-12"></div>
												</div>
												<div class="row"></div>
											</div>
										</div>
<!-- Recomendación -->
										<div class="col-xs-12 col-lg-6">
											<div class="box margin-top-20">
												<div class="box-header level-2">
													<h2><i class="icon-bar-chart"></i> Recomendación de tratamiento </h2>
													<div class="box-icon">
														<a href="javascript:void(0)" class="btn background btn-minimize" style="padding: 0px;"><i class="icon-chevron-up backgroundColor backColor"></i></a>
													</div>
												</div>
												<div class="box-content treatments">
													<div id="personRecom" class="col-lg-12 treat" style="margin-left: 10px;margin-bottom: 22px;"></div>
												</div>
												<div class="row"></div>
											</div>
										</div>
									</div><!--/row-->

									<!-- Status/health -->
									<div class="row">
<!-- Estado -->
										<div class="col-xs-12 col-lg-6">
											<div class="box margin-top-20">
												<div class="box-header level-2">
													<h2><i class="icon-bar-chart"></i> Estado </h2>
													<div class="box-icon">
														<a href="javascript:void(0)" class="btn background btn-minimize" style="padding: 0px;"><i class="icon-chevron-up backgroundColor backColor"></i></a>
													</div>
												</div>
												<div class="box-content" style="margin-bottom: 20px">
													<div id="personState" class="col-lg-12"></div>
												</div>
												<div class="row"></div>
											</div>
										</div>


										<div class="col-xs-12 col-lg-6">
											<div class="box margin-top-20">
												<div class="box-header level-2">
													<h2><i class="icon-bolt"></i><span class="break"></span>Información del usuario</h2>
													<div class="box-icon">
														<a href="javascript:void(0)" class="btn background btn-minimize" style="padding: 0px;"><i class="icon-chevron-up backgroundColor backColor"></i></a>
													</div>
												</div>

												<div class="box-content" style="text-align:center;padding-bottom: 21px">
													<div class="col-lg-6 col-xs-6">
														<br>
														<h2 class="inh_bold">Nombre</h2>
														<dl id="personName" class="dl-horizontal inh_nomargin-top person_data"></dl>
														<br>
														<h2 class="inh_bold">Genero</h2>
														<dl id="personGender" class="dl-horizontal inh_nomargin-top person_data"></dl>
														<br>
														<h2 class="inh_bold">Fecha de nacimiento</h2>
														<dl id="PersonBirth" class="dl-horizontal inh_nomargin-top person_data"></dl>
													</div>
													<div class="col-lg-6 col-xs-6" style="border-left-style: solid;padding-bottom: 49px; border-width: 2px;">
														<br>
														<h2 class="inh_bold">Apellidos</h2>
														<dl id="personSurname" class="dl-horizontal inh_nomargin-top person_data"></dl>
														<br>
														<h2 class="inh_bold">Altura</h2>
														<dl id="personHeigth" class="dl-horizontal inh_nomargin-top person_data"></dl>
														<br>
														<h2 class="inh_bold">Edad</h2>
														<dl id="personAge" class="dl-horizontal inh_nomargin-top person_data"></dl>
													</div>
													<span class="clearfix"></span>


												</div>
											</div>
										</div>
									</div><!--/row-->

							</div>

							<!-- Todo bien parece -->


							<!-- CDA Actions -->
							<div class="box-content" style="overflow: hidden; text-align:center; padding-bottom: 21px">

								<!-- Export -->
								<div class="col-xs-6">
									<a id="export-cda-action" href="getCDA" target="_blank" class="btn btn-lg btn-warning" style="border-radius: 25px;">
										<span class="icon-download"></span>
										Exportar Historial Clínico (HL7-CDA)
									</a>
								</div>


								<!-- Import -->
								<div class="col-xs-6">

									<form action="importCDA" id="form-import-cda" method="post" enctype="multipart/form-data">

										<!-- Hidden fields -->
										<input type="hidden" name="id-patient" value="" />
										<input type="hidden" name="file" value="" required />
										<label>
											<input
												style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0;"
												type="file" name="tmp" accept=".xml" required
											/>
											<div type="button" id="import-cda-action" href="importCDA" class="btn btn-lg btn-warning" style="border-radius: 25px;">
												<span class="icon-upload"></span>
												Importar Historial Clínico (HL7-CDA)
											</div>
										</label>


									</form>

								</div>
							</div>



<!--
							<div class="row">
								<div class="col-xs-12">
									<div class="box">
										<div class="box-header level-2">
											<h2><i class="icon-bolt"></i><span class="break"></span>Informacion del usuario</h2>
											<div class="box-icon">
												<a href="javascript:void(0)" class="btn background btn-minimize" style="padding: 0px;"><i class="icon-chevron-up backgroundColor backColor"></i></a>
											</div>
										</div>

										<div class="box-content">
											<div class="col-lg-6">
												<br>
												<h2 class="inh_bold">Nombre</h2>
												<dl id="personName" class="dl-horizontal inh_nomargin-top"></dl>
												<br>
												<h2 class="inh_bold">Genero</h2>
												<dl id="personGender" class="dl-horizontal inh_nomargin-top"></dl>
												<br>
												<h2 class="inh_bold">Fecha de nacimiento</h2>
												<dl id="PersonBirth" class="dl-horizontal inh_nomargin-top"></dl>
											</div>
											<div class="col-lg-6">
												<br>
												<h2 class="inh_bold">Apellidos</h2>
												<dl id="personSurname" class="dl-horizontal inh_nomargin-top"></dl>
												<br>
												<h2 class="inh_bold">Altura</h2>
												<dl id="personHeigth" class="dl-horizontal inh_nomargin-top"></dl>
												<br>
												<h2 class="inh_bold">Edad</h2>
												<dl id="personAge" class="dl-horizontal inh_nomargin-top"></dl>
											</div>
											<span class="clearfix"></span>


										</div>
									</div>
								</div>
							</div>
-->
						</div>

						<div class="box-content tab-pane" id="history">

							<div class="row">

								<form class="form-horizontal col-lg-12 col-sm-12" enctype="multipart/form-data">

									<fieldset class="col-lg-12 col-sm-12">

										<div class="form-group "">
										  	<label class="control-label" for="dates">Fecha</label>
										  	<div class="controls row">
												<div class="input-group col-sm-11">
													<span class="input-group-addon"><i class="icon-calendar"></i></span>
													@php( $range = Carbon\Carbon::now()->subDays(1)->format("d/m/Y")." - ".Carbon\Carbon::now()->format("d/m/Y") )
													<input type="text" class="form-control" id="dates" value="{{ $range }}">
	 											</div>
										  	</div>
										</div>

									</fieldset>

								</form>
							</div>

							<div class="row">
								<div class="btn-group type-selection col-lg-11" style="margin:0px 25px">
								  <button data-id="weight" type="button" class="btn btnweight active col-lg-4">Peso</button>

								  <button data-id="spo2" type="button" class="btn btnspo2 col-lg-4">Oxigeno en sangre</button>
								  <button data-id="bp" type="button" class="btn btnbp col-lg-4">Presion arterial</button>
								  <button data-id="glucosa" type="button" class="btn btnglucosa col-lg-4">Glucosa</button>

								</div>
							</div>


							<div class="row">

								<div class="col-xs-12">

									<div class="box margin-top-20">
										<div class="box-header level-2">
											<h2><i class="icon-bar-chart"></i> Registros </h2>
											<div class="box-icon">
												<a href="javascript:void(0)" class="btn background btn-minimize" style="padding: 0px;"><i class="icon-chevron-up backgroundColor backColor"></i></a>
											</div>
										</div>
										<div class="box-content">
											<div class="row">
												<div id='linechart'>
													<svg style="height: 500px"> </svg>
												</div>
											</div>
										</div>
									</div>
									<!-- <div class="box margin-top-20">
										<div class="box-header level-2">
											<h2><i class="icon-bar-chart"></i> Oxigeno en sangre </h2>
										</div>
										<div class="box-content">
											<div class="row">
												<div id='linechart-spo2'>
													<svg style="height: 450px"> </svg>
												</div>
											</div>
										</div>
									</div>
									<div class="box margin-top-20">
										<div class="box-header level-2">
											<h2><i class="icon-bar-chart"></i> Presión arterial </h2>
										</div>
										<div class="box-content">
											<div class="row">
												<div id='linechart-bp'>
													<svg style="height: 450px"> </svg>
												</div>
											</div>
										</div>
									</div> -->
								</div>
							</div><!--/row-->
							<div class="row">
								<div class="col-xs-12">
									<div class="box">
										<div class="box-header level-2">
											<h2><i class="icon-bolt"></i><span class="break"></span>Tabla de detalle</h2>
											<div class="box-icon">
												<a href="javascript:void(0)" class="btn background btn-minimize" style="padding: 0px;"><i class="icon-chevron-up backgroundColor backColor"></i></a>
											</div>
										</div>

										<div class="box-content">
											<table id="datatable" class="table table-condensed bootstrap-datatable vertical-middle table-linked">
												<thead>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
													</tr>
												</thead>
	   											<tbody></tbody>
											</table>
										</div>
									</div>
								</div>
							</div><!--/row-->
							<div class="row">
								<div class="col-xs-12">
									<div class="box">
										<div class="box-header level-2">
											<h2><i class="icon-globe"></i><span class="break"></span>Últimas alertas</h2>
											<div class="box-icon">
												<a href="javascript:void(0)" class="btn background btn-minimize" style="padding: 0px;"><i class="icon-chevron-up backgroundColor backColor"></i></a>
											</div>
										</div>

										<div class="box-content">
											<h1>Timeline</h1>
											<div class="graph">
												<div class="timeline"></div>
											</div>
										</div>
									</div>
								</div>
							</div><!--/row-->
						</div>

					</div>
				</div>
			</div>
		</div><!--/col-->
	</div><!--/col-->

	<!--=== End Content Part ===-->

<style type="text/css" style="display: none">
	.nvd3.multiChart .y2.axis .nv-axis line {
		stroke: none;
		fill: none;
	}
	.nvd3.multiChart .y1.axis .nv-axis line {
		opacity: 0.2;
	}
	.year-neighbor, .year-neighbor2 {
		/* Por si interesase en un futuro para eliminar el selector de años en el calendario
		visibility: hidden !important;
		*/
	}
	.prev, .next {
		/* Por si interesase en un futuro para eliminar el selector de años en el calendario
		display: none;
		*/
	}
	.btn-group button{
	    background-color: #8fcc00;
	}
	.btn-group  button.btn.active{
	    background-color: #c2ff33;
	}

	#gritter-notice-wrapper {
	    width: 600px;
	}
	.gritter-title {
	    text-shadow: 0px 0px 0 #000;
	}

	.gritter-item{
	    background-color: rgba(221, 42, 42, 0.66);
	    color: white;
	    font-size: 15px;
	}
</style>

@stop


@section('specific-javascript-plugins')
@stylesheets('cr_calendar')
@javascripts('cr_calendar')
@javascripts('cr_maps')
@stop

@section('custom-javascript')
<script>
$(document).ready(function(){


	// @todo Refactor. Only for test purposes
	// @author <joseantonio.garcia8@um.es>
	$(document).ready (function () {

		/** @var form DOM The Import CDA Form */
		var form = $('#form-import-cda');


		/** @var tmp_field DOM The input type file */
		var tmp_field = form.find ('[name="tmp"]');


		/** @var field DOM The hidden type file the the encoded content */
		var field = form.find ('[name="file"]');


		/** @var patient DOM  */
		var patient = form.find ('[name="id-patient"]');



		// Send via Ajax
		form.submit (function (e) {
        	$.ajax ({
        		url: 'importCDA',
        		method: 'post',
        		data: {
            		'id-patient': patient.val (),
            		'file': field.val ()
        		},
        		success: function () {
        			window.location.reload ();
        		}
    		});

    		return false;

		});


		// When a file is updated, it is converted to Base64 format
		tmp_field.change (function (e) {

			/** @var reader FileReader a FileReader */
			var reader  = new FileReader();

			/** @var file File the uploaded file (a XML file) */
			var file = tmp_field[0].files[0];


			// The file has loaded into base64 format
			reader.onloadend = function () {

				// Put the value
				field.val (reader.result);


				// Automatically submit the form
				form.trigger ('submit');

			}


			// Read the data as base64
			reader.readAsDataURL (file);
		});
	})


	crHooks.push(function(){

		// Actualizar el tamaño de algunos elementos a la ventana
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		  	$(window).trigger('resize');
		});

		//setInterval(function(){ if ($('#select-person').val()) { viewPersonData(false);} }, 10000);
		//$('#refresh').on('click', function(){ if ($('#select-person').val()) { viewPersonData();} } );

		/* jQuery Chosen */
		// Funcion para llamar al chosen cuando tiene muchos valores, actualiza los valores mediante peticiones ajax
		// Se necesita una ruta a la que llamar para obtener los datos
		// la ruta devuelve los datos y un count que se pasa por a la ruta para saber la ultima peticion
		function setChosen(selectDiv, url, call){

			var select = $('#'+selectDiv);

			$(select).chosen({ width: '100%', no_results_text: "<i class='icon-spinner icon-spin'></i> Searching " }).on('change', function(e) {

					@if  ( Auth::user()->admin || Auth::user()->role_id == 4)
						getDiseases();
					@endif
				    call();
		  	});

			var chosen = $(select).siblings('.chosen-container');
		  	var count = 0;
		  	var input =  $(chosen).find('input:text');


		  	// @todo. Hay un error aquí ya que se pierden los datos al usar el teclado
		  	// para navegar. Desactivando esto funciona, pero es posible
		  	// que alguna otra cosa se borre
		  	//
		  	// Para volver al comportamiento anterior, eliminar la línea return
		  	//
		  	// @author <joseantonio.garcia8@um.es>
		  	return;
			$(input).on('keyup', function(e) {
				var search = $(input).val();
				count++;
				$.get(url, {search: search, count: count})
					.success(function(data){
						if (count == data['count']) {
							$(select).empty();
							$(select).append(new Option());
							for(i=0; i<data['data'].length; i++)
		        			{
								$(select).append(new Option(data['data'][i].id_real, data['data'][i].id));
							}

							$(select).trigger("chosen:updated");
							$(input).val(search);

							if(data['data'].length == 0){
								$(select).next().find(".chosen-results").append('<li class="no-results">No result match "' + (search) + '"</li>');
							}

						}
					});
			});
		}

		function getDiseases(){
			$.get("getDiseases", { user: $('#select-person').val() })
				.success(function(data){
					console.log(data);
					//console.warn(data);

					// CDA
					$('#export-cda-action').attr ('href', 'getCDA/' + $('#select-person').val());


					$('#form-import-cda').find ('[name="id-patient"]').val ($('#select-person').val());
console.warn( "Id de la persona seleccionada [Ok -  es este]: "+$('#select-person').val() );
					$(".enfermedad-seleccion").empty();
					/* Esto sólo muestra para médicos y administradores, para pacientes (ver más arriba) */
					for (var d in data) {
						for (var i in data[d]) {

							var obj = jQuery.parseJSON( data[d][i].datum );
							var nombre_enfermedad = (obj["5922b08ed8df00"] != null) ? obj["5889e63a4646c0"].value+' '+obj["5922b08ed8df00"].value  : obj["5889e63a4646c0"].value;
							$(".enfermedad-seleccion").append('<li class="nav-item ' + (data[d][i].id == 635 || data[d][i].id == 636  ? 'active' : '') + ' ch-nav-s"><a class="nav-link" data-id="'+ data[d][i].id+'" href="#">' + nombre_enfermedad + '</a></li>') ;
						}
					}

					viewPersonData();

					$(".enfermedad-seleccion li").on("click", function() {
						$('.treatments > ch').empty();
						$(this).addClass('active').siblings().removeClass('active');

						enfermedadSelection();

					});


		    	})
		}

		// funcion para generar el date picker
		function setDateRangePicker(div, call){
			$('#'+div).daterangepicker({
				format: 'DD/MM/YYYY',
				ranges: {
				   'Today': [moment(), moment()],
				   'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
				   'Last 7 Days': [moment().subtract('days', 6), moment()],
				   'Last 30 Days': [moment().subtract('days', 29), moment()],
				   'This Month': [moment().startOf('month'), moment().endOf('month')],
				   'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
				},
				startDate: moment().subtract('days', 1),
				endDate: moment()
			}, function(from, to) {

				self.from = from;
				self.to = to;

				call();

			});
		}



		/* Funcion para generar la grafica de lineas */
		/*
			lineData: [{
						'key': _label,
						'color' : _color,
						'values' : [{ 'x': _valorX, 'y': _valorY }, ..... ] },
						'type' : _tipo ( 'line', 'bar', ...),
						'yAxis' : _numEjeY}
					, ....]
		*/
		function setLineChart(div, lineData , unit, unit2, byDay = true ){
//console.log("pasa char--"+div+"----"+ lineData +"----"+ unit+"----"+ unit2+"----"+ byDay);
			nv.addGraph(function() {
			  var chart = nv.models.multiChart()
			  	.margin({top: 30, right: 75, bottom: 100, left: 85})
			  	.useInteractiveGuideline(true);
			  	//.color(d3.scale.category10().range());

			  chart.xAxis.tickFormat(function(d) {
			              return d3.time.format(byDay ? '%d/%m/%Y' : '%d/%m/%Y %I %p')(new Date(d))
			             });
			  chart.yAxis1.tickFormat(function(d) { return d3.format(',.0f')(d) + ' ' + unit});
			  chart.yAxis2.tickFormat(function(d) { return d3.format(',.0f')(d) + ' ' + unit2});
			  //chart.yAxis2.tickFormat(function(d) { return d3.format(',.1f')(d) + ' º'});

			  for(var line in lineData){
			  	// Convertir los datos a numeros
			  	lineData[line].values.forEach(function(d) { d.y = +d.y; });

				// Ordenar datos
			  	lineData[line].values.sort(function (a, b){
			  		if (a.x > b.x) {
		 		   		return 1;
	  				}
		  			if (a.x < b.x) {
		    			return -1;
		  			}
		  					// a must be equal to b
		  			return 0;
			  	})
			  }

			  d3.select('#'+div+' svg')
				  .datum(lineData)
				  .transition().duration(500).call(chart);


			  $('#'+div+' svg g.nv-x text').attr('transform', 'translate(45,45) rotate(45)');


			  nv.utils.windowResize(function() {
			  	  chart.update();
				  $('#'+div+' svg g.nv-x text').attr('transform', 'translate(45,45) rotate(45)');
			  });

			  	chart.legend.dispatch.on('legendClick', function() {
			  		setTimeout( function(){
				  		$('#'+div+' svg g.nv-x text').attr('transform', 'translate(45,45) rotate(45)');
			  		}, 1)
				});


			  $('.nvtooltip').css('opacity',0); // Esconder los tooltip que se habian generado si se ha actualizado la grafica

			  return chart;
		    });
		}

		// Funcion para generar la tabla de datos
		function setDatatable(div, data, columns){
			// $("#"+div).dataTable().fnDestroy();
			$("#"+div).dataTable().fnClearTable();

			var thead = '';
			for (var i in columns) { thead += '<tr></tr>' }

			$("#"+div+' thead').html(thead);

			$('#'+div).dataTable({
				"bDestroy": true,
				"paging": true,
				'columns': columns,
				'data': data
			});
		}

		// funcion para generar el div de geolocalizacion
		/*
			data : obtenidos de la funcion TemplateBuilder::with(template->fields)->answer(datum)->readonly()->build() del report
		*/
		function setGeoData(geolocationDiv, geoinfoDiv, locationDiv, mapDiv, data) {
			// GEOLOCATION INFORMATION

			$("#"+geolocationDiv).attr('style','');

			var country = data.country ? data.country : '';
			var city = data.city ? data.city : '';
			var address = data.address ? data.address : '';

			var geoinfo = 	"<dl id='"+geoinfoDiv+"' class='dl-horizontal inh_nomargin-top'>"+
									"<br>"+
									"<dt>Country</dt><dd>"+ country +"</dd>"+
									"<dt>City</dt><dd>"+ city+"</dd>"+
									"<dt>Address</dt><dd>" + address + "</dd>"+
									"<br />";
			if(data.geo.type == 'Point'){
				geoinfo = geoinfo + "<dt>Latitude</dt><dd>" + data.geo.coordinates[1] + "</dd>";
				geoinfo = geoinfo + "<dt>Longitude</dt><dd>" + data.geo.coordinates[0] + "</dd>";
			}
			geoinfo = geoinfo +	"</dl>";

			$('#'+geoinfoDiv).replaceWith(geoinfo);

			var location  = "<h2 id='"+locationDiv+"' class='inh_bold'>Location <a href='{{ URL::to('/')}}/#"+ data.id + "' class='blue inh_small pull-right'><i class='icon-search'></i> View in a big map</a></h2>";
			$('#'+locationDiv).replaceWith(location);

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
			var canvas = $('#'+mapDiv), dimensions = { width: canvas.width(), height: canvas.height() };

			var map = new google.maps.Map(document.getElementById(mapDiv), {
				center: new google.maps.LatLng(0,0),
				zoom: 1});

			var geojson = data.geo ;

			CoolReport.drawFromGeoJSON(map, geojson);
			CoolReport.zoomToGeoJSON(map, geojson);
			$('#'+mapDiv).height(450).width('auto');

			google.maps.event.trigger(map,'resize');
		}

		function setAlertTimeline( div, data ){

			$(div).html('');

			for( var i in data){
				var timeslot =
					$('<div class="timeslot">'+
						'<div class="task">'+
							'<span style="background: #fffbf4; border: 2px solid #fd8f05; !important">'+
								'<span class="type">'+ data[i].title +'</span>'+
								'<span class="details">'+ data[i].description +'</span>'+
								'<span> Creado en:'+
									'<span class="remaining">'+ data[i].date +'</span>'+
								'</span>'+
							'</span>'+
							'</div>'+
							'<div class="icon" style="background: #fd8f05; border: #fd8f05; !important">'+
								'<i class="icon-bell" ></i>'+
							'</div>'+
						'<div class="time">'+ data[i].date +'</div>'+
					   '</div>');

				if (i%2 == 1) {
					$(timeslot).addClass('alt');
				}

				$(div).append(timeslot);
			}

			$(window).trigger('resize');


		}

		function getPersonDataValues( data, keys ){
//console.log("getPersonDataValues--"+data +"---"+keys);
			var values = [];

			for ( var key in keys ){
				values[keys[key]] = [];
			}

			for (var i in data ) {
				for ( var key in keys ){

					values[keys[key]].push({
						x: new Date (data[i]['fecha']),
						y: data[i][keys[key]]
					});
//					 console.log(data[i][keys[key]], keys[key], data);
				}
			}
			return values;
		}

		var gBPD,gBPS,gSo2,gW, glucosa;

		function initGages( age, gender, data, init = true ){

			var limits = {
				ages: [[16,18],[19,24],[25,29],[30,39],[40,49],[50,59],[60,Number.MAX_SAFE_INTEGER]],
				male:{
					sistolic: [[105,135],[105,139],[108,139],[110,145],[110,150],[115,155],[115,160]],
					diastolic: [[60,86],[62,88],[65,89],[68,92],[70,96],[70,98],[70,100]]
				},
				female:{
					sistolic: [[100,130],[100,130],[102,135],[105,139],[105,150],[110,155],[115,160]],
					diastolic: [[60,85],[60,85],[60,86],[65,89],[65,96],[70,98],[70,100]]
				}
			}

			var diastolic =['La tension diastólica es mas baja que la recomendada',
						'La tension diastólica está en los niveles recomendados',
						'La tension diastólica es mas alta que la recomendada'];
			var sistolic =['La tension sistólica es mas baja que la recomendada',
						'La tension sistólica está en los niveles recomendados',
						'La tension sistólica es mas alta que la recomendada'];
			var spo2 = ['El oxigeno en sangre es demasiado bajo',
						'El oxigeno en sangre es muy bajo',
						'El oxigeno en sangre está en niveles normales'];
			var imc = ['El peso está por debajo del recomendado',
						'El peso está en valores recomendados',
						'El peso está por encima del recomendado',
						'El peso está demasiado alto, puede afectar a su salud. Se le recomienda perder peso'];
			var glucosa = ['La glucosa está por debajo del recomendado',
						'La glucosa está  en valores recomendados',
						'La glucosa está  por encima del recomendado',
						'La glucosa está demasiada alta, puede afectar a su salud. Se recomienda consultar a su médico'];
			var frecResp = ['La frecuancia respiratoria está por debajo del recomendado',
						'La frecuancia respiratoria está  en valores recomendados',
						'La frecuancia respiratoria está  por encima del recomendado',
						'La frecuancia respiratoria está demasiada alta, puede afectar a su salud. Se recomienda consultar a su médico'];
			var temp = ['La temperatura respiratoria está por debajo del recomendado',
						'La temperatura respiratoria está  en valores recomendados',
						'La temperatura respiratoria está  por encima del recomendado',
						'La temperatura respiratoria está demasiada alta, puede afectar a su salud. Se recomienda consultar a su médico'];

			var i = 0;
			for (i in limits.ages) {
				if (age <= limits.ages[i][1])
					break;
			}
			var g = (gender == 'Hombre') ? 'male' : 'female';

			var s,d,sis,i;


			if (data['spo2'] != null){
				$('.gSo2').show( 2000 );
				if (data['spo2'] <= 80 ) {
					s= 0;
				}else if  (data['spo2'] <= 90 ){
					s = 1;
				}else{
					s = 2;
				}
			}else{
				$('.gSo2').hide( "slow" );
			}

			if (data['sistolic'] != null){
				$('.gBPS').show( 2000 );
				if (data['sistolic'] <= limits[g].sistolic[i][0] ) {
					sis = 0;
				}else if (data['sistolic'] >= limits[g].sistolic[i][1] ){
					sis = 2;
				}else{
					sis = 1;
				}
			}else{
				$('.gBPS').hide( "slow" );
			}

			if (data['diastolic'] != null){
				$('.gBPD').show( 2000 );
				if (data['diastolic'] <= limits[g].diastolic[i][0] ) {
					d = 0;
				}else if (data['diastolic'] >= limits[g].diastolic[i][1]  ){
					d = 2;
				}else{
					d = 1;
				}
			}else{
				$('.gBPD').hide( "slow" );
			}


			if (data['imc'] != null){
				$('.gW').show( 2000 );
				if (data['imc'] < 18.5 ) {
					i = 0;
				}else if (data['imc'] <= 25 ){
					i = 1;
				}else if (data['imc'] <= 30 ){
					i = 2;
				}else{
					i = 3;
				}
			}else{
				$('.gW').hide( "slow" );
			}

			if (data['glucosa'] != null){
				$('.glucosa').show( 2000 );
				$('.btnglucosa').show( 2000 );
				if (data['glucosa'] < 60 ) {
					i = 0;
				}else if (data['glucosa'] <= 70 ){
					i = 1;
				}else if (data['glucosa'] <= 100 ){
					i = 2;
				}else{
					i = 3;
				}
			}else{
				$('.glucosa').hide( "slow" );
				$('.btnglucosa').hide( "slow" );
			}

			if (data['frecResp'] != null){
				$('.frecResp').show( 2000 );
				if (data['frecResp'] < 60 ) {
					i = 0;
				}else if (data['frecResp'] <= 70 ){
					i = 1;
				}else if (data['frecResp'] <= 100 ){
					i = 2;
				}else{
					i = 3;
				}
			}else{
				$('.frecResp').hide( "slow" );
			}

			if (data['temp'] != null){
				$('.temp').show( 2000 );
				if (data['temp'] < 60 ) {
					i = 0;
				}else if (data['temp'] <= 70 ){
					i = 1;
				}else if (data['temp'] <= 100 ){
					i = 2;
				}else{
					i = 3;
				}
			}else{
				$('.temp').hide( "slow" );
			}






			if(init){
				$('#gBPD').html('');
				gBPD = new JustGage({
					id:"gBPD",
					value: (data['diastolic'] == null ? 0 : data['diastolic']),
					max: 120,
					min: 40,
					title:"Tension Diastólica",
					label: 'mmHg',
					customSectors: [{
				          	color : "#ff3b30",
				          	lo : 40,
				          	hi : (limits[g].diastolic[i][0] - 1)
				        },{
				          	color : "#57FB21",
				          	lo : limits[g].diastolic[i][0],
				          	hi : (limits[g].diastolic[i][1] - 1)
				        },{
				          	color : "#ff3b30",
				          	lo : limits[g].diastolic[i][1],
				          	hi : 120
				    }],
				});

				$('#gBPS').html('');
				gBPS = new JustGage({
					id:"gBPS",
					value: (data['sistolic'] == null ? 0 : data['sistolic']),
					max: 180,
					min: 80,
					title:"Tension Sistólica",
					label: 'mmHg',
					customSectors:[{
				          	color : "#ff3b30",
				          	lo : 80,
				          	hi : limits[g].sistolic[i][0]
				        },{
				          	color : "#57FB21",
				          	lo : limits[g].sistolic[i][0],
				          	hi : (limits[g].sistolic[i][1] - 1)
				        },{
				          	color : "#ff3b30",
				          	lo : limits[g].sistolic[i][1],
				          	hi : 180
				    }]
				});

				$('#gSo2').html('');
				gSo2 = new JustGage({
					id:"gSo2",
					value: (data['spo2'] == null ? 0 : data['spo2']),
					max: 100,
					min: 0,
					title:"Oxigeno en sangre",
					label: '%',
					customSectors:[{
				          	color : "#ff3b30",
				          	lo : 0,
				          	hi : 80
				        },{
				          	color : "#E1E14B",
				          	lo : 80,
				          	hi : 90
				        },{
				          	color : "#9ECA47",
				          	lo : 90,
				          	hi : 95
				        },{
				          	color : "#57FB21",
				          	lo : 95,
				          	hi : 100
			        }]
				});

				$('#gW').html('');
				gW = new JustGage({
					id:"gW",
					value: (data['imc'] == null ? 0 : data['imc']),
					max: 50,
					min: 10,
					title:"IMC",
					label:'Kg/m^2',
					customSectors: [{
				          	color : "#ff3b30",
				          	lo : 10,
				          	hi : 18.5
				        },{
				          	color : "#57FB21",
				          	lo : 18.5,
				          	hi : 24.99
				        },{
				          	color : "#ff3b30",
				          	lo : 25,
				          	hi : 50
			        }]
				});
				$('#glucosa').html('');
				gW = new JustGage({
					id:"glucosa",
					value: (data['glucosa'] == null ? 0 : data['glucosa']),
					max: 200,
					min: 0,
					title:"Glucosa",
					label:'mg/dl',
					customSectors: [{
				          	color : "#ff3b30",
				          	lo : 0,
				          	hi : 70
				        },{
				          	color : "#57FB21",
				          	lo : 70,
				          	hi : 100
				        },{
				          	color : "#ff3b30",
				          	lo : 100,
				          	hi : 200
			        }]
				});

				$('#frecResp').html('');
				gW = new JustGage({
					id:"frecResp",
					value: (data['frecResp'] == null ? 0 : data['frecResp']),
					max: 200,
					min: 0,
					title:"Frecuencia Respiratoria",
					label:'mg/dl',
					customSectors: [{
				          	color : "#ff3b30",
				          	lo : 0,
				          	hi : 70
				        },{
				          	color : "#57FB21",
				          	lo : 70,
				          	hi : 100
				        },{
				          	color : "#ff3b30",
				          	lo : 100,
				          	hi : 200
			        }]
				});

				$('#temp').html('');
				gW = new JustGage({
					id:"temp",
					value: (data['temp'] == null ? 0 : data['temp']),
					max: 200,
					min: 0,
					title:"Temperatura",
					label:'mg/dl',
					customSectors: [{
				          	color : "#ff3b30",
				          	lo : 0,
				          	hi : 70
				        },{
				          	color : "#57FB21",
				          	lo : 70,
				          	hi : 100
				        },{
				          	color : "#ff3b30",
				          	lo : 100,
				          	hi : 200
			        }]
				});


			}else{
				gBPD.refresh(data['diastolic'] == null ? 0 : data['diastolic']);
				gBPS.refresh(data['sistolic'] == null ? 0 : data['sistolic']);
				gSo2.refresh(data['spo2'] == null ? 0 : data['spo2']);
				gW.refresh(data['imc'] == null ? 0 : data['imc']);
				glucosa.refresh(data['glucosa'] == null ? 0 : data['glucosa']);
				frecResp.refresh(data['frecResp'] == null ? 0 : data['frecResp']);
				temp.refresh(data['temp'] == null ? 0 : data['temp']);
			}

			$('#gBPD,#gBPS,#gSo2,#gW,#glucosa').attr('data-toggle','tooltip');
			$('#gBPD').attr('title','<h5>'+diastolic[d]+'<h5>');
			$('#gBPS').attr('title','<h5>'+sistolic[sis]+'<h5>');
			$('#gSo2').attr('title','<h5>'+spo2[s]+'<h5>');
			$('#gW').attr('title','<h5>'+imc[i]+'<h5>');
			$('#glucosa').attr('title','<h5>'+glucosa[i]+'<h5>');
			$('#frecResp').attr('title','<h5>'+frecResp[i]+'<h5>');
			$('#temp').attr('title','<h5>'+temp[i]+'<h5>');

		 	$('[data-toggle="tooltip"]').tooltip({
		    	html: "true",
		    	placement: "auto-bottom"
		  	});

//			console.log('gBPD',gBPD,'gBPS',gBPS,'gSo2',gSo2,'gW',gW,'glucosa',glucosa, 'frecResp', frecResp, 'temp', temp);

		}

		function viewPersonData(init = true){

			userSelected = "";
			@if  ( Auth::user()->admin || Auth::user()->role_id == 4)
				userSelected = $('#select-person').val();
			@else
				userSelected = {{$user}};
			@endif

			// RECOMENDACIÓN del médico
			$.get( "personDoctorReco",{ person: userSelected  } )
				.success(function(data)
				{
					if($("#cp").length) $("#cp").empty();
					$('#personDoctorReco').append('<div id ="cp">');
					try
					{
						data.mensajes.forEach(function(element)
						{
							//console.warn(element);
							$('<h3 class="reco_doct"><p>'+element[1]+' | '+element[2]+'</p></h3>').appendTo("#cp");
							$('<p class="content_reco_doct"><b>'+element[3]+'</b></p>').appendTo("#cp");
						});
					}
					catch (e)
					{
						console.error("Error: lectura del json");
					};
				});
				/* . $get "personDoctorReco" */


//			console.log(init);

			$.get("personInfo", { person: $('#select-person').val(), enfermedad: $(".enfermedad-seleccion").find(".active > a").attr('data-id')})
				.success(function(data){

//					console.log('personInfo', data);

					$('#personName').text(data['info']['name']);
					$('#personSurname').text(data['info']['surname']);
					$('#personGender').text(data['info']['gender']);
					$('#personHeigth').text(data['info']['height']);
					$('#personAge').text(data['info']['age']);

					// var birth = new Date(data['birth']);
					var birth = null;

					if(data['info']['birth'].search(/-/) != -1)
						birth = moment(data['info']['birth'], "YYYY-MM-DD");
					else
						birth = moment(data['info']['birth'], "DD/MM/YYYY");

					$('#PersonBirth').text(birth.format("DD-MM-YYYY"));


					initGages(data['info']['age'], data['info']['gender'], data, init);

					$('#personState').html('');

					// Estado del Paciente
					if(data['recomendaciones'] == null){
						$('#personState').html("<h2><b>No ha sido diagnosticado como paciente de ninguna enfermedad</b></h2>");

					}else{

						var state= '' ;
						for (var i in data['recomendaciones']) {
							$('#personState').append('<h2><b>Paciente con ' + i +'</b></h2>');

							for(var ii in data['recomendaciones'][i]['warning']){
								$('#personState').append('<p>' + data['recomendaciones'][i]['warning'][ii] +'</p>');
							}

							for(var ii in data['recomendaciones'][i]['critical']){
								$('#personState').append('<p><b>' + data['recomendaciones'][i]['critical'][ii] +'</b></p>');
							}
						}
					}

					$('#personTreatments').html('');

					// Recomendaciones de tratamiento que está funcionando en otros pacientes
					if(data['tratamiento'] == null)
					{
						$('#personTreatments').html("<h2><b>No se ha encontrado ninguna recomendación</b></h2>");
					}
					else
					{
						var state= '' ;
						for (var i in data['tratamiento'])
						{
							$('#personTreatments').append('<h2><b>Paciente con ' + i +'</b></h2>');

							for(var ii in data['recomendaciones'][i]['warning'])
							{
								$('#personTreatments').append('<p>' + data['recomendaciones'][i]['warning'][ii] +'</p>');
							}

							for(var ii in data['recomendaciones'][i]['critical'])
							{
								$('#personTreatments').append('<p><b>' + data['recomendaciones'][i]['critical'][ii] +'</b></p>');
							}
						}
					}

				})

	  		$.get("personsData", { person: $('#select-person').val(), startDate: $('#dates').data('daterangepicker').startDate.format("YYYY-MM-DD HH:mm:ssZ"), endDate: $('#dates').data('daterangepicker').endDate.clone().add('days', 1).format("YYYY-MM-DD HH:mm:ssZ"), enfermedad: $(".enfermedad-seleccion").find(".active > a").attr('data-id')})
				.success(function(data){
//console.warn("other: "+JSON.stringify(data));
					setPersonData(data, $(".type-selection button.active").attr('data-id'));

					// Selector de intervalo en gráficas
					$(".type-selection button").off("click").on("click", function() {
				    	if ($(this).siblings("button.active").length == 0) { //No ha cambiado de valor
				      		return;
						}

						//Actualizamos controles
				    	$(this).siblings().removeClass("active");
				    	$(this).addClass("active");

				    	setPersonData(data, $(this).attr('data-id'));

				    });
		    	})

			 $.get("personAlerts", { person: $('#select-person').val(), startDate: $('#dates').data('daterangepicker').startDate.format("YYYY-MM-DD HH:mm:ssZ"), endDate: $('#dates').data('daterangepicker').endDate.clone().add('days', 1).format("YYYY-MM-DD HH:mm:ssZ") })
				.success(function(data){

					setAlertTimeline('.timeline', data);
				})
		}

		function setPersonData(data, type){
			var data_attr = {
				bp :  {
					columns: ['sistolic', 'diastolic', 'pulse'],
					key: ['Sistólica','Diastólica','Pulso'],
					color: ['red','blue','green'],
					type: ['line','line','line'],
					yAxis: ['1','1','2'],
					disabled: [false,false,true],
					axis_labels:['mmHg','latidos/m']
				},
				spo2 :  {
					columns: ['spo2'],
					key: ['Oxigeno en sangre'],
					color: ['blue'],
					type: ['line'],
					yAxis: ['1'],
					disabled: [false],
					axis_labels:['%', '']
				},
				weight :  {
					columns: ['peso', 'imc'],
					key: ['Peso','IMC'],
					color: ['red','blue'],
					type: ['line','line'],
					yAxis: ['1','1'],
					disabled: [false,false],
					axis_labels: ['Kg', 'Kg/m^2']
				},

				glucosa :  {
					columns: ['glucosa', 'mg/dl'],
					key: ['mg/dl'],
					color: ['red','blue'],
					type: ['line','line'],
					yAxis: ['1','1'],
					disabled: [false,false],
					axis_labels: ['Kg', 'mg/dl']
				},

				frecResp :  {
					columns: ['frecResp', 'mg/dl'],
					key: ['mg/dl'],
					color: ['red','blue'],
					type: ['line','line'],
					yAxis: ['1','1'],
					disabled: [false,false],
					axis_labels: ['Kg', 'mg/dl']
				},

				temp :  {
					columns: ['temp', 'mg/dl'],
					key: ['mg/dl'],
					color: ['red','blue'],
					type: ['line','line'],
					yAxis: ['1','1'],
					disabled: [false,false],
					axis_labels: ['Kg', 'mg/dl']
				},

			}

			var data_values = getPersonDataValues(data[type], data_attr[type]['columns']);

			var lineData = [];
			var tableColumns = [{
				title: 'fecha',
				className: 'text-center',
				data: 'fecha'
			}];

			for (var i in data_attr[type]['key']) {
				lineData.push({
					key: data_attr[type]['key'][i],
					color: data_attr[type]['color'][i],
					values: data_values[data_attr[type]['columns'][i]],
					type: data_attr[type]['type'][i],
					yAxis: data_attr[type]['yAxis'][i],
					disabled: data_attr[type]['disabled'][i]
				});

				tableColumns.push({
					title: data_attr[type]['key'][i],
					className: 'text-center',
					data: data_attr[type]['columns'][i]
				})
			}
/*console.log("lineData" );
console.log(lineData );
console.log(data_attr[type]['axis_labels'][0]);
console.log(data_attr[type]['axis_labels'][1]);
*/
			setLineChart('linechart', lineData, data_attr[type]['axis_labels'][0], data_attr[type]['axis_labels'][1]);

			setDatatable("datatable", data[type], tableColumns);

			// var columns = [
			// 	{title: 'Fecha', className: 'text-center', data: 'timestamp'},
			// 	{title: 'Entradas (' + formatedMaxCTValue + ')', className: 'text-center', data: 'entradas'},
			// 	{title: 'Salidas (' + formatedMaxCTValue + ')', className: 'text-center', data: 'salidas'},
			// 	{title: 'eControl Cabecera (' + formatedMaxCTValue + ')', className: 'text-center', data: 'econtrol'},
			// 	{title: 'Suministros / eControl Frontera (' + formatedMaxCTValue + ')', className: 'text-center', data: 'datasuministros'},
			// 	{title: 'Pérdidas (%)', className: 'text-center', data: 'perdidas'}
			// ];
			// console.log(data[type]);

			// var bp = getPersonDataValues(data['bp'], ['sistolic', 'diastolic', 'pulse']);
			// var spo2 = getPersonDataValues(data['spo2'], ['spo2']);
			// var weight = getPersonDataValues(data['weight'], ['peso', 'imc']);

			// console.log(bp, spo2, weight);

			// lineDataBP = [];
			// lineDataSpo2 = [];
			// lineDataWeight = [];

			// lineDataBP.push({
			// 	'key': 'Sistólica',
			// 	'color': 'red',
			// 	'values': bp['sistolic'],
			// 	'type': 'line',
			// 	'yAxis': 1,
			// 	"disabled": false
			// });

			// lineDataBP.push({
			// 	'key': 'Diastólica',
			// 	'color': 'blue',
			// 	'values': bp['diastolic'],
			// 	'type': 'line',
			// 	'yAxis': 1,
			// 	"disabled": false
			// });

			// lineDataBP.push({
			// 	'key': 'Pulso',
			// 	'color': 'green',
			// 	'values': bp['pulse'],
			// 	'type': 'line',
			// 	'yAxis': 2,
			// 	"disabled": true
			// });
			// setLineChart('linechart-bp', lineDataBP, 'mmHg','latidos/m');

			// lineDataSpo2.push({
			// 	'key': 'Oxigeno en sangre',
			// 	'color': 'blue',
			// 	'values': spo2['spo2'],
			// 	'type': 'line',
			// 	'yAxis': 1,
			// 	"disabled": false
			// });
			// setLineChart('linechart-spo2', lineDataSpo2, '%');

			// lineDataWeight.push({
			// 	'key': 'Peso',
			// 	'color': 'red',
			// 	'values': weight['peso'],
			// 	'type': 'line',
			// 	'yAxis': 1,
			// 	"disabled": false
			// });

			// lineDataWeight.push({
			// 	'key': 'IMC',
			// 	'color': 'blue',
			// 	'values': weight['imc'],
			// 	'type': 'line',
			// 	'yAxis': 2,
			// 	"disabled": true
			// });
			// setLineChart('linechart-weight', lineDataWeight, 'Kg', 'Kg/m^2');

		}

		// Selector de enfermedades
		$(".enfermedad-seleccion li").on("click", function()
		{

			$('.treatments > ch').empty();
			$(this).addClass('active').siblings().removeClass('active');

			enfermedadSelection();

		});

		function enfermedadSelection(){
			viewPersonData();

			$(".reg-name").html('<i class="icon-bar-chart"></i> Registro ' + $(".enfermedad-seleccion").find(".active > a").text());

			/*
			* Recomendación de tratamientos [2a llamada] (Tras dar al botón de la enfermedad)
			*
			* Nota: Correción del id del paciente '$('#select-person').val()'', sólo para el caso de que tenga que seleccionar un paciente (función habilitada para el admin y médico)
			*/
		$.ajax(
			{
				type: "GET",

				url: 'http://nimbeo.com:8080/iohealth_webservice/api/service/nn?user_id='+$('#select-person').val()+'&enf_user='+$(".enfermedad-seleccion").find(".active > a").attr('data-id'),
				success: function(data)
				{
					if($("#ch").length) $("#ch").empty();
					$('#personRecom').append('<div id ="ch">');

					if (data.medicamentos.length > 0 || data.alimentos.length > 0 || data.actividades.length > 0)
					{
						$('<h2 id="ant_tr"><b> TRATAMIENTO: "'+data.tratamiento+'" </b></h2>').appendTo("#ch");
						//$('<h2 id="ant_tr"><b> Recomendaciones </b></h2>').appendTo("#ch");
					}else{
						$('<h2 id="ant_tr"><b> No se ha podido recomendar ningún tratamiento </b></h2>').appendTo("#ch");
						$('<b id="ant_tr"> Posibles causas: </b>').appendTo("#ch");
						$('<p id="ant_tr"> - Ausencia de algún registro como: peso, altura, fecha de nacimiento, género... </p>').appendTo("#ch");
						$('<p id="ant_tr"> - Ausencia de la existencia de tratamiento en cuestión </p>').appendTo("#ch");
					}

					if (data.medicamentos.length > 0)
						$('<p class="title_reco"><b id="ant_tr"> MEDICAMENTOS </b></p>').appendTo("#ch");
					for (var i = 0; i < data.medicamentos.length; i++)
					{
						$('<p class="nm"><b id="ant_tr" > '+data.medicamentos[i].nombre+' </b></p>').appendTo("#ch");
						$('<p id="ant_tr" class="ct_tab">Número de dosis: '+data.medicamentos[i].dosis+' </p>').appendTo("#ch");
					}
					if (data.alimentos.length > 0)
						$('<p class="title_reco"><b id="ant_tr"> ALIMENTOS </b></p>').appendTo("#ch");
					for (var i = 0; i < data.alimentos.length; i++)
					{
						$('<p class="nm"><b id="ant_tr" > '+data.alimentos[i].nombre+' </b></p>').appendTo("#ch");
						$('<p id="ant_tr" class="ct_tab"> '+data.alimentos[i].comentarios+' </p>').appendTo("#ch");
					}
					if (data.actividades.length > 0)
						$('<p class="title_reco"><b id="ant_tr"> EJERCICIOS </b></p>').appendTo("#ch");
					for (var i = 0; i < data.actividades.length; i++)
					{
						$('<p class="nm"><b id="ant_tr" > '+data.actividades[i].nombre+' </p>').appendTo("#ch");
						//console.warn("Duración: "+data..actividades[i].duracion);
					}

				},
				error: function()
				{
					if($("#ch").length) $("#ch").empty();
					// No ha habido respuestas
					$('#personRecom').append('<div id ="ch">');
					$('<h2 id="ant_tr"><b> No se ha podido recomendar ningún tratamiento </b></h2>').appendTo("#ch");
					$('<b id="ant_tr"> Posibles causas: </b>').appendTo("#ch");
					$('<p id="ant_tr"> - Ausencia de algún registro como: peso, altura, fecha de nacimiento, género... </p>').appendTo("#ch");
					$('<p id="ant_tr"> - Ausencia de la existencia de tratamiento en cuestión </p>').appendTo("#ch");
				}
			});
		}

		// INIT
		setChosen('select-person','persons', viewPersonData);





		// Recomendación de tratamientos (por defecto) 1a llamada
		$.ajax(
			{
				type: "GET",

				url: 'http://nimbeo.com:8080/iohealth_webservice/api/service/nn?user_id={{$user}}&enf_user='+$(".enfermedad-seleccion").find(".active > a").attr('data-id'),
				success: function(data)
				{

					$('#personRecom').append('<div id ="ch">');

					if (data.medicamentos.length > 0 || data.alimentos.length > 0 || data.actividades.length > 0)
					{
						$('<h2 id="ant_tr"><b> TRATAMIENTO: "'+data.tratamiento+'" </b></h2>').appendTo("#ch");
						//$('<h2 id="ant_tr"><b> Recomendaciones </b></h2>').appendTo("#ch");
					}else{
						$('<h2 id="ant_tr"><b> No se ha podido recomendar ningún tratamiento </b></h2>').appendTo("#ch");
						$('<b id="ant_tr"> Posibles causas: </b>').appendTo("#ch");
						$('<p id="ant_tr"> - Ausencia de algún registro como: peso, altura, fecha de nacimiento, género... </p>').appendTo("#ch");
						$('<p id="ant_tr"> - Ausencia de la existencia de tratamiento en cuestión </p>').appendTo("#ch");
					}

					if (data.medicamentos.length > 0)
						$('<p class="title_reco"><b id="ant_tr"> MEDICAMENTOS </b></p>').appendTo("#ch");
					for (var i = 0; i < data.medicamentos.length; i++)
					{
						$('<p class="nm"><b id="ant_tr" > '+data.medicamentos[i].nombre+' </b></p>').appendTo("#ch");
						$('<p id="ant_tr" class="ct_tab">Número de dosis: '+data.medicamentos[i].dosis+' </p>').appendTo("#ch");
					}
					if (data.alimentos.length > 0)
						$('<p class="title_reco"><b id="ant_tr"> ALIMENTOS </b></p>').appendTo("#ch");
					for (var i = 0; i < data.alimentos.length; i++)
					{
						$('<p class="nm"><b id="ant_tr" > '+data.alimentos[i].nombre+' </b></p>').appendTo("#ch");
						$('<p id="ant_tr" class="ct_tab"> '+data.alimentos[i].comentarios+' </p>').appendTo("#ch");
					}
					if (data.actividades.length > 0)
						$('<p class="title_reco"><b id="ant_tr"> EJERCICIOS </b></p>').appendTo("#ch");
					for (var i = 0; i < data.actividades.length; i++)
					{
						$('<p class="nm"><b id="ant_tr" > '+data.actividades[i].nombre+' </p>').appendTo("#ch");
						//console.warn("Duración: "+data..actividades[i].duracion);
					}

				},
				error: function()
				{
					// No ha habido respuestas
					$('#personRecom').append('<div id ="ch">');
					$('<h2 id="ant_tr"><b> No se ha podido recomendar ningún tratamiento </b></h2>').appendTo("#ch");
					$('<b id="ant_tr"> Posibles causas: </b>').appendTo("#ch");
					$('<p id="ant_tr"> - Ausencia de algún registro como: peso, altura, fecha de nacimiento, género... </p>').appendTo("#ch");
					$('<p id="ant_tr"> - Ausencia de la existencia de tratamiento en cuestión </p>').appendTo("#ch");
				}
			}
		);
		/* . $ajax recomendación de tratamientos */


		setDateRangePicker('dates',function(){

			if ($('#select-person').val()) {
				//getDiseases();
				viewPersonData();
			}
		});

		// Selección de un paciente
		@if ( !(Auth::user()->admin) ) //no Admin
			@if ( !(Auth::user()->role_id == 4) ) // no medico
				$('#select-person').val({{$user}});
				$('#select-person').trigger("chosen:updated");
				$('#select-person').change();
				$('#select-person').closest('.row').hide();
			@endif
		@endif


	})

});
</script>
@stop

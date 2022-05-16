@extends('templates.default')

@section('title', trans('sections.dashitem.create'))

@section('content')

<style>
.dropdown-submenu{position:relative;}
.dropdown-submenu>.dropdown-menu{top:0;left:100%;margin-top:-6px;margin-left:-1px;-webkit-border-radius:0 6px 6px 6px;-moz-border-radius:0 6px 6px 6px;border-radius:0 6px 6px 6px;}
.dropdown-submenu:hover>.dropdown-menu{display:block;}
.dropdown-submenu>a:after{display:block;content:" ";float:right;width:0;height:0;border-color:transparent;border-style:solid;border-width:5px 0 5px 5px;border-left-color:#cccccc;margin-top:5px;margin-right:-5px;}
.dropdown-submenu:hover>a:after{border-left-color:#ffffff;}
.dropdown-submenu.pull-left{float:none;}.dropdown-submenu.pull-left>.dropdown-menu{left:-100%;margin-left:10px;-webkit-border-radius:6px 0 6px 6px;-moz-border-radius:6px 0 6px 6px;border-radius:6px 0 6px 6px;}

.quick-button.pressed{
	background: #dddddd; /* Old browsers */
	background: -moz-linear-gradient(top, #dddddd 0%, #eeeeee 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#dddddd), color-stop(100%,#eeeeee)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top, #dddddd 0%,#eeeeee 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top, #dddddd 0%,#eeeeee 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top, #dddddd 0%,#eeeeee 100%); /* IE10+ */
	background: linear-gradient(to bottom, #dddddd 0%,#eeeeee 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#dddddd', endColorstr='#eeeeee',GradientType=0 ); /* IE6-9 */
	border-color:#a5a5a5;
}
</style>

	{{-- [Modal] View Report --}}
	@include( 'templates.mustache.dashitem' )
	@include( 'templates.mustache.dashdisplay' )
	@include( 'templates.mustache.dashjoin' )
	@include( 'templates.mustache.expreditor' )

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-plus"></i><span class="break"></span>{{ trans('sections.dashitem.create') }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('dashitem.index') }}"><i class="icon-arrow-left"></i><span>{{ trans('dashitems.back') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				<div id="dashboard-wizard" class="wizard">
					<ul class="steps">
						<li class="active" data-target="#step-class">
							<span class="badge">1</span>
						</li>
						<li data-target="#step-templates">
							<span class="badge">2</span>
						</li>
						<li data-target="#step-data">
							<span class="badge">3</span>
						</li>
						<li data-target="#step-display">
							<span class="badge">4</span>
						</li>
						<li data-target="#step-preview">
							<span class="badge">5</span>
						</li>
					</ul>
					<div class="actions">
						<button class="btn btn-prev" type="button"> <i class="icon-arrow-left"></i> {{ trans('dashitems.prev') }}</button>
						<button id="dashitem-button-next" data-last="Finish" class="btn btn-success btn-next" type="button">{{ trans('dashitems.next') }}<i class="icon-arrow-right"></i></button>
					</div>
				</div>
				<div class="step-content">
					<div id="step-class" class="step-pane active">
						<h1>{{ trans('dashitems.dashitemtype') }}</h1>
						<div class="row">
							<div class="col-sm-4" >
								<a class="quick-button pressed" id="chart-type">
									<i class="icon-bar-chart"></i>
									<p>{{ trans('dashitems.chart') }}</p>
								</a>
							</div>
							<div class="col-sm-4">
								<a class="quick-button" id="map-type">
									<i class="icon-map-marker"></i>
									<p>{{ trans('dashitems.map') }}</p>
								</a>
							</div>
							<div class="col-sm-4">
								<a class="quick-button" id="political-type">
									<i class="icon-globe"></i>
									<p>{{ trans('dashitems.politicalmap') }}</p>
								</a>
							</div>
						</div>
					</div>
					<div id="step-templates" class="step-pane">
						<h1>{{ trans('dashitems.template') }}</h1>
						<form class="form-horizontal form-dashboard-store fix-margins" action="" data-type="template">							
							<fieldset id="template-join-container" class="col-sm-12 well">
								
								<div class="form-group">
									<label class="control-label" for="template-select">{{ trans('dashitems.template') }} <i class="icon-spinner icon-spin" style="display:none"></i></label>
									<div class="controls">
										<select id="template-select" name="template" class="form-control" data-rel="chosen" data-placeholder={{ trans('dashitems.chooseatemplate') }}>
											@foreach( $templates as $template )
												<option value="{{ $template->id }}">{{ $template->title }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</fieldset>
							<div class="btn-group">
								<button class="btn btn-large btn-primary btn-join-add">
									<i class="icon-plus"></i> {{ trans('dashitems.addanothertemplate') }}
								</button>
								<button class="btn btn-large btn-danger btn-join-reset">
									<i class="icon-remove"></i> {{ trans('dashitems.reset') }}
								</button>
							</div>
						</form>
					</div>
					<div id="step-data" class="step-pane">
						<h1>{{ trans('dashitems.datasource') }}</h1>
						<form class="form-horizontal form-dashboard-store fix-margins row" action="" data-type="template">					
							<fieldset class="col-sm-12">
								<div class="form-group">
									<label class="control-label">{{ trans('dashitems.fields') }}</label>
								</div>
								
								<div class="well">
									<div class="fields-container"></div>
									<div class="controls">
										<div class="btn-group">
											<div class="btn-group">
												<button class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
													<i class="icon-plus"></i> {{ trans('dashitems.addfield') }} <span class="caret"></span>
												</button>
												<ul class="dropdown-menu" id="db-add-field-dropdown">
													<li class="dropdown-header">{{ trans('dashitems.selectatemplatefirst') }}</li>
												</ul>
											</div>
											<div class="btn-group">
												<button class="btn btn-large dropdown-toggle btn-success" data-toggle="dropdown">
													<i class="icon-plus"></i> {{ trans('dashitems.addaggregate') }} <span class="caret"></span>
												</button>
												<ul class="dropdown-menu" id="db-add-aggregate-dropdown">
													<li><a href="javascript:void(0)" class="action-add-aggregate" data-type="count"><i class="icon-sort-by-order"></i> {{ trans('dashitems.count') }}</a></li>
													<li><a href="javascript:void(0)" class="action-add-aggregate" data-type="sum"><i class="icon-plus"></i> Sum</a></li>
													<li><a href="javascript:void(0)" class="action-add-aggregate" data-type="avg"><i class="icon-resize-small"></i> {{ trans('dashitems.average') }}</a></li>
													<li><a href="javascript:void(0)" class="action-add-aggregate" data-type="min"><i class="icon-sort-down"></i> {{ trans('dashitems.min') }}</a></li>
													<li><a href="javascript:void(0)" class="action-add-aggregate" data-type="max"><i class="icon-sort-up"></i> {{ trans('dashitems.max') }}</a></li>
												</ul>
											</div>
											<div class="btn-group">
												<button class="btn btn-large btn-warning action-add-expression" data-type="expression" data-toggle="">
													<i class="icon-plus"></i> {{ trans('dashitems.addexpression') }} </span>
												</button>
											</div>
										</div>
									</div>
									
								</div>
							</fieldset>
							<fieldset class="col-sm-12">
							<div class="row">
								<div class="col-sm-12 controls">
									<div class="input-group input-group-sm">
										<span class="input-group-addon"><b>{{ trans('dashitems.filter') }}</b></span>
										<input class="form-control cr-filter-condition" value="" placeholder={{ trans('dashitems.clicktoeditexpression') }}>
									</div>
								</div>
							</div>
							</fieldset>
							<fieldset class="col-sm-6">
								<div class="form-group">
									<label class="control-label">{{ trans('dashitems.groupby') }}</label>
								</div>

								<div class="well">
									
									<div class="groups-container"></div>

									<div class="controls">
										<div class="btn-group">
											<button class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
												<i class="icon-plus"></i> {{ trans('dashitems.addfield') }} <span class="caret"></span>
											</button>
											<ul class="dropdown-menu" id="db-add-group-dropdown">
												<li class="dropdown-header">{{ trans('dashitems.selectatemplatefirst') }}</li>
											</ul>
										</div>
									</div>
									
								</div>
							</fieldset>
							<fieldset class="col-sm-6">
								<div class="form-group">
									<label class="control-label">{{ trans('dashitems.orderby') }}</label>
								</div>

								<div class="well">
									
									<div class="order-container"></div>

									<div class="controls">
										<div class="btn-group">
											<button class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
												<i class="icon-plus"></i> {{ trans('dashitems.addfield') }} <span class="caret"></span>
											</button>
											<ul class="dropdown-menu" id="db-add-order-dropdown">
												<li class="dropdown-header">{{ trans('dashitems.selectatemplatefirst') }}</li>
											</ul>
										</div>
									</div>
									
								</div>
							</fieldset>
						</form>
					</div>
					
					
					<div id="step-display" class="step-pane">
						<h1>{{ trans('dashitems.displayoptions') }}</h1>
						<fieldset class="col-sm-12">
							<div class="form-group">
								<label class="control-label" for="display-select">{{ trans('dashitems.charttype') }} <i class="icon-spinner icon-spin" style="display:none"></i></label>
								<div class="controls">
									<select style="width: 100%;" id="display-select" name="chart" class="form-control" data-rel="chosen" data-placeholder="Choose a Chart...">
										<option value="table">{{ trans('dashitems.table') }}</option>
										<option value="pie">{{ trans('dashitems.piechart') }}</option>
										<option value="line">{{ trans('dashitems.linechart') }}</option>
										<option value="bar">{{ trans('dashitems.barchart') }}</option>
										<option value="indicator">{{ trans('dashitems.indicatorchart') }}</option>
									</select>
								</div>
							</div>
						</fieldset>
						
						<fieldset class="col-sm-12">
							<div class="form-group">
								<label class="control-label">{{ trans('dashitems.chartoptions') }}</label>
							</div>

							<div id="display-options" class="well">
								<p>{{ trans('dashitems.nooptionsavailable') }}</p>
							</div>
						</fieldset>
					</div>
					
					<div id="step-preview" class="step-pane">
						<h1>{{ trans('dashitems.preview') }}</h1>
						<div class="form-group">
							<label class="control-label" for="title">{{ trans('dashitems.title') }}</label>
							<div class="controls">
								<input class="form-control" id="title" name="title" type="text" value="" required autofocus />
							</div>
						</div>
						<div id="preview-spinner" style="text-align:center; padding: 20px;">
							<i style='font-size: 64px' class="icon-spinner icon-spin"></i>
						</div>
						<div id="dashitem-preview">
						
						</div>
						<!--<div class="clearfix">
							<div class="col-lg-12 inh_nopadding">
								<div class="form-actions clearfix text-center">
									<button disabled id="dashitem-submit" type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending...">Save changes</button>
								</div>
							</div>
						</div>-->
					</div>

					<div id="step-map" class="step-pane">
						<h1>{{ trans('dashitems.mapdisplayoptions') }}</h1>
						<fieldset class="col-sm-12">
							<div class="form-group">
								<label class="control-label" for="map-select">{{ trans('dashitems.map') }} <i class="icon-spinner icon-spin" style="display:none"></i></label>
								<div class="controls">
									<select style="width: 100%;" id="map-select" name="chart" class="form-control" data-rel="chosen" data-placeholder="Choose a Chart...">
										@foreach( $maps as $map )
												<option value="{{ $map->id }}">{{ $map->title }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</fieldset>

						<input id="check-political" type="hidden" name="political" value="false" />
						
						<fieldset class="col-sm-12">
							<div class="form-group">
								<label class="control-label">{{ trans('dashitems.mapoptions') }}</label>
							</div>

							<div id="map-options" class="well">
								<p>{{ trans('dashitems.nooptionsavailable') }}</p>
							</div>
						</fieldset>
					</div>

				</div>
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->

@stop

@section('specific-css-plugins')
@stylesheets('cr_expreditor')
@stop

@section('specific-javascript-plugins')
@javascripts('cr_dashitem')
@javascripts('cr_expreditor')
@javascripts('cr_maps')
@stop


@section('custom-javascript')
<script>

var CR_TEMPLATES = {{ json_encode(array_combine(array_column($templates->toArray(),'id'), $templates->toArray())) }};

var wizardForward;

var isMap = false;

$(document).ready(function(){
	//Toggle buttons
	$('.quick-button').on('click', function() {
		$('.quick-button').removeClass('pressed');
		$(this).addClass('pressed');

		if ( $(this).attr('id') == 'chart-type')
		{
			isMap = false;

			if ( $('#step-chart').val() != null )
			{
				$('#step-display').attr('id', 'step-map');
				$('#step-chart').attr('id', 'step-display');
				$('#display-select').attr('id', 'map-select');
				$('#chart-select').attr('id', 'display-select');
				$('#display-options').attr('id', 'map-options');
				$('#chart-options').attr('id', 'display-options');
			}
		}
		else
		{
			isMap = true;

			if ( $('#step-map').val() == '' )
			{
				$('#step-display').attr('id', 'step-chart');
				$('#step-map').attr('id', 'step-display');
				$('#display-select').attr('id', 'chart-select');		
				$('#map-select').attr('id', 'display-select');
				$('#display-options').attr('id', 'chart-options');
				$('#map-options').attr('id', 'display-options');
			}	
		}

		if ($(this).attr('id') == 'political-type')
		{
			$('#check-political').val(true);
		}
		else
		{	
			$('#check-political').val(false);
		}
			
	});

	$('#dashboard-wizard')
		.on('change', function(ev, d)
		{
			wizardForward = (d.direction == 'next');
			if (wizardForward && d.step == 2 && !DashItems.validateFrom())
			{
				ev.preventDefault();
			}
			if (wizardForward && d.step == 3 && !DashItems.validate())
			{
				ev.preventDefault();
			}
			if (wizardForward && d.step == 5 && !DasItems.validateTitle())
			{
				ev.preventDefault();
			}
		})
		.on('changed', function()
		{
			//Initialize select when it is visible
			var step = $('#dashboard-wizard').data('wizard').currentStep
			$('#dashitem-button-next').prop('disabled', false);
			if (step == 2)
			{
				$('#template-select').chosen();
				if (wizardForward)
					DashItems.resetJoinTemplate();
			}
			if (step == 3 && wizardForward)
				DashItems.updateTemplateFields();
			if (step == 4 && wizardForward)
				DashDisplay.wizardDisplay();
			else if (step == 5)
				DashDisplay.wizardPreview();
		})
		.on('finished', DashItems.store);
	
	$(document).on('click', '.cr-join-condition, .cr-filter-condition, .cr-expression-condition', function(){
		var selectedTemplates = [];
		$('#template-join-container select').each(function(k,v) {
			selectedTemplates.push(CR_TEMPLATES[parseInt($(v).val())]);
		});

		ExprEditor.showExprEditor(this, selectedTemplates);
	});

	LocationPicker.init( $('form.form-report'), 'report' );
});
</script>
@stop

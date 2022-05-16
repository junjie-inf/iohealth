/**
 * DashDisplay Lib
 * 
 * This library contains methods to manage dashboards displays.
 * 
 * @type type
 */

var DashDisplay = {
	numericFields: [],
	allFields: [],
	dateFields: [],
	expressionFields: [],
    selectedTemplates: [],
	
	init: function()
	{
		var self = this;
		
		$(document).ready(function(){
			$(document)
				// Actualizar valor de input range
				.on('change', '#display-select', function(){
					if (!isMap)
						self.changeChartType($(this).val());
				}
)				.on('click', '.action-add-line-series', function(){
					self.lineAddSeriesByButton($(this));
				}
)				.on('click', '.action-add-bar-series', function(){
					self.barAddSeriesByButton($(this));
				}
)				.on('click', '.action-add-indicator-series', function(){
					self.indicatorAddSeriesByButton($(this));
				});
		});
	},
	
	lineAddSeriesByButton: function ($button)
	{
		var d3_category10 = [ "#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd", "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf" ];
		var count = $('.line-series-container').children().length;
		
		this.lineAddSeries({
			data: $button.data('id'),
			title: $button.data('alias'),
			color: d3_category10[count % 10],
			area: false,
		});

	},
	
	lineAddSeries: function ( line )
	{
		var html = Handlebars.compile( $("#tpl-chart-line-series").html() )({
			title: line.title,
			id: line.data,
			color: line.color,
			area: line.area,
		});
	
		$(html)
			.hide()
			.appendTo( $('.line-series-container') )
			.slideDown('fast').find('.color-picker').colorpicker();

	},
	
	lineParseOptions: function ()
	{
		var options = {
			'axis': {
				'x': {
					'data': $('#line-xaxis-col').val(),
					'label': $('#line-xaxis-label').val(),
					'datatype': $('#line-xaxis-col option:selected').data('type')
				},
				'y': {
					'label': $('#line-yaxis-label').val()
				}
			},
			'series': []
		};
		
		$('.line-series-container').children().each(function(i,d){
			var row = $(d);
			
			var s = {
				'data' : row.find('.field-name').data('id'),
				'title': row.find('.field-name').val(),
				'color': row.find('.color-picker').val(),
				'area' : row.find(':checkbox').prop('checked'),
			};
			options.series.push(s);
		});

		
		return options;
	},
	
	lineFromJson: function ( options )
	{
		$('#line-xaxis-col').val(options.axis.x.data);
		$('#line-xaxis-label').val(options.axis.x.label);
		$('#line-yaxis-label').val(options.axis.y.label);
		
		for(var i = 0; i < options.series.length; i++)
		{
			this.lineAddSeries(options.series[i]);
		}

	},

	barAddSeriesByButton: function ($button)
	{
		var d3_category10 = [ "#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd", "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf" ];
		var count = $('.bar-series-container').children().length;
		
		this.barAddSeries({
			data: $button.data('id'),
			title: $button.data('alias'),
			color: d3_category10[count % 10],
		});

	},
	
	barAddSeries: function ( bar )
	{
		var html = Handlebars.compile( $("#tpl-chart-bar-series").html() )({
			title: bar.title,
			id: bar.data,
			color: bar.color
		});
	
		$(html)
			.hide()
			.appendTo( $('.bar-series-container') )
			.slideDown('fast').find('.color-picker').colorpicker();

	},
	
	barParseOptions: function ()
	{
		var options = {
			'axis': {
				'x': {
					'data': $('#bar-xaxis-col').val(),
					'label': $('#bar-xaxis-label').val(),
					'datatype': $('#bar-xaxis-col option:selected').data('type')
				},
				'y': {
					'label': $('#bar-yaxis-label').val()
				}
			},
			'series': []
		};
		
		$('.bar-series-container').children().each(function(i,d){
			var row = $(d);
			
			var s = {
				'data' : row.find('.field-name').data('id'),
				'title': row.find('.field-name').val(),
				'color': row.find('.color-picker').val()
			};
			options.series.push(s);
		});

		return options;
	},
	
	barFromJson: function ( options )
	{
		$('#bar-xaxis-col').val(options.axis.x.data);
		$('#bar-xaxis-label').val(options.axis.x.label);
		$('#bar-yaxis-label').val(options.axis.y.label);
		
		for(var i = 0; i < options.series.length; i++)
		{
			this.barAddSeries(options.series[i]);
		}

	},

	indicatorAddSeriesByButton: function ($button)
	{
		var count = $('.indicator-series-container').children().length;
		
		this.indicatorAddSeries({
			data: $button.data('id'),
			title: $button.data('alias'),
		});

	},
	
	indicatorAddSeries: function ( indicator )
	{
		var html = Handlebars.compile( $("#tpl-chart-indicator-series").html() )({
			title: indicator.title,
			id: indicator.data,
		});
	
		$(html)
			.hide()
			.appendTo( $('.indicator-series-container') )
			.slideDown('fast');
	},
	
	indicatorParseOptions: function ()
	{
		var options = {
            'labels': $('#indicator-labels-select').val(),
			'series': []
		};

		$('.indicator-series-container').children().each(function(i,d){
			var row = $(d);

			var s = {
				'data' : row.find('.field-name').data('id'),
				'title': row.find('.field-name').val()
			};
			options.series.push(s);
		});

		return options;
	},
	
	indicatorFromJson: function ( options )
	{
		$('#indicator-labels-select').val(options.labels);
		
		for(var i = 0; i < options.series.length; i++)
		{
			this.indicatorAddSeries(options.series[i]);
		}

	},
	
	changeChartType : function (chartType)
	{
		var options = $('#display-options');
		options.children().remove();
		
		html = Handlebars.compile( $("#tpl-display-options").html() )({
			'chartType': chartType,
			'allFields' : this.allFields,
			'numericFields' : this.numericFields,
			'dateFields' : this.dateFields,
			'expressionFields' : this.expressionFields,
			'selectedTemplates' : this.selectedTemplates,
		});
		
		$(html).hide().appendTo(options).slideDown('fast').find('select').chosen();
	},
	
	processForm: function ( from )
	{
		var chartType = $('#display-select').val();
		
		var options = $('#display-options');
		var opt = {};
		
		switch (chartType)
		{
			case 'table':
				break;
			case 'pie':
				opt = {
					'labels': $('#pie-labels-select').val(),
					'values': $('#pie-values-select').val(),
				};
				break;
			case 'line':
				opt = this.lineParseOptions();
				break;
			case 'bar':
				opt = this.barParseOptions();
				break;
			case 'indicator':
				opt = this.indicatorParseOptions();
				break;
			default:
				chartType = 'map';
				opt = {
					'map': parseInt($('#display-select').val()),
					'geolocationFrom': parseInt($('#geolocationfrom-select').val()),
				};
				break;
		}
		
		var display = {
			'type': chartType,
			'options': opt,
		};
		

		return display;
	},
	
	fromJson: function ( display )
	{
		//Change chart type and fire events
		if (display.type == "map") 
		{

			if($('#check-political').val() == 'true')
			{
				$('#political-type').trigger('click');
			}
			else
			{
				$('#map-type').trigger('click');
			}			
		}

		$('#display-select').val(display.type).trigger('change');

		switch(display.type)
		{
			case 'table':
				break;
			case 'pie':
				$('#pie-labels-select').val(display.options.labels);
				$('#pie-values-select').val(display.options.values);
				break;
			case 'line':
				this.lineFromJson( display.options );
				break;
			case 'bar':
				this.barFromJson( display.options );
				break;
			case 'indicator':
				this.indicatorFromJson( display.options );
				break;
			case 'map':
				$('#display-select').val(display.options.map);
				$('#geolocationfrom-select').val(display.options.geolocationFrom);
				break;
		}
		
		//Update all chosen selectes
		$('select').trigger('chosen:updated');
	},
	
	wizardDisplay: function ()
	{
		$('#display-select').chosen();
		this.fields = DashItems.getSelectedFields();
		this.selectedTemplates = DashItems.getSelectedTemplates();
	
		this.allFields = [];
		this.numericFields = [];
		this.dateFields = [];
		this.expressionFields = [];
		for (var i = 0; i < this.fields.length; i++)
		{
			var f = this.fields[i];
			f.selId = i;
			
			if ($.inArray(f.datatype, ['number', 'range', 'decimal']) != -1)
				this.numericFields.push(f);
			if ($.inArray(f.datatype, ['date']) != -1)
				this.dateFields.push(f);
			// REVISAR TEMA DE EXPRESIONES, tal vez indicar el tipo de expresion seria lo mas correcto
			if ($.inArray(f.type, ['expression']) != -1)
				this.expressionFields.push(f);
			this.allFields.push(f);
		}
		
		if (isMap)
			this.changeChartType("map");
		else
			this.changeChartType($('#display-select').val());	
		
 
	},
	
	wizardPreview: function ()
	{
		crHooks = [];
		$('#preview-spinner').show();
		$('#dashitem-preview').children().remove();
		$('#dashitem-button-next').prop('disabled', true);
		$.post($SITE_PATH + '/dashitem/preview', {'d': JSON.stringify(DashItems.getJson())}, function(d){
			$('#dashitem-preview').html(d);
			for (var i = 0; i < crHooks.length; i++)
			{
				crHooks[i]();
			}
			$('#preview-spinner').hide();
			$('#dashitem-button-next').prop('disabled', false);
		});	

	},
};

// Init Lib
DashDisplay.init();

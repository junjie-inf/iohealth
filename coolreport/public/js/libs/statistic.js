/**
 * Statistics Lib
 * 
 * This library contains methods to manage statistics.
 * 
 * @type type
 */

var Statistics = {
	
	chosenClass: '[data-rel="chosen"],[rel="chosen"]',

	from: moment().subtract('days', 29), // Date from
	
	to: moment(), // Date to
			
	myChart: null,
	
	type: null,
	
	data: null,
	
	// Almacén de datos
	cache: {
		templates: new Array(),
		fields: new Array()
	},
	
	init: function()
	{
		var self = this;
		
		$(document).ready(function(){	
			
			self.setGraphic();
			
			// Petición datos iniciales
			Forms.get( $SITE_PATH + 'template', {}, $('.apply-filter'), function(d){
				$(d.data).each(function(k, v){
					
					// Cargo Templates
					var templateDecoded = $.parseJSON(v.template);
					var temp = new Array();
					$.each(templateDecoded, function(id, attributes){
						$(attributes).each(function(k2, attribute){
							switch( attribute.type )
							{
								case 'checkbox':
								case 'radio':
								case 'select':
									temp.push( {id: id, title: attribute.label} );
									break;
							}
						});
					});
					
					// Cargo Templates
					self.cache.templates[v.id] = {id: v.id, title: v.title, fields: temp};
				});
				
				self.loadTemplates();
			});
			
			// Aplico Chosen
			self.applyChosen();
			
			/* -------- Bootstrap Date Range Picker ----- */
			$('#reportrange-statistics').daterangepicker({
				ranges: {
				   'Hoy': [moment(), moment()],
				   'Ayer': [moment().subtract('days', 1), moment().subtract('days', 1)],
				   'Últimos 7 días': [moment().subtract('days', 6), moment()],
				   'Últimos 30 días': [moment().subtract('days', 29), moment()],
				   'Este mes': [moment().startOf('month'), moment().endOf('month')],
				   'Último mes': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
				},
				startDate: self.from,
				endDate: self.to
			}, function(from, to) {
				//console.log(' A date range was chosen: ' + from.format('YYYY-MM-DD') + ' to ' + to.format('YYYY-MM-DD'));
				$('#reportrange-statistics span').html(from.format('MMM D, YYYY') + ' - ' + to.format('MMM D, YYYY'));
				self.from = from;
				self.to = to;
			});
			
			$(document)
				// Load options in selects
				.on('change', '#select-template', function(){
					var template_selected = $(this).val();
					self.loadFields( $('#select-field'), template_selected );
				})
				// Load options in selects
				.on('change', '#select-field', function(){
					self.applyFilter( $('.apply-filter') );
				})
				// Apply filter
				.on('click', '.apply-filter', function(){
					self.applyFilter( $(this) );
				})
				// Apply type
				.on('change', '#select-plot', function(){
					self.type = $(this).val();
					self.refresh();
				});
				
			$('#select-plot').change();
		});
	},
	
	/**
	 * Carga los Templates.
	 */
	loadTemplates: function()
	{
		var self = this,
			$select = $('#select-template');
		
		$select.find('option').remove();
		$select.append('<option value="" disabled selected>Select a Template...</option>');
				
		$(self.cache.templates).each(function(k,v){
			if( k > 0 ){
				$select.append('<option value="' + v.id + '">' + v.title + '</option>');
			}
		});
		
		self.applyChosen(true);
	},
	
	/**
	 * Carga los Fields.
	 * 
	 * @param jQuery $select Select donde se cargarán los fields
	 * @param int template_id ID del Template del que se quieren cargar los fields
	 */
	loadFields: function( $select, template_id )
	{
		var self = this;
		
		$select.find('option').remove();
		$select.append('<option value="" disabled selected>Select a Field...</option>');
		
		$(self.cache.templates[template_id].fields).each(function(k,v){
			$select.append('<option value="' + v.id + '">' + v.title + '</option>');
		});
		
		self.applyChosen(true);
	},
	
	/**
	 * Aplica los filtros seleccionados y pinta la gráfica.
	 * 
	 * @param {type} $button
	 * @returns {undefined}
	 */
	applyFilter: function( $button )
	{
		var self = this,
			template_id = $('#select-template').val(),
			field_id = $('#select-field').val();
		
		Forms.post($SITE_PATH + 'statistic/query', {template_id: template_id, field_id: field_id, from: self.from.format('X'), to: self.to.format('X')}, $button, function(d){
			//console.log( d );
			self.setGraphicData( d.data );
		});
	},
		
	
	applyChosen: function( refresh )
	{
		refresh = refresh || false;
		
		if( refresh )	$(this.chosenClass).trigger("chosen:updated");
		else			$(this.chosenClass).chosen();
	},
	
	setGraphic: function()
	{
		var self = this;
		
		//-- xChart --
		var tt = document.createElement('div'),
			leftOffset = -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', ''))+50,
			topOffset = -36;
		tt.className = 'ex-tooltip';
		document.body.appendChild(tt);
		
		var data = {
			"xScale": "ordinal",
			"yScale": "linear",
			"main": [{"className": ".topReport .cursor-pointer"}]
		};

		var opts = {
			"dataFormatX": function (x) { return x; },
			"tickFormatX": function (x) { return x; },
			axisPaddingTop: 5,
			"mouseover": function (d, i) {
				var pos = $(this).offset();
				$(tt).html('<strong>'+d.x + '</strong> (selected ' + d.y + ' times)')
					.css({top: topOffset + pos.top, left: pos.left + leftOffset})
					.show();
			},
			"mouseout": function (x) {
				$(tt).hide();
			},
			"click": function (x) {
				window.location.href = x.url;
			},
			"empty": function (x) {
				$('#chart-graphic .empty-msg').show();
			},
			"notempty": function (x) {
				$('#chart-graphic .empty-msg').hide();
			}
		};
		
		self.myChart = new xChart('bar', data, '#chart-graphic', opts);
	},
	
	setGraphicData: function( d )
	{
		var self = this;
		self.data = d;
		
		self.refresh();
	},	

	refresh: function()
	{
		var self = this;
		
		if( self.data !== null )
		{
			var data = {
				"xScale": "ordinal",
				"yScale": "linear",
				"type": self.type,
				"main": [
				{
					"className": ".topReport .cursor-pointer",
					"data": self.data
				}
				]
			};
			
			self.myChart.setData(data);
		}
	}
};
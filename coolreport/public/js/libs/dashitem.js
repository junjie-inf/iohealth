/**
 * DashItems Lib
 * 
 * This library contains methods to manage dashboards.
 * 
 * @type type
 */

var DashItems = {

	fieldsContainerClass: '.fields-container',
	groupsContainerClass: '.groups-container',
	orderContainerClass: '.order-container',
	numericFieldList : [],
	countTemplates: 1,

	icons: {
		'checkbox' : 'icon-check',
		'color' : 'icon-tint',
		'date' : 'icon-calendar',
		'decimal' : 'icon-euro',
		'email' : 'icon-envelope-alt',
		'file' : 'icon-paper-clip',
		'number' : 'icon-tag',
		'radio' : 'icon-circle-blank',
		'range' : 'icon-resize-horizontal',
		'select' : 'icon-collapse',
		'text' : 'icon-terminal',
		'textarea' : 'icon-align-left',
		'time' : 'icon-time',
		'report' : 'icon-file-text-alt',
		'expression' : 'icon-terminal',
		
		'count' : 'icon-sort-by-order',
		'sum' : 'icon-plus',
		'avg' : 'icon-resize-small',
		'min' : 'icon-sort-down',
		'max' : 'icon-sort-up',
	},
	
	fixedFields : [
		{
			'id'   : '_id',
			'label': 'Id',
			'type' : 'number'
		},
		{
			'id'   : '_title',
			'label': 'Title',
			'type' : 'text'
		},
		{
			'id'   : '_latitude',
			'label': 'Latitude',
			'type' : 'decimal'
		},
		{
			'id'   : '_longitude',
			'label': 'Longitude',
			'type' : 'decimal'
		},
		{
			'id'   : '_address',
			'label': 'Address',
			'type' : 'text'
		},
		{
			'id'   : '_date',
			'label': 'Date',
			'type' : 'date'
		},
	],

	templates: {
		
		option: '<div class="controls">\
					<label class="inh_nopadding-top">\
						<div class="input-group input-group-sm">\
							<input type="text" class="form-control field-option" placeholder="Option title" required>\
							<span class="input-group-btn">\
								<button class="btn btn-info up-option" type="button"><i class="icon-arrow-up"></i></button>\
								<button class="btn btn-info down-option" type="button"><i class="icon-arrow-down"></i></button>\
								<button class="btn btn-danger remove-option" type="button"><i class="icon-remove"></i></button>\
							</span>\
						</div>\
					</label>\
				</div>'
	},
	
	init: function()
	{
		var self = this;
		
		$(document).ready(function(){
			self.adaptRanges();
			
			// Si existe un div.report-data-view ajusto el formulario
			// al modo readonly
			if( $('.report-data-view').length > 0 ){
				self.adaptReadOnly();
			}
			
			$(document)
				// Actualizar valor de input range
				.on('change', 'input[type="range"]', function(){
					var $this = $(this);
					console.log($this.val());
					$this.next('.range-data').find('.range-value').html( $this.val() );
				})
				// Eliminar archivo
				.on('click', '.action-remove-file', function(){
					var $this = $(this),
						$dz_preview = $this.closest('.dz-preview'),
						$file = $dz_preview.prev('input.form-file[type="hidden"]');
					
					console.log($file);
					$file.remove();
					
					$dz_preview.hide('fast', function(){
						$(this).remove();
						
						DashItems.initDropzone();
					});
					
					
				})
				// Inputs resaltados (caso required)
				.on('click', '.field-required', function(){
					$(this).parent().siblings('.input-group').toggleClass('has-warning');
				})
				// Selects multiples
				.on('click', '.field-multiple', function(){
					var $select = $(this).closest('.field-row').find('select'),
						attr = $select.attr('multiple');
					
					$select.toggleClass('input-sm');
					
					if( typeof attr !== 'undefined' && attr !== false ){
						$select.removeAttr('multiple');
					}else{
						$select.attr('multiple', 'multiple');
					}
				})
				// Add field
				.on('click', '.action-add-field', function(){
					self.addFieldByButton( $(this) );
				})
				// Add group by
				.on('click', '.action-add-group', function(){
					self.addGroupByButton( $(this) );
				})
				// Add order by
				.on('click', '.action-add-order', function(){
					self.addOrderByButton( $(this) );
				})
				// Add aggregate
				.on('click', '.action-add-aggregate', function(){
					self.addAggregateByButton( $(this) );
				})
				// Add expression
				.on('click', '.action-add-expression', function(e){
					e.preventDefault();
					self.addExpression( $(this) );
				})
				// Remove field
				.on('click', '.action-remove-field', function(e){
					e.preventDefault();
					self.confirmRemoveElement( $(this).closest('.field-row') );
				})
				// Move up field
				.on('click', '.action-up-field', function(e){
					e.preventDefault();
					self.moveField( $(this).closest('.field-row'), 0 );
				})
				// Move down field
				.on('click', '.action-down-field', function(e){
					e.preventDefault();
					self.moveField( $(this).closest('.field-row'), 1 );
				})
				// Add option
				.on('click', '.add-option', function(e){
					e.preventDefault();
					$(self.templates.option)
						.hide()
						.insertBefore( $(this) )
						.slideDown('fast');
					
				})
				// Remove option
				.on('click', '.remove-option', function(e){
					e.preventDefault();
					self.removeElement( $(this).closest('.controls') );
				})
				// Up option
				.on('click', '.up-option', function(e){
					e.preventDefault();
					self.moveElement( $(this).closest('.controls'), 0 );
				})
				// Down option
				.on('click', '.down-option', function(e){
					e.preventDefault();
					self.moveElement( $(this).closest('.controls'), 1 );
				})
				
				//Joins
				.on('click', '.btn-join-add', function(e) {
					e.preventDefault();
					self.addJoinTemplate();
				})
				.on('click', '.btn-join-reset', function(e) {
					e.preventDefault();
					self.resetJoinTemplate();
				})
				
				//Aggregate filters
				.on('click', '.agg-filter', function() {
					var selectedTemplates = [];
					$('#template-join-container select').each(function(k,v) {
						selectedTemplates.push(CR_TEMPLATES[parseInt($(v).val())]);
					});
					ExprEditor.showExprEditor(this, selectedTemplates);
				});
			
			// Store dashboard
			$('.form-dashboard-store').on('submit', function(e) {
				e.preventDefault();
				self.store();
			});
			
			// Update dashboard
			$('.form-dashboard-update').on('submit', function(e) {
				e.preventDefault();
				self.update();
			});
			
			// Store dashboard
			$('#dashitem-submit').on('click', function(e) {
				e.preventDefault();
				self.store( );
			});
			
			// Pickers
			self.setPickers();
			
			// Chosen
			self.setChosen();
		});
	},
	
	addJoinTemplate : function(prevData)
	{
		var selectedTemplates = [];
		$('#template-join-container select').each(function(k,v) {
			selectedTemplates.push(parseInt($(v).val()));
		});

		var remainingTemplates = [];
		$.each(CR_TEMPLATES, function(key, value){
			if ($.inArray(value.id, selectedTemplates) == -1)
				remainingTemplates.push(value);
		});

		if (remainingTemplates.length == 1)
			$('.btn-join-add').prop('disabled', true);
		
		var html = Handlebars.compile( $("#tpl-template-join").html() )({
			templates: remainingTemplates,
			prev_data: prevData
		});
		$('#template-join-container select').prop('disabled', true).trigger("chosen:updated");
		$(html)
			.hide()
			.appendTo( $('#template-join-container') )
			.slideDown('fast')
			.find('select').chosen();

		countTemplates += 1;
	},
	
	resetJoinTemplate : function()
	{
		$('#template-join-container .form-group').each(function(k,v) {
			if (k >= 1)
				$(v).slideUp('fast', function(){ $(this).remove() });
		});
		$('select').prop('disabled', false).trigger('chosen:updated');
		$('.btn-join-add').prop('disabled', false);

		countTemplates = 1;
	},
	
	updateTemplateFields : function( $this )
	{
		//Update fields
		var self = this;
		
		var templates = [];
		$('#template-join-container select').each(function(k,v) {
			var templateId = parseInt($(v).val());
			var selectedTemplate = CR_TEMPLATES[templateId];
			for (var i = 0; i < selectedTemplate.fields.length; i++) {
				selectedTemplate.fields[i].template = selectedTemplate;
			}
			templates.push(selectedTemplate);
		});
		DashItems.initFields(templates);
		
		/*var template_id = $this.val(),
			$fieldset = $('.insert-template'),
			$spinner = $this.closest('.form-group').find('.icon-spinner');

		$spinner.show(); // SPINNER on

		$.getJSON( $SITE_PATH + 'template/'+template_id, {readonly: false}, function(d){

			if( (d !== null) && (typeof d.status !== "undefined") && (d.status !== null) && (d.status === "OK") ){
				DashItems.initFields(d.data.fields);

				$('.insert-template select').chosen();
				

			}
			$spinner.hide(); // SPINNER off
			return false;
		})
		.fail(function(d){
			$spinner.hide(); // SPINNER off

			d = d.responseJSON;

			if( d.status === 'ERROR' ){
				$.each(d.messages, function(field, message){
					Forms.addAlert( field, message );
				});
			}else{ // status === FATAL
				Forms.addAlert( 'error', $_LANG.GENERIC.error_msg );
			}
		});*/
	},
	
	adaptRanges: function()
	{
		$('input[type="range"]').each(function(k,v){
			var $this = $(this);
			
			$this.css('margin-bottom', '5px');
			
			if( ! $this.next().hasClass('range-data') ){
				$this.after('<div class="range-data text-center">'+
						'<span class="pull-left label label-default">Min ' + $this.attr('min') + '</span>'+
						'<span class="range-value label label-info">' + $this.val() + '</span>'+
						'<span class="pull-right label label-default">Max ' + $this.attr('max') + '</span></div>');
			}
		});
	},
	
	confirmRemoveElement: function( $element )
	{
		bootbox.confirm('<h2><small class="inh_bold text-danger"><i class="icon-remove icon-2x"></i> El campo se eliminará</small></h2>', function(result){
			if( result ){
				// Elimino fila
				DashItems.removeElement($element);
			}
		});
	},
	
	removeElement: function( $element )
	{
		$element.fadeTo('slow', 0.00, function(){ //fade
			$(this).slideUp("slow", function() { //slide up
				$(this).remove(); //then remove from the DOM
			});
		});
	},
	
	moveElement: function( $element, mode )
	{
		if( mode === 0 )
		{
			if( $element.prev('.controls').length > 0 )
			{
				$element.hide('fast', function(){
					var $this = $(this);
					$this.prev('.controls').before( $this );
					$this.show('fast');
				});
			}
		}
		else
		{
			if( $element.next('.controls').length > 0 )
			{
				$element.hide('fast', function(){
					var $this = $(this);
					$this.next('.controls').after( $this );
					$this.show('fast');
				});
			}
		}
	},
	
	moveField: function( $element, mode )
	{
		if( mode === 0 )
		{
			if( $element.prev('.field-row').length > 0 )
			{
				$element.hide('fast', function(){
					var $this = $(this);
					$this.prev('.field-row').before( $this );
					$this.show('fast');
				});
			}
		}
		else
		{
			if( $element.next('.field-row').length > 0 )
			{
				$element.hide('fast', function(){
					var $this = $(this);
					$this.next('.field-row').after( $this );
					$this.show('fast');
				});
			}
		}
	},
	
	addGroupByButton: function( $button )
	{
		this.addGroup({
			type: $button.data('type'),
			title: $button.data('title'),
			id: $button.data('id'),
			template: $button.data('template-id')
		});
	},
	
	addGroup: function( field )
	{
		var template = CR_TEMPLATES[field.template];

		var html = Handlebars.compile( $("#tpl-create-template").html() )({
			type: field.type,
			icon: DashItems.icons[ field.type ],
			title: field.title,
			original_title: template.title,
			id: field.id,
			column_type: 'group',
			datetype: field.datetype,
			template: template
		});
	
		$(html)
			.hide()
			.appendTo( $(DashItems.groupsContainerClass) )
			.slideDown('fast');
	},
	
	addOrderByButton: function( $button, direction )
	{
		this.addOrder({
			type: $button.data('type'),
			title: $button.data('title'),
			id: $button.data('id'),
			direction: 'asc',
			template: $button.data('template-id')
		});
	},
	
	addOrder: function( field )
	{
		var template = CR_TEMPLATES[field.template];

		var html = Handlebars.compile( $("#tpl-create-template").html() )({
			type: field.type,
			icon: DashItems.icons[ field.type ],
			title: field.title,
			original_title: template.title,
			id: field.id,
			column_type: 'order',
			direction: field.direction,
			datetype: field.datetype,
			template: template
		});
	
		$(html)
			.hide()
			.appendTo( $(DashItems.orderContainerClass) )
			.slideDown('fast');
	},
	
	addFieldByButton: function( $button )
	{
		this.addField({
			type: $button.data('type'),
			title: $button.data('title'),
			alias: $button.data('title'),
			id: $button.data('id'),
			template: $button.data('template-id'),
			template_title: $button.data('template-title')
		});
	},
	
	findTemplateById: function ( id )
	{
		var templateFinded;

		$.each(CR_TEMPLATES, function(key, value)
		{
			if (key == id)
			{
				templateFinded = value;
				return false;
			}
		});

		return templateFinded;
	},
	
	findFieldById: function ( template, field )
	{
		//Buscar en campos fijos
		for(var i = 0; i < this.fixedFields.length; i++)
		{
			if (this.fixedFields[i].id == field)
				return this.fixedFields[i];
		}
		
		//Buscar en campos de plantilla
		var template = CR_TEMPLATES[template];
		for (var i = 0; i < template.fields.length; i++)
		{
			if (template.fields[i].id == field)
			{
				return template.fields[i];
			}
		}
	},
	
	addField: function( field )
	{
		var template = CR_TEMPLATES[field.template];
		
		var html = Handlebars.compile( $("#tpl-create-template").html() )({
			type: field.type,
			icon: DashItems.icons[ field.type ],
			title: field.alias,
			original_title: (countTemplates > 1) ? template.title + ' > ' + field.title : field.title,
			id: field.id,
			column_type: 'field',
			datetype: field.datetype,
			template: template,
			template_title: template.title,
		});

		$(html)
			.hide()
			.appendTo( $(DashItems.fieldsContainerClass) )
			.slideDown('fast');
	},
	
	addAggregateByButton: function( $button )
	{
		this.addAggregate({
			type: $button.data('type'),
		});
	},
	
	addAggregate: function ( field )
	{
		var unique_id = Date.parse(new Date());
		while ($('#'+unique_id).length)
			unique_id++;

		var html = Handlebars.compile( $("#tpl-create-template").html() )({
			type: field.type,
			icon: DashItems.icons[ field.type ],
			title: field.alias,
			column_type: 'aggregate',
			fields: this.numericFieldList,
			aggregate_column: field.column,
			filter: field.filter,
			unique_id: unique_id,
		});

		$(html)
			.hide()
			.appendTo( $(DashItems.fieldsContainerClass) )
			.slideDown('fast');
	},

	addExpression: function ( field )
	{
		var unique_id = Date.parse(new Date());
		while ($('#'+unique_id).length)
			unique_id++;

		var html = Handlebars.compile( $("#tpl-create-template").html() )({
			type: 'expression',
			icon: DashItems.icons[ 'expression' ],
			title: field.alias,
			column_type: 'expression',
			datetype: 'text',
			expression: field.expression,
			unique_id: unique_id,
		});

		$(html)
			.hide()
			.appendTo( $(DashItems.fieldsContainerClass) )
			.slideDown('fast');
	},
	
	fromJson: function( json, map )
	{
		//Joins
		for (var i = 1; i < json.from.length; i++)
		{
			this.addJoinTemplate(json.from[i]);
		}
		//Siguiente página, campos
		$('#dashboard-wizard').data('wizard').next()
		
		//Groups
		var groupButtons = $('#db-add-group-dropdown');
		for (var i = 0; i < json.group_by.length; i++)
		{
			var group = json.group_by[i];
			var f = this.findFieldById(group.template, group.column);
			this.addGroup({
				type: f.type,
				title: f.label,
				id: group.column,
				datetype: group.datetype,
				template: group.template,
			});
		}
		
		//Orders
		var orderButtons = $('#db-add-order-dropdown');
		for (var i = 0; i < json.order_by.length; i++)
		{
			var order = json.order_by[i];
			var f = this.findFieldById(order.template, order.column);
			this.addOrder({
				type: f.type,
				title: f.label,
				id: order.column,
				direction: order.direction,
				datetype: order.datetype,
				template: order.template,
			});
		}
		
		//Fields
		for (var i = 0; i < json.select.length; i++)
		{
			var field = json.select[i];
			if (field.type == 'json' || field.type == 'field')
			{
				//Button and alias
				this.addField({
					type: field.datatype,
					title: this.findFieldById(field.template, field.column).label,
					alias: field.alias,
					id: field.column,
					datetype: field.datetype,
					template: field.template,
					template_title: field.template_title
				});
			}
			else if (field.type == 'aggregate')
			{
				//Button, title and column (except count)
				this.addAggregate({
					type: field.function,
					alias: field.alias,
					column: field.column,
					filter: field.filter
				});
			}
			else if (field.type == 'expression')
			{
				//Button, title and column
				this.addExpression({
					type: field.type,
					alias: field.alias,
					datetype: field.datetype,
					expression: field.expression,
				})
			}
		}
	},
	
	store: function( )
	{
		bootbox.confirm('<h2><small class="inh_bold text-info"><i class="icon-question-sign icon-2x"></i> El elemento se creará</small></h2>', function(result){
			if( result ){
				DashItems.processForm( 'store' );
			}
		});
		return false;
	},
	
	update: function( )
	{
		var self = this;
		bootbox.confirm('<h2><small class="inh_bold text-info"><i class="icon-question-sign icon-2x"></i> Los cambios se guardarán</small></h2>', function(result){
			if( result ){
				DashItems.processForm( 'update' );
			}
		});
		return false;
	},

	getSelectedTemplates: function() {
		var selectedTemplates = [];
		$('#template-join-container .form-group').each(function(index, obj)
		{
			var row = $(obj);
			var template = CR_TEMPLATES[row.find('select').val()];
			var t = { 'id': template.id, 'title': template.title };
			
			selectedTemplates.push(t);
		});

		return selectedTemplates;
	},
	
	getSelectedFields: function() {
		var fields = [];
		var fieldsContainer = $( DashItems.fieldsContainerClass );

		// Normalize input for expressions and aggregations
		// Variable stores fieldData obtained below
		var normalized = {};
		var normalizeInput = function(id, fieldData)
		{
			if (fieldData['type'] == 'expression')
			{
				var origNew = fieldData['expression'];

				// Replace already present fields in new field
				for (var k in normalized)
				{
					if (normalized[k]['type'] == 'aggregate')
					{
						var expr;
						if (normalized[k]['function'] == 'count')
							expr = '1';

						else
							expr = $('#'+k).find('select').val();

						if (normalized[k]['filter'])
							expr = 'CASE WHEN ' + normalized[k]['filter'] + ' THEN ' + expr + ' ELSE NULL END';

						origNew = origNew.replace('$_'+k, normalized[k]['function'] + '(' + expr + ')');
					}

					else if (normalized[k]['type'] == 'expression')
						origNew = origNew.replace('$_'+k, normalized[k]['expression']);
				}

				fieldData['expression'] = origNew;
			}

			// Replace new field in already present fields
			// Can only replace in expressions
			for (var k in normalized)
			{
				if (normalized[k]['type'] == 'aggregate')
					continue;

				var orig = normalized[k]['expression'];

				if (fieldData['type'] == 'aggregate')
				{
					var expr;
					if (fieldData['function'] == 'count')
						expr = '1';

					else
						expr = $('#'+id).find('select').val();

					if (fieldData['filter'])
						expr = 'CASE WHEN ' + fieldData['filter'] + ' THEN ' + expr + ' ELSE NULL END';

					normalized[k]['expression'] = normalized[k]['expression'].replace('$_'+k, fieldData['function'] + '(' + expr + ')');
				}

				else if (fieldData['type'] == 'expression')
					normalized[k]['expression'] = normalized[k]['expression'].replace('$_'+id, fieldData['expression']);
			}

			normalized[id] = fieldData;
		};

		fieldsContainer.find('.field-row').each(function(index, obj)
		{
			$fieldRow = $(obj);
			$fieldName = $fieldRow.find('input.field-name');
			$fieldContainer = $fieldName.closest('.form-group');
			fieldId = $fieldName.attr('name');
			fieldTitle = $fieldContainer.find('div > .pull-left').text();
			type = $fieldRow.data('type');
			name = $fieldName.val();

			var columnType = $fieldName.data('type');
			
			if (columnType == 'field')
			{
				var fieldData = {
					'type'    : fieldId[0] == '_' ? 'field' : 'json',
					'alias'   : (countTemplates > 1) ? name + ' (' + fieldTitle + ')' : name,
					'datatype': type,
					'column'  : fieldId,
					'template': $fieldName.data('template-id'),
				};
				var datetype = $fieldRow.find('select.datetype-select');
				if (datetype.length > 0)
					fieldData['datetype'] = datetype.val();
			}
			else if (columnType == 'aggregate')
			{
				var fieldData = {
					'type'     : 'aggregate',
					'alias'    : name,
					'datatype' : 'decimal',
					'function' : type,
				};
				
				var fieldSelect = $fieldRow.find('select');
				if (fieldSelect.length)
				{
					var col = fieldSelect.val().substr(1); //Quita el $ inicial
					var cols = col.split('.');
					fieldData['column'] = cols[1];
					fieldData['template'] = cols[0];
				}
				var filter = $fieldRow.find('.agg-filter').val();
				if (filter)
					fieldData['filter'] = filter;

				// Defer push to fields array
				normalizeInput($fieldRow.attr('id'), fieldData);
				return;
			}
			else if (columnType == 'expression')
			{
				var fieldExpression = $fieldRow.find('.cr-expression-condition').val();
				var fieldData = {
					'type'     	: 'expression',
					'alias'    	: name,
					'datatype' 	: 'text',
					'expression': fieldExpression,
				}

				// Defer push to fields array
				normalizeInput($fieldRow.attr('id'), fieldData);
				return;
			}

			// Añado datos del campo al array
			fields.push(fieldData);
			console.log('*** añadido '+type+' ***');
		});

		// Add normalized expressions
		for (var k in normalized)
		{
			var fieldData = normalized[k];
			fields.push(fieldData);
			console.log('*** añadido '+fieldData['type']+' ***');
		}

		console.log(fields);
		return fields;
	},
	
	getJson : function()
	{
		var title = $('[name="title"]').val(),
			$fieldRow = null, // Fila del campo
			$fieldName = null, // Campo nombre del campo
			$fieldContainer = null, // Contenedor de required, show y multiple (parte izquierda)
			fieldId = null, // ID del field
			type = null; // Tipo de field

		// Recorro los campos insertados
		var groupsContainer = $( DashItems.groupsContainerClass );
		var groups = [];
		groupsContainer.find('.field-row').each(function(index, obj)
		{
			$fieldRow = $(obj);
			$fieldName = $fieldRow.find('input.field-name');
			$fieldContainer = $fieldName.closest('.form-group');
			fieldId = $fieldName.attr('name');
			type = $fieldRow.data('type');
			name = $fieldName.val();

			var fieldData = {
				'template': $fieldName.data('template-id'),
				'column': fieldId
			};

			var datetype = $fieldRow.find('select.datetype-select');
			if (datetype.length > 0)
				fieldData['datetype'] = datetype.val();
			
			// Añado datos del campo al array
			groups.push(fieldData);
		});
		
		var orderBy = [];
		//Recorrer ordenaciones
		$( DashItems.orderContainerClass ).find('.field-row').each(function(index, obj)
		{
			$fieldRow = $(obj);
			$fieldName = $fieldRow.find('input.field-name');
			$fieldContainer = $fieldName.closest('.form-group');
			fieldId = $fieldName.attr('name');
			type = $fieldRow.data('type');
			name = $fieldName.val();
			var dir = $fieldRow.find('select.direction-select').val();

			// Añado datos del campo al array
			var fieldData = {
				'template': $fieldName.data('template-id'),
				'column' : fieldId, 
				'direction' : dir 
			}

			var datetype = $fieldRow.find('select.datetype-select');
			if (datetype.length > 0)
				fieldData['datetype'] = datetype.val();

			orderBy.push(fieldData);
		});
		
		var from = [];
		//Recorrer joins
		$('#template-join-container .form-group').each(function(index, obj)
		{
			var row = $(obj);
			var t = { 'template': row.find('select').val() };
			
			var cond = row.find('input.form-control').val();
			if (cond)
				t.condition = cond;
			from.push(t);
		});
		
		var data = {
			'from' : from,
			'select' : this.getSelectedFields(),
			'group_by' : groups,
			'order_by' : orderBy,
			'filter' : $('.cr-filter-condition').val()
		};
		
		var dashboard = {
			'title' : title,
			'data'   : data,
			'display': DashDisplay.processForm(from),
			'political': ($('#check-political').val() == "true"),
		};
		
		console.log('------------------------------');
		console.log('JSON dashboard:');
		console.log(dashboard);
		console.log('------------------------------');
		console.log( $.toJSON(dashboard) );
		
		return dashboard;
	},
	
	processForm: function( action ) {
		var dashboard = this.getJson();
		$button = $('#dashitem-submit');

		if( action === 'store' ) // Store
		{
			Forms.post($SITE_PATH + 'dashitem', {
				title: dashboard.title,
				dashboard: $.toJSON(dashboard)
			}, $button);
		}
		else // Update
		{
			Forms.post($SITE_PATH + 'dashitem/' + $('.form-dashboard-update').data('id'), {
				title: dashboard.title,
				dashboard: $.toJSON(dashboard),
				_method: 'PUT',
			}, $button);
		}
	},
	
	/**
	 * Adapt form to text-like view
	 */
	adaptReadOnly: function()
	{
		var self = this,
			fileinputs = $('.report-data-view input[type="file"]');
		
		fileinputs.first().before('No files uploaded');
		fileinputs.hide();

		$('.report-data-view .color-picker').each(function(k,v){
			var $this = $(this),
				color =  $this.val();

			$this.before('<span class="color-sample" style="background:' + color + '"></span><span class="color-sample-code">' + color + '</span>')
				 .remove();
		});

		$('.report-data-view input[type="range"]').each(function(k,v){
			var $this = $(this),
				value = parseInt( $this.val() ),
				min = parseInt( $this.attr('min') ),
				max = parseInt( $this.attr('max') ),
				id = $this.attr('name');

			$this
				.before('<div id="slider-' + id + '" class="progress progressGreen margin-top-5 margin-bottom-5">' + (value*10) + '</div>'/*+
						'<div class="row">' +
							'<div class="col-xs-4 text-left">Min: '+min+'</div>' +
							'<div class="col-xs-4 text-center">Selected: '+value+'</div>' +
							'<div class="col-xs-4 text-right">Max: '+max+'</div>' +
						'</div>'*/)
				.remove();

			$('#slider-'+id).progressbar({ value: value, min: min, max: max });
		});

		$('.report-data-view select:not([multiple="multiple"])').each(function(k,v){
			var $this = $(this),
				$selected =  $this.find('option[selected="selected"]'),
				value = $selected.length > 0 ? $selected.text() : '';

			$this.before('<input type="text" class="form-control" value="'+value+'" />')
				 .remove();
		});
		
		self.setPickers();
		self.setChosen();
		
		$('form.report-content input').attr('readonly', 'readonly');
	},
	
	setPickers: function()
	{
		$('.color-picker:not([readonly="readonly"])').colorpicker();
		$('.date-picker:not([readonly="readonly"])').datepicker();
		$('.time-picker:not([readonly="readonly"])').timepicker();
	},
	
	setChosen: function()
	{
		$('[data-rel="chosen"][multiple="multiple"],[rel="chosen"][multiple="multiple"]').chosen({width:'100%',no_results_text: "Oops, nothing found!"});
	},
	
	addFields : function(fields, menu, addClassName, template)
	{
		for (var i = 0; i < fields.length; i++)
		{
			var html = 'data-template-id="' + template.id + '" data-title="' + fields[i].label + '" data-type="' + fields[i].type + '" data-id="' + fields[i].id + '"><i class="' + DashItems.icons[fields[i].type] + '"></i> ' + fields[i].label +'</a></li>';
			menu.append('<li><a href="javascript:void(0)" class="' + addClassName + '" ' + html);
		}
	},
	
	initFields : function(jsonFields)
	{
		$(this.fieldsContainerClass).children().slideUp('fast', function(){$(this).remove();});
		$(this.groupsContainerClass).children().slideUp('fast', function(){$(this).remove();});
		$(this.orderContainerClass).children().slideUp('fast', function(){$(this).remove();});
		
		var menu = $('#db-add-field-dropdown');
		var menuGroup = $('#db-add-group-dropdown');
		var menuOrder = $('#db-add-order-dropdown');
		
		this.fieldMap = {};
		this.numericFieldList = [];
		
		var menus = [[menu, 'action-add-field'], [menuGroup, 'action-add-group'], [menuOrder, 'action-add-order']];
			
			for (var i = 0; i < menus.length; i++)
			{
				var m = menus[i];
				m[0].children().remove();
			}
		
		for (var t = 0; t < jsonFields.length; t++) {
			var template = jsonFields[t];
			var prefix = "$" + template.id + ".";
			
			for (var i = 0; i < this.fixedFields.length; i++)
			{
				var type = this.fixedFields[i].type;
				if (type == 'range' || type == 'number' || type == 'decimal') {
					var f = $.extend( {'template': template }, this.fixedFields[i]);
					
					this.numericFieldList.push(f);
				}

				this.fieldMap[prefix+this.fixedFields[i].id] = f; 
			}
			for (var i = 0; i < template.fields.length; i++)
			{
				var type = template.fields[i].type;
				if (type == 'range' || type == 'number' || type == 'decimal')
					this.numericFieldList.push(template.fields[i]);

				this.fieldMap[prefix+template.fields[i].id] = template.fields[i];
			}
			
			var menus = [[menu, 'action-add-field'], [menuGroup, 'action-add-group'], [menuOrder, 'action-add-order']];
			
			for (var i = 0; i < menus.length; i++)
			{
				var m = menus[i];
				
				var sm = $('<li class="dropdown-submenu"><a>' + template.title + '</a><ul class="dropdown-menu"></ul></li>');
				m[0].append(sm);
				var submenu = sm.find('ul');
				
				submenu.append('<li role="presentation" class="dropdown-header">Common fields</li>');
				this.addFields(this.fixedFields, submenu, m[1], template);
				submenu.append('<li role="presentation" class="dropdown-header">Report fields</li>');
				this.addFields(template.fields, submenu, m[1], template);
			}
		}
	},
	
	validateFrom : function()
	{
		var errors = [];
		var json = this.getJson();

		// Json = query en sql: select from group_by order_by
		if ( json.data.from == '' )
		{
			errors.push('• A template is required');
		}
		
		if ( errors.length != 0 )
		{
			noty({
				layout: 'topRight', 
				type: 'error', 
				text: errors.join('<br>')
			});
		}
		return errors.length == 0;
	},

	validateTitle : function()
	{
		var errors = [];
		var json = this.getJson();

		if ( json.title == '' )
		{
			errors.push('• A title is required');
		}

		if ( errors.length != 0 )
		{
			noty({
				layout: 'topRight', 
				type: 'error', 
				text: errors.join('<br>')
			});
		}
		return errors.length == 0;
	},
	
	validate : function()
	{
		var errors = [];
		var json = this.getJson();
		
		if ( json.data.group_by.length > 0 )
		{
			for ( var i = 0; i < json.data.select.length; i++ )
			{
				var field = json.data.select[i];
				if ( field.type == 'field' || field.type == 'json' )
				{
					var found = false;
					var datetype = null;

					for ( var j = 0; j < json.data.group_by.length; j++ )
					{
						if ( field.column == json.data.group_by[j].column )
						{
							found = true;
							datetype = json.data.group_by[j].datetype;
							break;
						}
					}
					if ( ! found )
					{
						errors.push('• Field '+ field.alias +' has to appear in group by');
					}
					else if (datetype != field.datetype)
					{
						errors.push('• Field '+ field.alias +' has to have the same date type as in group by');
					}
				}
			}

			for ( var i = 0; i < json.data.order_by.length; i++ )
			{
				var field = json.data.order_by[i];
				
				var found = false;
				var datetype = null;

				for ( var j = 0; j < json.data.group_by.length; j++ )
				{
					if ( field.column == json.data.group_by[j].column )
					{
						found = true;
						datetype = json.data.group_by[j].datetype;
						break;
					}
				}
				if ( ! found )
				{
					errors.push('• Order by '+ this.fieldMap[field.column].label +' has to appear in group by');
				}		
				else if (datetype != field.datetype)
				{
					errors.push('• Field '+ field.alias +' has to have the same date type as in group by');
				}
			}
		}
		else
		{ 
			for ( var i = 0; i < json.data.select.length; i++ )
			{
				var field = json.data.select[i];
				console.log(field);
				if ( field.type == 'aggregate' && field.function != 'count' )
				{
					errors.push('• Aggregated field '+ field.alias +' cannot be used without group by');
				}
			}
		}

		if ( errors.length != 0 )
		{
			noty({
				layout: 'topRight', 
				type: 'error', 
				text: errors.join('<br>')
			});
		}
		return errors.length == 0;
	}
	
};

// Init Lib
DashItems.init();

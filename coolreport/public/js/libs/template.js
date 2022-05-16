/**
 * Templates Lib
 * 
 * This library contains methods to manage templates.
 * 
 * @type type
 */

var Templates = {

	fieldsContainerClass: '.fields-container',
	
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
		'report' : 'icon-file-text-alt'
	},

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
						
						Templates.initDropzone();
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
				.off('click', '.btn-select-report')
				.on('click', '.btn-select-report', function(e) {
					e.preventDefault();
					self.selectReport( $(this).closest('.controls') );
				});
			
			// Store template
			$('.form-template-store').on('submit', function(e) {
				e.preventDefault();
				self.store( $(this).closest('form') );
			});
			
			// Update template
			$('.form-template-update').on('submit', function(e) {
				e.preventDefault();
				self.update( $(this).closest('form') );
			});
			
			
			
			// Pickers
			self.setPickers();
			
			// Chosen
			self.setChosen();
		});
	},
	
	selectReport: function( $element )
	{
		var self = this;
		
		var input = $element.find('input');
		var id = input.attr('name');
		var html = Handlebars.compile($("#tpl-select-report-modal").html())({
			template: input.data('template'),
			field: id,
		});
		
		$('#select-report-modal').remove();
		var modal = $(html).appendTo($('#content')).modal();
		modal.on('click', '.btn-report-selected', function( ) {
				var rowIdx = $(this).data('row');
				var row = $('#selectReportTable').DataTable().row(rowIdx).data();
				
				input.val(row.DT_RowData.id);
				input.siblings('span').html(row.title);
				
				modal.modal('hide');
			});
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
				Templates.removeElement($element);
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
	
	addFieldByButton: function( $button )
	{
		var type = $button.data('type'),
			html = Handlebars.compile( $("#tpl-create-template").html() )({
			type: type,
			icon: Templates.icons[ type ],
			templates: CR_TEMPLATES,
			optionable: ( type === 'checkbox' || type === 'radio' || type === 'select' )
		});
	
		$(html)
			.hide()
			.appendTo( $(Templates.fieldsContainerClass) )
			.slideDown('fast');
	},
	
	addFieldByObject: function( field, main_date_field )
	{
		var type = field.type,
			html = Handlebars.compile( $("#tpl-create-template").html() )({
			field: field,
			type: type,
			icon: Templates.icons[ type ],
			templates: CR_TEMPLATES,
			main_date: (field.id == main_date_field),
			optionable: ( type === 'checkbox' || type === 'radio' || type === 'select' )
		});
		
		$(html)
			.hide()
			.appendTo( $(Templates.fieldsContainerClass) )
			.slideDown('fast');
	},
	
	store: function( $form )
	{
		var self = this;
		bootbox.confirm('<h2><small class="inh_bold text-info"><i class="icon-question-sign icon-2x"></i> El elemento se creará</small></h2>', function(result){
			if( result ){
				self.processForm( $form, 'store' );
			}
		});
		return false;
	},
	
	update: function( $form )
	{
		var self = this;
		bootbox.confirm('<h2><small class="inh_bold text-info"><i class="icon-question-sign icon-2x"></i> Los cambios se guardarán</small></h2>', function(result){
			if( result ){
				self.processForm( $form, 'update' );
			}
		});
		return false;
	},
	
	processForm: function( $form, action )
	{
		var fieldsContainer = $form.find( Templates.fieldsContainerClass ), // Contenedor de filas del formulario
			$button = $form.find(':submit'),
			title = $form.find('[name="title"]').val(),
			$fieldRow = null, // Fila del campo
			$fieldName = null, // Campo nombre del campo
			$fieldContainer = null, // Contenedor de required, show y multiple (parte izquierda)
			fields = new Array(), // Array de datos de los campos
			fieldId = null, // ID del field
			type = null, // Tipo de field
			name = null, // Nombre del campo (introducido por el usuario)
			required = null, // Atributo required (introducido por el usuario)
			show = null, // Atributo show (introducido por el usuario)
			multiple = null; // Atributo multiple (introducido por el usuario)

		// Recorro los campos insertados
		fieldsContainer.find('.field-row').each(function(index, obj)
		{
			$fieldRow = $(obj);
			$fieldName = $fieldRow.find('input.field-name');
			$fieldContainer = $fieldName.closest('.form-group');
			fieldId = $fieldName.attr('name');
			type = $fieldRow.data('type');
			name = $fieldName.val();
			required = $fieldContainer.find('.field-required').hasClass('active');
			show = $fieldContainer.find('.field-anchor').hasClass('active');
			main_date = $fieldContainer.find('.template-date-filter').hasClass('active');
			multiple = $fieldContainer.find('.field-multiple').hasClass('active');

			//console.log('INDEX, OBJ: ', index, obj); console.log('TYPE, NAME: ', type, name); console.log( (type+':').toUpperCase() );

			// Objeto de datos comunes para todos los campos
			var fieldData = {
				'label' : name,
				'type': type
			};

			if( fieldId != '' )
			{
				fieldData['id'] = fieldId;
			}

			// Required?
			if( required !== false ) fieldData['required'] = 'required';

			// Show as title?
			if( show !== false ) fieldData['show'] = 'show';
			
			// Is the main date?
			if( main_date !== false ) fieldData['main_date'] = true;

			// Según el tipo de campo
			switch( type )
			{
				case 'select':
				case 'radio':
				case 'checkbox':
					//console.log($fieldRow.find('input[type="'+type+'"]:checked').length);
					// Añado propiedades al objeto fieldData
					if( multiple !== false ) fieldData['multiple'] = 'multiple';
					fieldData['options'] = [];

					// Inspecciono las options
					$fieldRow.find('.field-option').each(function(ind,obj){
						var optionName = $(obj).val(); console.log('optionName: ' + optionName);

						var optionsData = {'value': optionName};

						if( $(obj).attr('name') != '' ){
							optionsData['id'] = $(obj).attr('name');
						}

						fieldData.options.push(optionsData);
					});
				break;

				case 'number':
				case 'range':
					$step = parseInt($fieldRow.find('.field-step').val()); if( ! isNaN($step) ) fieldData['step'] = $step;
				case 'decimal':
					$min = parseInt($fieldRow.find('.field-min').val()); if( ! isNaN($min) ) fieldData['min'] = $min;
					$max = parseInt($fieldRow.find('.field-max').val()); if( ! isNaN($max) ) fieldData['max'] = $max;
					if (type == 'decimal')
					{
						$sensor = $fieldRow.find('.field-sensor').val();
						if ($sensor != '')
							fieldData['sensor'] = $sensor;
					}
				break;

				case 'file':
					$max = parseInt($fieldRow.find('.field-max').val()); if( ! isNaN($max) ) fieldData['max'] = $max;
					$accept = $fieldRow.find('.field-accept').val(); if( $accept !== '' ) fieldData['accept'] = $accept;
				break;

				case 'text':
				case 'textarea':
				case 'email':
				break;
				case 'report':
					$template = $fieldRow.find('.field-template').val();
					fieldData['template'] = $template;
				break;
				default:
					// Nothing to do
			}

			// Añado datos del campo al array
			fields.push(fieldData);
			console.log('*** añadido '+type+' ***');
		});

		var settings = {};
		if ($('#template-date-filter-none').hasClass('active'))
			settings['date'] = null;
		else if ($('#template-date-filter-created-at').hasClass('active'))
			settings['date'] = '_created_at';
		
		settings['geolocation'] = $('#geolocation').prop('checked');
		settings['visible'] = $('#visible').prop('checked');
		settings['register'] = $('#register').prop('checked');
		settings['common'] = $('#common').prop('checked');

		console.log('------------------------------');
		console.log('array FIELDS:');
		console.log(fields);
		console.log('------------------------------');
		console.log( $.toJSON(fields) );

		if( action === 'store' ) // Store
		{
			Forms.post($SITE_PATH + 'template', {
				title: title,
				fields: $.toJSON(fields),
				settings: $.toJSON(settings)
			}, $button);
		}
		else // Update
		{
			Forms.post($SITE_PATH + 'template/' + $form.data('id'), {
				title: title,
				fields: $.toJSON(fields),
				settings: $.toJSON(settings),   
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
				id = $this.attr('name'),
				color = "hsl("+ value/max * 120 +", 60%, 60%)";

			$this
				.before('<div id="slider-' + id + '" class="progress progressGreen margin-top-5 margin-bottom-5"></div>'/*+
						'<div class="row">' +
							'<div class="col-xs-4 text-left">Min: '+min+'</div>' +
							'<div class="col-xs-4 text-center">Selected: '+value+'</div>' +
							'<div class="col-xs-4 text-right">Max: '+max+'</div>' +
						'</div>'*/)
				.remove();

			$('#slider-'+id).progressbar({ value: value, min: min, max: max });
			$('#slider-'+id).find('.ui-progressbar-value').css("background",  color);
			$('#slider-'+id).next().find('.range-value').css("background", color);
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
	
	initDropzone: function( )
	{
		var fileinputs = $('input.form-file[type="file"]');
	
		fileinputs.hide().prop('disabled', true).each(function(k,v){
			
			var $this = $(this),
				field_id = $this.attr('name'),
				max = $this.attr('max'),
				accept = $this.attr('accept'),
				files_uploaded = $this.siblings('.dropzone-previews').find('input.form-file[type="hidden"]').length,
				maxFiles = ( typeof max === "undefined" || parseInt(max) === 0 ) ? null : (max - files_uploaded);

			var url = (typeof $this.attr('url') === "undefined") ? "report/file" : $this.attr('url');

			if( maxFiles === null || maxFiles > 0 )
			{
				$('#dropzone-' + field_id).remove();
				$this.before('<div class="dropzone" id="dropzone-' + field_id + '"></div>');


				/*------ Dropzone Init ------*/
				$('#dropzone-' + field_id).dropzone({
					url: $SITE_PATH + url,
					maxFiles: maxFiles,
					acceptedFiles: ( typeof accept === "undefined" ) ? null : accept,
					maxFilesize: $UPLD_MAX_SIZE / 1024,
					addRemoveLinks: true,

					removedfile: function(file) {
    					var filename = $(file.previewElement).find('.dz-filename').find('span').text();
    					var _ref;
    					$("input[name='"+ field_id +"[]'][value*='"+ filename +"']").remove()
    					return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
  					},

					success: function(file, response){
						var json_file = {
							hash: response.data,
							filename: file.name
						};
						if (url == "report/file")
						{
							$this.after('<input type="hidden" name="'+field_id+'[]" value=\''+$.toJSON(json_file)+'\' />');
						}
						else
						{
							$this.after('<input type="hidden" name="'+field_id+'[]" value=\''+$.toJSON(json_file)+'\' />');

							$.get("/map/geojson/" + response.data).success(function(geojson){
								// Relleno mapa del report
								var canvas = $('#map'), dimensions = { width: canvas.width(), height: canvas.height() };
							
								var map = new google.maps.Map(document.getElementById('map'), {
									center: new google.maps.LatLng(0,0),
									zoom: 1});

								if (typeof geojson.type === 'undefined' )
								{
									for (var i=0; i<geojson.length; i++)
									{
										// Compruebo que tenga geometry para mostrar el mapa
										if ( geojson[i].geometry !== null )
										{
											CoolReport.drawFromGeoJSON(map, geojson[i].geometry);	
										}					
									}
									//CoolReport.zoomToGeoJSON(map, {type: 'MultiPolygon'});
								}
								else
								{
									CoolReport.drawFromGeoJSON(map, geojson);
									CoolReport.zoomToGeoJSON(map, geojson);
								}

								$('#map').height(450).width('auto'); 
    						});													
						}
						

						file.previewTemplate.classList.add("dz-success");
					},

					error: function(file, response){
						var messages = [];

						if( typeof response === 'object' ){
							if( response.status === 'ERROR' ){
								$.each(response.messages, function(field, message){
									messages.push( message );
								});
							}else{ // status === FATAL
								messages.push( $_LANG.GENERIC.error_msg );
							}
						}else{
							messages.push( response );
						}

						file.previewTemplate.classList.add("dz-error");

						var _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]"),
							_results = [],
							_i, _len, node;

						for (_i = 0, _len = _ref.length; _i < _len; _i++) {
							node = _ref[_i];
							$.each(messages, function(id, message){
								_results.push(node.textContent = message);
							});
						}

						return _results;
					}
				});
			}
		}); // each input[file]
	},
	
	drawAttachments: function( showRemove, report_id ){
		$('input.form-file[type="hidden"]').each(function(k,v){
			var $this = $(this),
				data = $.parseJSON( $this.val() ),
				button_html = '';
				
			var att_url = $SITE_PATH + 'report/' + report_id + '/attachment/' + data.hash;
			
			var fileicon;
			if( data.mimetype[0] === 'image' ){
				fileicon = 'icon-picture';
			}else if( data.mimetype[0] === 'video' ){
				fileicon = 'icon-facetime-video';
			}else if( data.mimetype[0] === 'audio' ){
				fileicon = 'icon-volume-down';
			}else{
				fileicon = 'icon-paper-clip';
			}

			button_html = 
			'<a class="btn btn-xs dropdown-toggle pull-right" style="background:#FFF;margin-top:3px;" data-toggle="dropdown"><b class="caret"></b></a>' +
			'<ul class="dropdown-menu">'+
				'<li><a href="' + att_url + '/display" target="_blank" title=""><i class="icon-external-link blue"></i> View</a></li>' +
				'<li><a href="' + att_url + '/download" title="Download"><i class="icon-cloud-download darkGreen"></i> Download</a></li>' +
				( showRemove ? '<li><a href="javascript:void(0)" class="action-remove-file" data-hash="'+data.hash+'"><i class="icon-remove red" style="font-size:120%"></i> Remove</a></li>' : '' ) +
			'</ul>';

			var html = '<div class="dz-preview dz-file-preview dz-error file-uploaded">'+
						'<div class="dz-details">'+
							'<div class="dz-filename text-center"><i class="' + fileicon +'" style="color:#C9C9C9;font-size:440%;line-height:1.5"></i><span style="top:-97px;position:relative;color#333">' + data.filename + '</span></div>' +
							'<div class="dz-size" style="width:100%">' + data.size + button_html + '</div>' +
							'<img data-dz-thumbnail="" alt="" src="' + ( data.mimetype[0] == 'image' ? (att_url + '/display') : '') + '" style="display:block">' +
						'</div>'+
					'</div>';
	
			$this.after(html);
		});
	}
	
};

// Init Lib
Templates.init();

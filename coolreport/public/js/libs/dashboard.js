/**
 * Dashboards Lib
 * 
 * This library contains methods to manage dashboards.
 * 
 * @type type
 */

var Dashboards = {

	dashboardContainerClass: '.dashboard-container',
	
	init: function()
	{
		var self = this;
		
		$(document).ready(function(){
			$(document)
				// Add field
				.on('click', '.action-add-row', function(){
					self.addRowByButton( $(this) );
				})
				// Remove field
				.on('click', '.action-remove-row', function(e){
					e.preventDefault();
					self.confirmRemoveElement( $(this).closest('.field-row') );
				})
				// Move up field
				.on('click', '.action-up-row', function(e){
					e.preventDefault();
					self.moveRow( $(this).closest('.field-row'), 0 );
				})
				// Move down field
				.on('click', '.action-down-row', function(e){
					e.preventDefault();
					self.moveRow( $(this).closest('.field-row'), 1 );
				});
				
			// Store dashboard
			$('.form-dashboard-store').on('submit', function(e) {
				e.preventDefault();
				self.store( $(this).closest('form') );
			});
			
			// Update dashboard
			$('.form-dashboard-update').on('submit', function(e) {
				e.preventDefault();
				self.update( $(this).closest('form') );
			});
		});
	},
	
	confirmRemoveElement: function( $element )
	{
		bootbox.confirm('<h2><small class="inh_bold text-danger"><i class="icon-remove icon-2x"></i> La fila se eliminará</small></h2>', function(result){
			if( result ){
				// Elimino fila
				Dashboards.removeElement($element);
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
	
	moveRow: function( $element, mode )
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
	
	addRow: function( numCols )
	{
		var cols = [];
		var colWidth = 12/numCols;
		for (var i = 0; i < numCols; i++)
		{
			cols.push(colWidth);
		}
		
		
		var html = Handlebars.compile( $("#tpl-create-template").html() )({
			'columns' : cols,
		});
		
		var row = $(html);
		
		var options = '';
		for (var i = 0; i < DASH_ITEMS.length; i++)
		{
			options += '<option value="' + DASH_ITEMS[i].id + '">' + DASH_ITEMS[i].title + '</option>';
		}
		row.find('select').append(options);
		
		row
			.hide()
			.appendTo( $(Dashboards.dashboardContainerClass) )
			.slideDown('fast')
			.find('select').chosen();
		
		return row;
	},
	
	addRowByButton: function( $button )
	{
		var numCols = $button.data('cols');
		Dashboards.addRow(numCols);
	},
	
	fromJson: function( json )
	{
		for (var i = 0; i < json.length; i++)
		{
			var row = json[i];
			var rowHtml = Dashboards.addRow(row.length);
			var selects = rowHtml.find('select');
			
			for (var j = 0; j < row.length; j++)
			{
				$(selects[j]).val(row[j].id).trigger('chosen:updated');
			}
		}
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
	
	processForm: function( $form, action ){

		var dashboardContainer = $form.find( Dashboards.dashboardContainerClass ), // Contenedor de filas del formulario
			$button = $form.find(':submit'),
			title = $form.find('[name="title"]').val(),
			$fieldRow = null, // Fila del campo
			$fieldName = null, // Campo nombre del campo
			$fieldContainer = null, // Contenedor de required, show y multiple (parte izquierda)
			fields = new Array(), // Array de datos de los campos
			fieldId = null, // ID del field
			type = null; // Tipo de field
			
		// Recorro las filas
		var rows = [];
		dashboardContainer.find('.field-row').each(function(index, row)
		{
			var cols = [];
			$(row).find('select').each(function(i, col)
			{
				var col = $(col);
				cols.push(
					{
						'id' : col.val(),
						'title' : col.find('option[value="' + col.val() + '"]').text(),
					});
			});
			rows.push(cols);
		});

		var dashboard = {
			'title': title,
			'items': rows,
		};
		
		console.log('------------------------------');
		console.log('JSON dashboard:');
		console.log(dashboard);
		console.log('------------------------------');
		console.log( $.toJSON(dashboard) );

		if( action === 'store' ) // Store
		{
			Forms.post($SITE_PATH + 'dashboard', {
				title: title,
				dashboard: $.toJSON(dashboard)
			}, $button);
		}
		else // Update
		{
			Forms.post($SITE_PATH + 'dashboard/' + $form.data('id'), {
				title: title,
				dashboard: $.toJSON(dashboard),
				_method: 'PUT'
			}, $button);
		}
	},
};

// Init Lib
Dashboards.init();
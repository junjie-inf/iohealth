/**
 * Forms Lib
 * 
 * This library contains methods to manage forms.
 * 
 * @type type
 */

var Forms = {
	
	init: function()
	{
		$(document).on('focus', 'div.form-group.has-error input', function(){
			$(this).closest('.form-group.has-error').removeClass('has-error');
		});
	},
	
	addAlert: function( name, msg, type )
	{
		type = type || 'danger';

		$('input[name="' + name + '"]').closest('.form-group').addClass('has-error');

		var $warning = '<div class="alert alert-' + type + '" style="display:none">\
							<button type="button" class="close" data-dismiss="alert">×</button><strong style="text-transform:capitalize">' + name + '</strong>: ' + msg +
						'</div>';

		$( $warning ).prependTo( $('#content') ).slideDown('slow', function(){
			$(this).fadeTo('slow', 1.00);
		});
	},
	
	cleanAlerts: function()
	{
		// Limpio inputs
		$('.form-group.has-error').removeClass('has-error');

		// Elimino alerts
		$('div.alert').fadeTo('slow', 0.00, function() { //fade
			$(this).slideUp("slow", function() { //slide up
				$(this).remove(); //then remove from the DOM
			});
		});
	},
	
	// Not used
	addErrorTooltip: function( selector, message, placement ){
		placement = (typeof placement === "undefined") ? 'right' : placement;
		$(selector).closest('.control-group').addClass('error');
		$(selector).tooltip({
			html		: true,
			title		: message,
			trigger		: 'manual',
			placement	: placement
		});
		$(selector).tooltip('show');
		$(selector).on('focus', function(){
			$(this).tooltip('destroy');
			$(this).closest('.control-group').removeClass('error');
		});
	},
	
	/**
	 * Send a POST request.
	 * 
	 * @param {string} url
	 * @param {array} data
	 * @param {jQuery} $button
	 * @param {function} callbackOk
	 */
	post: function( url, data, $button, callbackOk, disabled = false ){
		this.sendRequest( 'post', url, data, $button, callbackOk, disabled );
	},
	
	/**
	 * Send a GET request.
	 * 
	 * @param {string} url
	 * @param {array} data
	 * @param {jQuery} $button
	 * @param {function} callbackOk
	 */		
	get: function( url, data, $button, callbackOk ){
		this.sendRequest( 'get', url, data, $button, callbackOk );
	},
	
	validateRequired: function( $form ){

		if (typeof $form !== 'undefined' && $form !== false)
		{
			var $form = $(this),
				empty = 0;
			console.log('enviando form');
			$form.find('input').each(function(k,v){
				var $input = $(this),
					value = $.trim($input.val()),
					req = $input.attr('required');
				if( typeof req !== 'undefined' && req !== false && value == '' ){
					console.log($input, 'tiene required y está vacío');
					empty++;
				}
			});

			if( empty > 0 ){
				Forms.addAlert( 'Fill all required fields', 'The fields with <i class="icon-asterisk red"></i> are required!' );
				return false;
			}
			
			return true;
		}
	},
	
	/**
	 * Send a request.
	 * 
	 * @param {string} url
	 * @param {array} data
	 * @param {jQuery} $button
	 * @param {function} callbackOk
	 */
	sendRequest: function( method, url, data, $button, callbackOk, disabled = false ){
		// Selecciono la función jQuery según el method
		var $requestFunction = ( method === "post" ? jQuery.post : jQuery.get ),
			$form = $button.closest('form');
		
		Forms.cleanAlerts(); // Elimino todos los alert
		
		if( Forms.validateRequired( $form ) === true )
		{
			console.log(disabled);
			// TODO: SPINNER on
			// Añado opcion para el caso de que se quiera bloquear el boton despues de pulsar (votos)
			// ya que el button loading y reset son asincronos y dan problemas para modificar el estado del boton
			if (disabled === false)
			{
				$button.button('loading'); // Loading state on
			}

			$requestFunction( url, data, function(d){
				if( (d !== null) && (typeof d.status !== "undefined") && (d.status !== null) && (d.status === "OK") ){
					// Ejecuto el callback con los argumentos del $.post/$.get
					if( typeof callbackOk !== "undefined" && callbackOk !== null ){
						callbackOk(d);
					}
					// Redirección
					if( d.redirect ){
						window.location.href = d.redirect;
					}else{
						// TODO: SPINNER off
						if (disabled === false)
						{
							$button.button('reset'); // Loading state off
						}

						// Si no hay formulario, se trata de un botón de delete => no muestro alert
						if( $form.length !== 0 ){
							Forms.addAlert( 'Éxito', 'Datos guardados', 'success' );
						}
					}
				}
				return false;
			}, 'json')
			.fail(function(d){
				// TODO: SPINNER off
				$button.button('reset'); // Loading state off

				d = d.responseJSON;

				if( d.status === 'ERROR' ){
					$.each(d.messages, function(field, message){
						Forms.addAlert( field, message );
					});
				}else{ // status === FATAL
					Forms.addAlert( 'error', $_LANG.GENERIC.error_msg );
				}
			})
			.always(function(){
				//
			});
		}
	}
	
};

// Init Lib
Forms.init();
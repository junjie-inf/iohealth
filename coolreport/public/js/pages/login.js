/* ===================================================
 * JS login
 * =================================================== */

$(document).ready( function(){

	/* Ajax - Registro
	================================================== */
	$('.form-login').submit(function(e){
		e.preventDefault();
		var $button = $(this).find(':submit');
		
		Forms.post( $SITE_PATH + 'auth/login', $(this).serialize(), $button );
	});
	
});
/* ===================================================
 * JS signup
 * =================================================== */

$(document).ready( function(){

	/* Ajax - Registro
	================================================== */
	$('.form-signup').submit(function(e){
		e.preventDefault();
		var $button = $(this).find(':submit');
		
		Forms.post( $SITE_PATH + 'auth', $(this).serialize(), $button );
	});
	
});
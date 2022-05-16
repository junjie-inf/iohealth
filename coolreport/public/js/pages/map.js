/**
 * Carga y abre un reporte
 * 
 * @param {type} id
 * @returns {Boolean}
 */
function openReportDetails( id ){
	id = id || null;
	
	if( id === null ) return false;
	
	$.getJSON($SITE_PATH + "report/" + id, function(r){
		if( r.status === "OK" ){

			var data = r.data;
			
			// Centramos el background
			CoolReport.centerTo(data.latitude, data.longitude);
			
			// Preparo datos
			var dataToTpl = {
				report_id:		id,
				editable:		( data.editable == true ? $REPORT_URL + id + '/edit' : false ),
				removable: 		( data.removable == true ? true : false ),
				n_comments:		data.comments.length,
				user_fullname:	$.trim(data.user.firstname + ' ' + data.user.surname),
				
				data:			data,
				content:		data.content,
				AUTH:			$AUTH
			};
			
			// Compilo plantilla
			var html = Handlebars.compile( $("#tpl-viewReport").html() )( dataToTpl );

			// Inserto plantilla (la modal) en el document
			$('#modal_container').remove();
			var $modal_container = $('<div id="modal_container"></div>');
			$('body').append($modal_container);
			$modal_container.html(html);
			
			Templates.adaptRanges();
			Templates.adaptReadOnly();
			Templates.drawAttachments( false, id );

			// Relleno mapa del report
			var url = GMaps.staticMapURL({
				size: [1200, 150],
				lat: data.latitude,
				lng: data.longitude,
				zoom: 15,
				scale: 2,
				markers: [
					{lat: data.latitude, lng: data.longitude, color: 'blue'}
				]
			});
			$('#report-map-'+id).attr('src', url);
			
			/* // Relleno mapas de los adjuntos
			if( data.attachments.length !== 0 ){
				$.each(data.attachments, function(k,v){

					url = GMaps.staticMapURL({
						size: [736, 150],
						lat: v.latitude,
						lng: v.longitude,
						zoom: 16,
						markers: [
							{lat: v.latitude, lng: v.longitude, color: 'blue'}
						]
					});
					$('#attachment-map-'+v.id).attr('src', url);

				});
			} */
			
			// Plugins para modal
			$('input, textarea').placeholder();
			$('textarea').autosize();
			setInputLimiter( $('textarea') );

			// Muestro modal
			$('#modal-report-'+id).modal('show');

			// Actualizo hash de la URL
			window.location.hash = '#'+id;
		}
	});
	
}


$(document).ready(function(e) {

	/* -------- Resize map to fit window ------ */
	WindowResizer.init( $('#map_canvas'), $('#cr-navbar') );


	/* -------- Bootstrap Modal ------ */
	// Si hay un hash (#...) en la URL, cargo la sección
	openReportDetails( document.URL.split('#')[1] );
		
	// [Modal] View Report
	$(document).on('click', '.btn-more', function(){		
		openReportDetails( $(this).data('id') );
	});
	
	// Constrain Modal to Window Size
	$.fn.modal.defaults.height = function(){
		return $(window).height() - 265;
	}


	/* ----- Init CoolReport engine ----- */
	CoolReport.init( $MAP.latitudes, $MAP.longitudes );
	CoolReport.addLegend(CoolReport.gmap.map, templates_data);
});
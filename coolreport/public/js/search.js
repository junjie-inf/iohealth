/* ------------------
 * POPUP QUERY FUNCTION
 * ------------------ */
function queryPopup( input ) {

	// Empty? exit man
	if( input.val() === '' ) {
		$('ul.searchlist').dropdown('toggle');
		return false;
	}
	
	// Ajax search
	$.getJSON( $SITE_PATH + 'report/search', 'mindate=' + CoolReport.start.format('X') + '&' + 'maxdate=' + CoolReport.end.format('X') + '&title=' + input.val(), function(d) {
		if( input.val() !== '' ) { // prevent delayed query
			// Build response popup
			buildItemPopup( d.data );
		}
	});
	
	/*if( typeof CoolReport.gmap.markers !== 'undefined' ) {
		
		var items = new Array();
		
		$.each( CoolReport.gmap.markers, function(i, marker ) {
			var pattern = new RegExp('.*' + input.val() + '.*', 'i');
			if( pattern.test(marker.title) ) {
				items.push(marker);
			}
		});
		
		buildItemPopup( items );
	}*/
	
	return true;
}

/* ------------------
 * BUILD ITEM POPUP
 * ------------------ */

function buildItemPopup( items ) {
	// There is any item?
	if( items !== null ) {
		// Save search results
		CoolReport.search = items;
		
		// Default value
		var dft = 'Empty';
		
		// Show popup if not shown
		if( $('ul.searchlist').is(':visible') === false ) $('ul.searchlist').dropdown('toggle');
		
		// If not items to show
		if( items.length !== 0 ) {
			
			if( $('a.searchitem').eq(0).text() === dft ) {
				$('ul.searchlist li').remove();
			}
			
			// Build item popup
			$.each(items, function(i, item){
				updateItemPopup( i, item );
			});
			
			// Remove unused lists
			$.each($('a.searchitem'), function(i, item) {
				if( i >= items.length ) {
					item.remove();
				}
			});
			
			/* SEARCH ITEM CLICK FIX
			 * ------------------ */
			$('a.searchitem').click(function(e) {
				
				// Añadimos el marker
				var id = parseInt($(this).attr('data-target'));
				
				if( CoolReport.gmap !== null ) {
					$.each(CoolReport.search, function(i, item){
						if( item.id === id ) {
							CoolReport.setMarker(item);
							CoolReport.focusMarker(item.id);
						}
					});
				} else {
					window.location.href = $SITE_PATH + '#' + id;
				}
				
				// Clean input and close dropdown
				$('input.search').val('');
			});
			
		} else {
			$('ul.searchlist li').remove();
			$('ul.searchlist').append('<li class="disabled"><a class="searchitem" tabindex="-1" href="javascript:void(0)">' + dft + '</a></li>');
		}
	}
}

function updateItemPopup( i, item ) {
	
	if( $('a.searchitem').eq(i).length !== 0 )
	{
		$('a.searchitem').eq(i).text(item.title);
		$('a.searchitem').eq(i).attr('data-target', item.id);
	}else{
		$('ul.searchlist').append('<li><a class="searchitem inh_bold" tabindex="-1" data-target="' + item.id + '" href="">' + item.title + '</a></li>');
	}
}

/* DOCUMENT READY
 * ------------------ */
$(document).ready( function() {	
	/* SEARCH INPUT KEYPRESS
	 * ------------------ */
	$('input.search').keyup(function(e) {
		// Hide popup when writing
		if( $('ul.searchlist').is(':visible')) {
			if( e.which === 38 || e.which === 40 ) {
				e.preventDefault();
				e.stopPropagation();
				$('a.searchitem').eq(0).focus();
			} else {
				queryPopup( $(this) );
			}
		} else {
			queryPopup( $(this) );
		}
	});
	
	$('input.search').click(function(e) {
		queryPopup( $(this) );
	});
});
/* ---------- Textarea with limits ---------- */
function setInputLimiter( $obj ){
	if( typeof $obj.data('limit') === "undefined" ) return false;
	
	$obj.inputlimiter({
		limit: $obj.data('limit'),
		limitBy: 'characters',
		remText: 'Te quedan %n caracteres ',
		limitText: 'de %n.'
	});
}


$(document).ready(function($) {

	/* Bootbox - Deactivate animation
	================================================== */
	bootbox.setDefaults({animate: false});

	/* $.browser esta deprecated a partir de jQuery 1.9. Se repara con esta funcion
	================================================== */
	add_browser_detection(jQuery);
	function add_browser_detection($) {
		//code taken from http://code.jquery.com/jquery-1.8.3.js to provide simple browser detection for 1.9+ versions
		if(!$.browser) {
			var matched, browser;

			// Use of jQuery.browser is frowned upon.
			// More details: http://api.jquery.com/jQuery.browser
			// jQuery.uaMatch maintained for back-compat
			$.uaMatch = function( ua ) {
				ua = ua.toLowerCase();

				var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
					/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
					/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
					/(msie) ([\w.]+)/.exec( ua ) ||
					ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
					[];

				return {
					browser: match[ 1 ] || "",
					version: match[ 2 ] || "0"
				};
			};

			matched = $.uaMatch( navigator.userAgent );
			browser = {};

			if ( matched.browser ) {
				browser[ matched.browser ] = true;
				browser.version = matched.version;
			}

			// Chrome is Webkit, but Webkit is also Safari.
			if ( browser.chrome ) {
				browser.webkit = true;
			} else if ( browser.webkit ) {
				browser.safari = true;
			}

			$.browser = browser;

		}
	}
	
	/* ---------- Placeholder Fix for IE ---------- */
	$('input, textarea').placeholder();

	/* ---------- Auto Height texarea ---------- */
	$('textarea').autosize();
	
	/* ---------- Textarea with limits ---------- */
	setInputLimiter( $('textarea') );
	
	// Tabs (fix for SimpliQ Template)
	$('.nav-tabs li a[data-toggle="tab"], .nav-pills li a[data-toggle="tab"]').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
	
	/* ---------- Tabs with hash in URL --------- */
	// Open tab
	if (location.hash !== '' && location.hash.indexOf("tab_") === 1 ){
		var hash = location.hash;
		var tab = hash.replace("tab_", '');
		$('a[href="' + tab + '"]').tab('show');
	}
	// Change URL when click on a tab
	$('a[data-toggle="tab"]').on('click', function(e) {
		location.hash = 'tab_'+ e.target.hash.substr(1);
		return false;
	});
	
	/* Search box in navbar */
	$('.search-box-opener').click(function () {
		var $search_btn = $(this).find('.search-btn'),
			$search_open = $(this).siblings('.search-open');
		
		if( $search_btn.hasClass('icon-search')){
			$search_open.fadeIn(500);
			$search_btn.removeClass('icon-search').addClass('icon-remove');
		} else {
			$search_open.fadeOut(500);
			$search_btn.addClass('icon-search').removeClass('icon-remove');
		}   
	}); 
	
	/* -------- Bootstrap Date Range Picker ----- */
	$('#reportrange').daterangepicker({
		ranges: {
		   'Hoy': [moment(), moment()],
		   'Ayer': [moment().subtract('days', 1), moment().subtract('days', 1)],
		   'Últimos 7 días': [moment().subtract('days', 6), moment()],
		   'Últimos 30 días': [moment().subtract('days', 29), moment()],
		   'Este mes': [moment().startOf('month'), moment().endOf('month')],
		   'Último mes': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
		   'Sin límite': [moment().subtract('year',100), moment()]
		},
		startDate: ( typeof $.cookie("from") === "undefined" ? moment().subtract('days', 29) : moment(parseInt($.cookie("from"))) ),
		endDate: ( typeof $.cookie("to") === "undefined" ? moment() : moment(parseInt($.cookie("to"))) )
	}, function(from, to) {
		//console.log(' A date range was chosen: ' + from.format('YYYY-MM-DD') + ' to ' + to.format('YYYY-MM-DD'));	
		if ($('.ranges ul li.active').text() == "No Limit") 
		{
			$('#reportrange span').html("No Limit");	
		}	
		else 
		{
			$('#reportrange span').html(from.format('MMM D, YYYY') + ' - ' + to.format('MMM D, YYYY'));
		}	

		if( typeof datecallback === "undefined" || datecallback === null )
		{
			datecallback = function(){};
		}

		$.cookie("from", from.format('X')*1000, { path: $SITE_PATH });
		$.cookie("to", to.format('X')*1000, { path: $SITE_PATH });	

		CoolReport.setDate(from, to, datecallback);
	});

	if ($('.ranges ul li.active').text() == "No Limit") 
	{
		$('#reportrange span').html("No Limit");	
	}	

});

	
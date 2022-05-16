<?php

Basset::collection('default_start', function($collection)
{
	$collection->stylesheet('plugins/bootstrap/css/bootstrap.css'); // Bootstrap core CSS
	$collection->stylesheet('https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css'); // Font Awesome
	$collection->stylesheet('plugins/bootstrap-daterangepicker/daterangepicker-bs3.css'); // Bootstrap Date Range Picker
});


Basset::collection('default_coolreport', function($collection)
{
	$collection->stylesheet('css/coolreport_default.css')->apply('CssMin');
	$collection->stylesheet('css/coolreport_common.css')->apply('CssMin');
	$collection->stylesheet('css/coolreport_manager.css')->apply('CssMin');
	
	$collection->javascript('js/location-picker.js')->apply('JsMin');
	
	// Bootstrap Date Range Picker
	$collection->javascript('manager_tpl/js/bootstrap-datepicker.min.js');
	$collection->javascript('manager_tpl/js/bootstrap-timepicker.min.js');
	$collection->javascript('manager_tpl/js/bootstrap-colorpicker.min.js');
	$collection->javascript('plugins/bootstrap-daterangepicker/moment.min.js');
	$collection->javascript('plugins/bootstrap-daterangepicker/daterangepicker.js')->apply('JsMin');
	$collection->javascript('plugins/jquery-json/jquery.json-2.4.min.js');
	
	$collection->javascript('manager_tpl/js/jquery.autosize.min.js');
	$collection->javascript('manager_tpl/js/jquery.placeholder.min.js');
	
	// CoolReport Libs
	$collection->javascript('js/libs/window-resizer.js')->apply('JsMin');
	$collection->javascript('js/libs/coolreport.js')->apply('JsMin');
	$collection->javascript('js/libs/forms.js')->apply('JsMin');
	$collection->javascript('js/libs/statistic.js')->apply('JsMin');
	$collection->javascript('js/libs/location-picker.js')->apply('JsMin');
	$collection->javascript('js/libs/manager.js')->apply('JsMin');
	$collection->javascript('js/search.js')->apply('JsMin');
	
	$collection->javascript('js/langs/en.js');
	$collection->javascript('js/generic-table.js');
	$collection->javascript('js/phpjs.js');
	$collection->javascript('js/script.js');
	
	$collection->javascript('plugins/bootbox/js/bootbox.min.js');
	
	$collection->javascript('https://maps.google.com/maps/api/js?key=AIzaSyDiKcVl7bPPwq5nUTL0X_GJoPrlltb3_7M&libraries=visualization&language=en');
	$collection->javascript('plugins/gmap/gmap.js')->apply('JsMin');
	$collection->javascript('plugins/gmap/markerclusterer.js')->apply('JsMin');
})->apply('CssMin');

Basset::collection('cr_template', function($collection)
{
	$collection->javascript('js/libs/template.js')->apply('JsMin');
});

Basset::collection('cr_dashboard', function($collection)
{
	$collection->javascript('js/libs/dashboard.js')->apply('JsMin');
});

Basset::collection('cr_dashitem', function($collection)
{
	$collection->javascript('js/libs/dashitem.js')->apply('JsMin');
	$collection->javascript('js/libs/dashdisplay.js')->apply('JsMin');
	$collection->javascript('js/libs/expreditor.js')->apply('JsMin');
	$collection->javascript('manager_tpl/js/wizard.min.js')->apply('JsMin');
});

Basset::collection('cr_expreditor', function($collection)
{
	$collection->javascript('js/libs/expreditor.js')->apply('JsMin');
	$collection->javascript('js/jstree.min.js');
	$collection->stylesheet('css/jstree/style.min.css')->apply('UriRewriteFilter');
});

Basset::collection('cr_import', function($collection)
{
	$collection->javascript('plugins/papaparse.min.js');
	$collection->javascript('js/libs/import.js')->apply('JsMin');
});

Basset::collection('cr_maps', function($collection)
{
	$collection->javascript('plugins/cr_maps/topojson.js');
	$collection->javascript('plugins/cr_maps/three.min.js');
	// $collection->javascript('plugins/cr_maps/d3.min.js');
	$collection->javascript('plugins/cr_maps/d3-threeD2.js');
	$collection->javascript('plugins/cr_maps/OrbitControls.js');
	$collection->javascript('plugins/cr_maps/dat.gui.min.js');
	$collection->javascript('plugins/cr_maps/gis.js');
	$collection->javascript('plugins/cr_maps/gis3d.js');
});

Basset::collection('manager_template', function($collection)
{
	// CSS
	$collection->stylesheet('manager_tpl/css/jquery-ui-1.10.3.custom.css');
	$collection->stylesheet('manager_tpl/css/dropzone.css')->apply('CssMin');
	$collection->stylesheet('manager_tpl/css/fullcalendar.css');
	$collection->stylesheet('manager_tpl/css/chosen.css')->apply('UriRewriteFilter');
	$collection->stylesheet('manager_tpl/css/jquery.cleditor.css');
	$collection->stylesheet('manager_tpl/css/jquery.noty.css');
	$collection->stylesheet('manager_tpl/css/noty_theme_default.css');
	$collection->stylesheet('manager_tpl/css/elfinder.min.css');
	$collection->stylesheet('manager_tpl/css/elfinder.theme.css');
	$collection->stylesheet('manager_tpl/css/uploadify.css');
	$collection->stylesheet('manager_tpl/css/jquery.gritter.css');
	$collection->stylesheet('manager_tpl/css/glyphicons.css');
	$collection->stylesheet('manager_tpl/css/halflings.css');
	$collection->stylesheet('manager_tpl/css/dropzone.css');
	$collection->stylesheet('manager_tpl/css/xcharts.min.css');
	$collection->stylesheet('manager_tpl/css/jquery.easy-pie-chart.css');
	$collection->stylesheet('manager_tpl/css/jquery-jvectormap-1.2.2.css');
	
	// Less
	$collection->stylesheet('manager_tpl/css/style.less')->apply('Less');
	
	// JavaScript
	$collection->javascript('manager_tpl/js/jquery-ui-1.10.3.custom.min.js');
	$collection->javascript('manager_tpl/js/jquery-migrate-1.2.1.min.js');
	$collection->javascript('manager_tpl/js/bootstrap.min.js');
	$collection->javascript('manager_tpl/js/jquery.ui.touch-punch.min.js');
	$collection->javascript('manager_tpl/js/jquery.knob.modified.min.js');
	$collection->javascript('manager_tpl/js/jquery.sparkline.min.js');
	$collection->javascript('manager_tpl/js/d3.min.js');
	$collection->javascript('manager_tpl/js/xcharts.min.js');
	$collection->javascript('manager_tpl/js/jquery.inputlimiter.1.3.1.min.js');
	$collection->javascript('manager_tpl/js/jquery.dataTables.min.js');
	$collection->javascript('manager_tpl/js/dataTables.bootstrap.min.js');
	$collection->javascript('manager_tpl/js/jquery.cookie.min.js');
	$collection->javascript('manager_tpl/js/jquery.noty.min.js');
	$collection->javascript('manager_tpl/js/dropzone.min.js');
	$collection->javascript('manager_tpl/js/fullcalendar.min.js');
	$collection->javascript('manager_tpl/js/jquery.chosen.min.js');
	$collection->javascript('manager_tpl/js/jquery.flot.min.js');
	$collection->javascript('manager_tpl/js/jquery.flot.pie.min.js');
	$collection->javascript('manager_tpl/js/jquery.flot.stack.min.js');
	$collection->javascript('manager_tpl/js/jquery.flot.resize.min.js');
	$collection->javascript('manager_tpl/js/jquery.flot.time.min.js');
	$collection->javascript('manager_tpl/js/jquery.autosize.min.js');
	$collection->javascript('manager_tpl/js/jquery.placeholder.min.js');
	$collection->javascript('manager_tpl/js/jquery-jvectormap-1.2.2.min.js');
	$collection->javascript('manager_tpl/js/jquery-jvectormap-es-mill-en.js');
	//$collection->javascript('manager_tpl/js/jquery-jvectormap-europe-mill-en.js');
	//$collection->javascript('manager_tpl/js/jquery-jvectormap-world-mill-en.js');
	$collection->javascript('manager_tpl/js/jquery.gritter.min.js');
	
	// Stats
	$collection->stylesheet('manager_tpl/css/nv.d3.min.css');
	// $collection->javascript('manager_tpl/js/d3.v3.js');
	$collection->javascript('manager_tpl/js/nv.d3.min.js');
	
	// theme scripts
	$collection->javascript('manager_tpl/js/custom.js');
	$collection->javascript('manager_tpl/js/core.js');
	
	// Safari compatibility
    $collection->javascript('plugins/jquery.h5validate.js')->apply('JsMin');

    // Just gage chart
    // $collection->javascript('manager_tpl/js/justgage.1.0.1.min.js');
    // $collection->javascript('manager_tpl/js/raphael.min.js');
    $collection->javascript('manager_tpl/js/uncompressed/justgage.js')->apply('JsMin');
    $collection->javascript('manager_tpl/js/raphael-2.1.4.min.js');
    
})->apply('CssMin')->apply('UriRewriteFilter');


// HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries
Basset::collection('IE8_HTML5', function($collection)
{
    $collection->javascript('https://html5shiv.googlecode.com/svn/trunk/html5.js');
    $collection->javascript('plugins/html5shiv.js');
    $collection->javascript('plugins/respond.min.js');
});



// Mustache and Handlebars
Basset::collection('mustache', function($collection)
{
	$collection->javascript('plugins/mustache.js');
	$collection->javascript('plugins/handlebars.js');
	$collection->javascript('plugins/hb_helpers.js');
})->apply('JsMin');



/**
 * Collections for pages
 */


//
// Page - Login
//
Basset::collection('page-login', function($collection)
{
	$collection->javascript('js/pages/login.js');
})
->apply('JsMin');


//
// Page - Signup
//
Basset::collection('page-signup', function($collection)
{
	$collection->javascript('js/pages/signup.js');
})->apply('JsMin');


//
// Page - Map
//
Basset::collection('google-maps-cr', function($collection)
{
	$collection->javascript('js/pages/map.js')->apply('JsMin');
});


//
// Page - Statistics
//
Basset::collection('page-statistics', function($collection)
{
	$collection->javascript('manager_tpl/js/raphael.min.js')->apply('JsMin');
	$collection->javascript('js/libs/nimbeo.raphael/popup.js')->apply('JsMin');
	$collection->javascript('js/libs/nimbeo.raphael/chart.js')->apply('JsMin');
	$collection->javascript('js/libs/nimbeo.raphael/donut.js')->apply('JsMin');
	$collection->javascript('js/libs/nimbeo.raphael/donutParam.js')->apply('JsMin');
});


Basset::collection('bootstrap-modal', function($collection)
{
	$collection->javascript('plugins/bootstrap-modal/js/bootstrap-modalmanager.js')->apply('JsMin');
	$collection->javascript('plugins/bootstrap-modal/js/bootstrap-modal.js')->apply('JsMin');
	$collection->stylesheet('plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css')->apply('CssMin');
	$collection->stylesheet('plugins/bootstrap-modal/css/bootstrap-modal.css')->apply('CssMin');
});

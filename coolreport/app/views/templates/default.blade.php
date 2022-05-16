<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title') {{ ( $__env->yieldContent('title') === '' ? '' : ' | ') ,  Config::get('local.site.name') }}</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="{{ Config::get('local.site.description') }}" />
	<meta name="author" content="Nimbeo">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="shortcut icon" href="{{ URL::asset('favicon.png') }}" type="image/x-icon" />
	<link rel="icon" href="{{ URL::asset('favicon.png') }}" type="image/x-icon" />
	
	{{-- Bootstrap, Font Awesome and other plugins --}}
	<!--[if IE 7]>{{ HTML::style('http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome-ie7.min.css') }}<![endif]-->
	@stylesheets('default_start')
	<!--[if lt IE 9]>@javascripts('IE8_HTML5')<![endif]-->

	
	{{-- Custom CSS PLUGINS for the actual page --}}
	@yield('specific-css-plugins')
	
	
	{{-- Custom CSS (Website) --}}
	@stylesheets('manager_template')
	@stylesheets('default_coolreport')
	
	
	{{-- Custom CSS INLINE for the actual page --}}
	@yield('custom-css')
	
	<script>
		var crHooks = [];
	</script>
</head>
<body class="{{ Route::currentRouteName() == 'landing' ? 'body-landing' : '' }}">
	{{-- Navbar --}}
	@section('navbar')
		@if(!(isset($embed) && $embed))
		@include( 'menubar.top' )
		@endif
	@show
	
	<!--=== Content Part ===-->
	<div class="container">

		<div class="row">
	
			@if( Route::currentRouteName() !== 'landing' && !(isset($embed) && $embed))
				@include('menubar.sidebar')
			@endif
			
			<!-- start: Content -->
			<div id="content" class="col-lg-10 col-sm-11 {{ Route::currentRouteName() == 'map' ? 'inh_nopadding' : '' }} {{ !(isset($embed) && $embed) ? '' : 'full' }}">
			
				{{-- Breadcrumb --}}
				@component('components.breadcrumb')
				
				{{-- Content --}}
				@yield('content')
			
			</div>
			<!-- end: Content -->
			
		</div><!--/row-->
		
	</div>
	<!--=== End Content Part ===-->
	

	{{-- Custom JS vars --}}
	<script>
	var $SITE_PATH = "{{ url('/') }}/",
		$SITE_NAME = "{{ Config::get('local.site.name') }}",
		$REPORT_URL = "{{ URL::to('report') }}/",
		$UPLD_MAX_SIZE = "{{ Config::get('local.uploads.size') }}", // KB
		$enable_signup = "{{ Config::get('local.enable.signup') }}",
		$AUTH = "{{ Auth::check() }}",
		$MARKER_TYPES = {
			'default': 'flecha.png',
			'report': 'flecha_report.png',
			'attachment': 'flecha_attachment.png'
		};
	</script>
	
	@if( Auth::check() )
		<script>
		var $USER = {
				id: "{{ Auth::user()->getKey() }}",
				firstname: "{{ Auth::user()->firstname }}",
				surname: "{{ Auth::user()->surname }}",
				url: "{{ Auth::user()->getUrl() }}"
			};
		</script>
	@endif
	
	
	{{-- JS Plugins --}}
	<!--[if !IE]> --><script>window.jQuery || document.write("<script src='{{ URL::asset('manager_tpl/js/jquery-2.0.3.min.js') }}'>"+"<"+"/script>");</script><!-- <![endif]-->
	<!--[if IE]><script>window.jQuery || document.write("<script src='{{ URL::asset('plugins/jquery-1.10.2.min.js') }}'>"+"<"+"/script>");</script><![endif]-->
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="{{ URL::asset('manager_tpl/js/excanvas.min.js') }}"></script><![endif]-->
	@javascripts('manager_template')
	@javascripts('mustache')
	
	
	{{-- Website's JS --}}
	@javascripts('default_coolreport')
	
	
	{{-- Custom JS PLUGINS for the actual page --}}
	@yield('specific-javascript-plugins')
	
	
	{{-- Custom JS INLINE for the actual page --}}
	@yield('custom-javascript')
	
	<script>
	$(document).ready(function()
	{
		for (var i = 0; i < crHooks.length; i++)
		{
			crHooks[i]();
		}
	});
	</script>
</body>
</html>

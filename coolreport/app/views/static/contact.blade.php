@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')
	
	<!--=== Content Part ===-->
	<div class="container">
	
		<div class="page-header inh_nomargin-top">
			<h1>{{ trans('sections.' . Route::currentRouteName()) }}</h1>
		</div

		<div class="row">
<!--
			<div class="span9">
				<div id="map" class="map map-box map-box-space margin-bottom-40"></div>
				<a href="javascript: window.open('
				https://maps.google.com/maps?q=40.4564237,-3.6853647&z=17&t=m&output=embed'
				, this.target, 'width=680, height=680'
				); return false;" class="thumbnail">
					<img src="
					http://maps.googleapis.com/maps/api/staticmapdesisn?center=40.4564237,-3.6853647&size=1200x200&sensor=true&zoom=15&scale=2&markers=color:blue%7C40.331760,-3.766972
					" class="static-map" data-latitude="40.4564237" data-longitude="-3.6853647" style="width:100%" />
				</a>
			</div>
-->
			<div class="span3">
				<div class="headline"><h3>Escríbenos a</h3></div>
				<ul class="unstyled who margin-bottom-20">
				<!--
					<li><a href="mailto:{{ Config::get('local.site.email') }}"><i class="icon-envelope-alt"></i> {{ Config::get('local.site.email') }}</a></li>
				-->
				<li><a href="mailto:user@sotec.es"><i class="icon-envelope-alt"></i> user@sotec.es </li>
				</ul>
			</div>
		</div>
	</div>
	<!--=== End Content Part ===-->

@stop


@section('specific-javascript-plugins')
	{{ HTML::script('http://maps.google.com/maps/api/js?sensor=true') }}
	{{ HTML::script('plugins/gmap/gmap.js') }}
@stop


@section('custom-javascript')
@stop
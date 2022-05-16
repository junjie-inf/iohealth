@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')
	
	<!--=== Content Part ===-->
	<div class="container">
	
		<div class="page-header inh_nomargin-top">
			<h1>{{ trans('sections.' . Route::currentRouteName()) }}</h1>
		</div>
		
	</div>
	<!--=== End Content Part ===-->

@stop
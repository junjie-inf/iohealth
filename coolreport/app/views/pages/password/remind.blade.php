@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<!--=== Content Part ===-->
<div class="container">
	
	<div class="page-header inh_nomargin-top">
		<h1>Recover your password</h1>
	</div
	
	<div class="row">
		
		<div class="col-sm-6">
			
			{{-- check for login errors flash var --}}
			@if( isset($error) )
				<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<h4>Error</h4>
					<p class="nomargin">{{ trans($error) }}</p>
				</div>
			@elseif( isset($status) )
				<div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<h4>Success</h4>
					<p class="nomargin">{{ trans($status) }}</p>
				</div>
			@endif

			{{ Form::open(array('route' => 'password.request', 'class' => 'form-horizontal form-login', 'role' => 'form')) }}

				<div class="form-group">
					{{ Form::label('email', 'Your email', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<input type="email" class="form-control" id="email" name="email" placeholder="Email" autofocus="autofocus" required value="{{ isset($input) ? $input['email'] : '' }}" />
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
						<button type="submit" class="btn btn-primary">Recover</button>
					</div>
				</div>
			
			{{ Form::close() }}
			
		</div>
		
	</div>

</div>
<!--=== End Content Part ===-->
	
@stop


@section('specific-javascript-plugins')
	{{-- basset_javascripts('page-login') --}}
@stop


@section('custom-javascript')
@stop
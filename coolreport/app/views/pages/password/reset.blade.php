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
			@if (Session::has('error'))
				<div class="alert alert-error">
					<a class="close" data-dismiss="alert" href="#">x</a>
					<h4>Error</h4>
					<p class="nomargin">{{ trans(Session::get('error')) }}</p>
				</div>
			@endif
			
			{{ Form::open(array('route' => array('password.update', $token), 'class' => 'form-horizontal form-login', 'role' => 'form')) }}
        		<input type="hidden" name="token" value="{{ $token }}">
			
				<div class="form-group">
					{{ Form::label('email', 'Your email', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<input type="email" class="form-control" id="email" name="email" placeholder="Email" autofocus="autofocus" required />
					</div>
				</div>
				<div class="form-group">
					{{ Form::label('password', 'New password', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<input type="password" class="form-control" id="password" name="password" placeholder="New password" autofocus="autofocus" required />
					</div>
				</div>
				<div class="form-group">
					{{ Form::label('password_confirmation', 'Password confirmation', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Password confirmation" autofocus="autofocus" required />
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
						<button type="submit" class="btn btn-primary" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending...">Submit</button>
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
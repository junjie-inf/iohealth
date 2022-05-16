@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<!--=== Content Part ===-->
<div class="container col-md-offset-2">
	
	<div class="page-header inh_nomargin-top">
		<h1>Por favor, inicia sesión</h1>
	</div
	
	<div class="row">
		
		<div class="col-sm-6">
			
			{{ Form::open(array('url' => URL::route('auth.login'), 'class' => 'form-horizontal form-login', 'role' => 'form')) }}

				{{-- check for login errors flash var --}}
				@if (Session::has('login_errors'))
					<div class="alert alert-error">
						<a class="close" data-dismiss="alert" href="#">x</a>
						<h4>Email o contraseña errónea</h4>
						<p class="nomargin">Comprueba los campos y vuelve a intentarlo, o <a href="#recovery">recupera tu contraseña</a></p>
					</div>
				@endif
			
				<div class="form-group">
					{{ Form::label('email', 'Email', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-6">
						<input type="email" class="form-control" id="email" name="email" placeholder="Email" autofocus="autofocus" required />
					</div>
				</div>
				<div class="form-group">
					{{ Form::label('password', 'Password', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-6">
						<input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required />
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-6">
						<a href="{{ URL::route('password.request') }}" class="btn btn-info">¿Olvidaste la contraseña?</a>

						<button type="submit" class="btn btn-primary pull-right" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending...">Iniciar sesión</button>
					</div>
				</div>
			
			{{ Form::close() }}
			
		</div>
		
	</div>

</div>
<!--=== End Content Part ===-->
	
@stop


@section('specific-javascript-plugins')
	{{ basset_javascripts('page-login') }}
@stop


@section('custom-javascript')
@stop
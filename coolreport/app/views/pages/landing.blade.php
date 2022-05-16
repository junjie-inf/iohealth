@extends('templates.default')

@section('content')

{{-- Medium / Large --}}
<div class="floating-desktop visible-md visible-lg">
	
	<div class="page-header inh_nomargin-top text-center">
		<img src="{{ URL::asset('img/logo.png') }}" alt="" style="width:250px;"/>
	</div>
	
	<div class="row">
		
		<div class="col-sm-12">
			
			<div class="col-sm-12 text-center padding-top-20">
				<p>Por favor, inicia sesión</p>
				
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
						<div class="col-sm-12">
							<input type="email" class="form-control" id="email" name="email" placeholder="Email" autofocus="autofocus" required />
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required />
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<button type="submit" class="btn btn-inverse" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending..." style="width:100%; color:white">ACCEDER</button>
							<p class="text-center"><a href="{{ URL::route('password.request') }}">¿Olvidaste la contraseña?</a></p>	
						</div>
					</div>	

				
				{{ Form::close() }}
			</div>
			<!--<div class="col-sm-5" style="padding-left: 10%; border-left: 1px solid; padding-bottom: 40px">
				<p style="padding-left: 5px;"><a href="{{ URL::route('auth.signup') }}"><i class="icon-edit"></i> Crear una cuenta</a></p>
			</div>-->

		</div>
	</div>
		
	<div class="row">
		<div class="col-sm-12">
			<div class="col-sm-4">
				<p class="text-muted" style="margin-bottom: 0px;">
					{{ Config::get('local.site.name') }} &copy; {{ date('Y') }}.
				</p>		
			</div>

			<div class="col-sm-8">
				<p class="text-center inh_small">
					<a href="{{ URL::to('about') }}" class="grey-hover">{{ trans('sections.static.about') }}</a> &middot;
					<a href="{{ URL::to('contact') }}" class="grey-hover">{{ trans('sections.static.contact') }}</a> &middot;
					<a href="{{ URL::to('terms') }}" class="grey-hover">{{ trans('sections.static.terms') }}</a>
				</p>
			</div>
		</div>
	</div>
</div>

{{-- Small / Extra small --}}
<div class="floating-mobile visible-xs visible-sm">
	
	<div class="page-header inh_nomargin-top text-center">
		<img src="{{ URL::asset('img/flecha.png') }}" alt="" style="width:100px;"/>
		<p>{{ Config::get('local.site.name') }}</p>
	</div>
	
	<div class="row">
		
		<div class="col-sm-12">
			
			<div class="col-sm-12 text-center padding-top-20">
				<p>Por favor, inicia sesión</p>
				
				{{ Form::open(array('url' => URL::route('auth.login'), 'class' => 'form-horizontal form-login', 'role' => 'form')) }}

					{{-- check for login errors flash var --}}
					@if (Session::has('login_errors'))
						<div class="alert alert-error">
							<!--<a class="close" data-dismiss="alert" href="#">x</a>-->
							<h4>Email o contraseña errónea</h4>
							<p class="nomargin">Comprueba los campos y vuelve a intentarlo, o <a href="#recovery">recupera tu contraseña</a></p>
						</div>
					@endif
				
					<div class="form-group">
						<div class="col-sm-12">
							<input type="email" class="form-control" id="email" name="email" placeholder="Email" autofocus="autofocus" required />
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required />
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<button type="submit" class="btn btn-inverse" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending..." style="width:100%; color:white">ACCEDER</button>
							<p class="text-center"><a href="{{ URL::route('password.request') }}">¿Olvidaste la contraseña?</a></p>	
						</div>
					</div>	

				
				{{ Form::close() }}
			</div>
			<!--<div class="col-sm-12 text-center">
				<p style="padding-left: 5px;"><a href="{{ URL::route('auth.signup') }}"><i class="icon-edit"></i> Crear una cuenta</a></p>
			</div>-->

		</div>
	</div>
		
	<div class="row">
		<div class="col-sm-12">
			<div class="col-sm-12 text-center">
				<p class="text-muted padding-top-60" style="margin-bottom: 0px;">
					{{ Config::get('local.site.name') }} &copy; {{ date('Y') }}. 
				</p>
			</div>

			<div class="col-sm-12">
				<p class="text-center inh_small">
					<a href="{{ URL::to('about') }}" class="grey-hover">{{ trans('sections.static.about') }}</a> &middot;
					<a href="{{ URL::to('contact') }}" class="grey-hover">{{ trans('sections.static.contact') }}</a> &middot;
					<a href="{{ URL::to('terms') }}" class="grey-hover">{{ trans('sections.static.terms') }}</a>
				</p>
			</div>
		</div>
	</div>
</div>



@stop


@section('specific-javascript-plugins')
	{{ basset_javascripts('page-login') }}
@stop


@section('custom-javascript')
@stop

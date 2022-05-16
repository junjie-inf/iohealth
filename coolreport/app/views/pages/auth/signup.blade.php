@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<!--=== Content Part ===-->
<div class="container">
	
	<div class="page-header inh_nomargin-top">
		<h1>Crea tu cuenta</h1>
	</div
	
	<div class="row">
		
		<div class="col-sm-6">
		
			{{ Form::open(array('class' => 'form-horizontal form-signup', 'role' => 'form', 'autocomplete' => 'off')) }}

				<div class="form-group text-center">
					<span class="text-muted inh_small"><span class="text-danger">*</span> Todos los campos son necesarios</span>
				</div>

				<div class="form-group">
					{{ Form::label('firstname', 'Nombre', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						{{ Form::text('firstname', Input::old('firstname'), array('class' => 'form-control',
																					'id' => 'firstname',
																					'required' => 'required',
																					'autofocus' => 'autofocus',
																					'placeholder' => 'Nombre')) }}
					</div>
				</div>
				<div class="form-group">
					{{ Form::label('surname', 'Apellidos', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						{{ Form::text('surname', Input::old('surname'), array('class' => 'form-control',
																				'id' => 'surname',
																				'required' => 'required',
																				'placeholder' => 'Apellidos')) }}
					</div>
				</div>
				<div class="form-group">
					{{ Form::label('email', 'Email', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="" required />
					</div>
				</div>
				<div class="form-group">
					{{ Form::label('password', 'Contraseña', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 6 caracteres" value="" required />
					</div>
				</div>

				<div class="form-group">
					{{ Form::label('password2', 'Verificar Contraseña', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<input type="password" class="form-control" id="password2" name="password2" placeholder="Mínimo 6 caracteres" value="" required />
					</div>
				</div>

				<div class="form-group">
					{{ Form::label('gender', 'Genero', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<label class="radio">
							<input name="gender" type="radio" value="5889efb0944a93" required checked>Hombre
						</label>
						<label class="radio">
							<input name="gender" type="radio" value="5889efb0944a94" required>Mujer
						</label>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label('height', 'Altura (cm)', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<input class="form-control" name="height" type="number" value="" placeholder="Altura" required>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label('birth', 'Fecha de nacimiento', array('class' => 'col-sm-3 control-label')) }}
					<div class="col-sm-9">
						<input class="form-control date-picker" data-date-format="yyyy-mm-dd" required name="birth" placeholder="Fecha de nacimiento" type="text" value="">
					</div>
				</div>

				<div class="controls form-inline clearfix">
					<p class="inh_small text-muted span8">
						By creating an account, you agree our 
						<a href="{{ URL::to('terms') }}" class="color-green">{{ trans('sections.static.terms') }}</a>, and that you have read the
						<a href="{{ URL::to('cookies') }}" class="color-green">Use of cookies</a>
					</p>
					<p class="span4">
						<button type="submit" class="btn btn-primary pull-right" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending...">{{ trans('sections.auth.signup') }}</button>
					</p>
				</div>

				<hr />
				<p>Tienes una cuenta? entonces <a href="{{ URL::to('auth') }}" class="color-green inh_bold">{{ strtolower(trans('sections.auth.index')) }}</a>.</p>

			{{ Form::close() }}
			
		</div>
		
	</div>
	
</div>
<!--=== End Content Part ===-->

@stop


@section('specific-javascript-plugins')
	{{ basset_javascripts('page-signup') }}
	@javascripts('cr_template')
@stop


@section('custom-javascript')
@stop
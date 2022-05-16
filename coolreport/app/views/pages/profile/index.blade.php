@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

{{-- MY SETTINGS --}}

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-cog"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
				<div class="box-icon">
					<a href="{{ Auth::user()->getUrl() }}"><i class="icon-arrow-left"></i><span>View my profile</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<form class="form-horizontal form-edit" action="" data-type="profile" data-id="{{ $user->id }}"  autocomplete="off">
					<input type="hidden" name="_method" value="PUT" />

					<fieldset class="col-sm-12">

						<div class="row">
							<div class="form-group">
								<div class="col-sm-6">
									<label class="control-label" for="firstname">Nombre</label>
									<div class="controls">
										<input class="form-control" id="firstname" name="firstname" type="text" value="{{ $user->firstname }}" required />
									</div>
								</div>

								<div class="col-sm-6">
									<label class="control-label" for="surname">Apellidos</label>
									<div class="controls">
										<input class="form-control" id="surname" name="surname" type="text" value="{{ $user->surname }}" required />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-6">
									<label class="control-label" for="email">Correo electrónico</label>
									<div class="controls">
										<input class="form-control" id="email" name="email" type="email" value="{{ $user->email }}" required />
									</div>
								</div>
								
								<div class="col-sm-3">
									<label class="control-label" for="password">Contraseña</label>
									<div class="controls">
										<input class="form-control" id="password" name="password" type="password" value="" />
									</div>
								</div>

								<div class="col-sm-3">
									<label class="control-label" for="password2">Confirmación de la contraseña</label>
									<div class="controls">
										<input class="form-control" id="password_confirmation" name="password_confirmation" type="password" value="" />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-6">
									<label class="control-label" for="role">Rol</label>
									<div class="controls">
										<input class="form-control" id="role" name="role" type="text" value="{{ $user->role->title }}" disabled />
									</div>
								</div>
								
								<div class="col-sm-6">
									<label class="control-label" for="group">Grupo</label>
									<div class="controls">
										<input class="form-control" id="group" name="group" type="text" value="{{ $user->group->title }}" disabled />
									</div>
								</div>
							</div>
						</div>

<!--
						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="controls">
										<label class="checkbox">
											<input type="checkbox" id="gossip" name="gossip" {{ $user->gossip ? 'checked' : '' }} />
											Notify all events
										</label>
									</div>
								</div>
							</div>
						</div>
-->

					</fieldset>

					<div class="clearfix">
						<div class="col-lg-12 inh_nopadding">
							<div class="form-actions clearfix text-center">
								<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending...">Guardar los cambios</button>
							</div>
						</div>
					</div>

				</form>
				
			</div>
		</div>


		<div class="box">
			<div class="box-header">
				<h2><i class="icon-bullhorn"></i><span class="break"></span>Notificaciones activas</h2>
			</div>
			<div class="box-content">
				
				<table class="table table-striped table-bordered table-condensed vertical-middle">
					<tr>
						<th>Type</th>
						<th>Title</th>
						<th style="width:10px"></th>
					</tr>

					@if( ! $user->followed->isEmpty() )
						@foreach( $user->followed as $followed )
							<tr>
								<td>{{ $followed->followable_type }}</td>

								<td>
									<a href="{{ URL::route(lcfirst($followed->followable_type).'.show', $followed->followable_id) }}">
										@if( $followed->followable_type == 'User' )
											{{ $followed->followable->getFullname() }}
										@else
											{{ $followed->followable->title }}
										@endif
									</a>
								</td>

								<td class="text-right">
									<a class="btn btn-danger action-unfollow" href="javascript:void(0)"
										data-followable-type="{{ lcfirst($followed->followable_type) }}"
										data-followable-id="{{ $followed->followable_id }}"
										data-toggle="false"
										data-loading-text="<i class='icon-spinner icon-spin'></i>">
											<i class="icon-trash"></i>
									</a>
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="3">No data available</td>
						</tr>
					@endif

				</table>
				
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->
	
@stop


@section('specific-javascript-plugins')
@stop


@section('custom-javascript')
	{{-- inline scripts related to this page --}}
@stop
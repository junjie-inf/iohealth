@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-pencil"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('user.show', $data->id) }}"><i class="icon-arrow-left"></i><span>{{ trans('users.back') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<form class="form-horizontal form-edit" action="" data-type="user" data-id="{{ $data->id }}">
					<input type="hidden" name="_method" value="PUT" />

					<fieldset class="col-sm-12">

						<div class="row">
							<div class="form-group">
								<div class="col-sm-6">
									<label class="control-label" for="firstname">{{ trans('users.firstname') }}</label>
									<div class="controls">
										<input class="form-control" id="firstname" name="firstname" type="text" value="{{ $data->firstname }}" required />
									</div>
								</div>

								<div class="col-sm-6">
									<label class="control-label" for="surname">{{ trans('users.surname') }}</label>
									<div class="controls">
										<input class="form-control" id="surname" name="surname" type="text" value="{{ $data->surname }}" required />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-6">
									<label class="control-label" for="email">{{ trans('users.email') }}</label>
									<div class="controls">
										<input class="form-control" id="email" name="email" type="email" value="{{ $data->email }}" required />
									</div>
								</div>
								
								<div class="col-sm-3">
									<label class="control-label" for="password">{{ trans('users.password') }}</label>
									<div class="controls">
										<input class="form-control" id="password" name="password" type="password" value="" />
									</div>
								</div>

								<div class="col-sm-3">
									<label class="control-label" for="password2">{{ trans('users.confirmpassword') }}</label>
									<div class="controls">
										<input class="form-control" id="password_confirmation" name="password_confirmation" type="password" value="" />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-6">
									<label class="control-label" for="role">{{ trans('users.role') }}</label>
									<div class="controls">
										<select class="form-control" id="role_id" name="role_id">
										@foreach( Role::lists('title', 'id') as $id => $title )
											<option value="{{ $id }}" {{ ( $data->role->id == $id ? 'selected' : '' ) }}>{{ $title }}</option>
										@endforeach
										</select>
									</div>
								</div>

								<div class="col-sm-6">
									<label class="control-label" for="group">{{ trans('users.group') }}</label>
									<div class="controls">
										<select class="form-control" id="group_id" name="group_id">
										@foreach( Group::lists('title', 'id') as $id => $title )
											<option value="{{ $id }}" {{ ( $data->group->id == $id ? 'selected' : '' ) }}>{{ $title }}</option>
										@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="controls">
										<label class="checkbox">
											<input type="checkbox" id="gossip" name="gossip" {{ $data->gossip ? 'checked' : '' }} />
											{{ trans('users.notifyallevents') }}
										</label>
									</div>
								</div>
							</div>
						</div>

					</fieldset>

					<div class="clearfix">
						<div class="col-lg-12 inh_nopadding">
							<div class="form-actions clearfix text-center">
								<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('users.sending') }}">{{ trans('users.savechanges') }}</button>
							</div>
						</div>
					</div>

				</form>
				
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
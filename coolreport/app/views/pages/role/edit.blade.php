@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-pencil"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
				<div class="box-icon">
					<a href="{{ URL::route('role.show', $data->id) }}"><i class="icon-arrow-left"></i><span>{{ trans('role.back') }}</span></a>
				</div>
			</div>
			<div class="box-content">
				
				<form class="form-horizontal form-edit" action="" data-type="role" data-id="{{ $data->id }}">
					<input type="hidden" name="_method" value="PUT" />
					
					<fieldset class="col-sm-12">
						
						<div class="form-group">
							<label class="control-label" for="title">{{ trans('role.title') }}</label>
							<div class="controls">
								<input class="form-control" id="title" name="title" type="text" value="{{ $data->title }}" required autofocus />
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label" for="permissions">{{ trans('role.permissions') }}</label>
							
							<table class="table vertical-middle" style="width:auto; margin:0 auto">
								<thead>
									<tr>
										<th class="text-right">{{ trans('role.permission') }}</th>
										<th class="text-center">{{ trans('role.scope') }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach( Config::get('local.permission.types') as $permission )
										@php( $rolePermission = $data->getPermissionValue($permission) )
										<tr>
											<td class="text-right">{{ ucwords(strtolower(str_replace('_', ' ', $permission))) }}</td>
											<td>
												<div class="btn-group" data-toggle="buttons">
													<label class="btn btn-primary {{ $rolePermission === 'ALL' ? 'active' : '' }}">
														<input type="radio" name="{{ $permission }}" id="" value="ALL" {{ $rolePermission === 'ALL' ? 'checked' : '' }}> {{ trans('role.all') }}
													</label>
													<label class="btn btn-primary {{ $rolePermission === 'GROUP' ? 'active' : '' }}">
														<input type="radio" name="{{ $permission }}" id="" value="GROUP" {{ $rolePermission === 'GROUP' ? 'checked' : '' }}> {{ trans('role.group') }}
													</label>
													<label class="btn btn-primary {{ $rolePermission === 'OWN' ? 'active' : '' }}">
														<input type="radio" name="{{ $permission }}" id="" value="OWN" {{ $rolePermission === 'OWN' ? 'checked' : '' }}> {{ trans('role.own') }}
													</label>
													<label class="btn btn-primary {{ $rolePermission === 'NONE' ? 'active' : '' }}">
														<input type="radio" name="{{ $permission }}" id="" value="NONE" {{ $rolePermission === 'NONE' ? 'checked' : '' }}> {{ trans('role.none') }}
													</label>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					
					</fieldset>

					<div class="clearfix">
						<div class="col-lg-12 inh_nopadding">
							<div class="form-actions clearfix text-center">
								<button type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> {{ trans('role.sending') }}">{{ trans('role.savechanges') }}</button>
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
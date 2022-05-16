@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">		
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span>{{ trans('sections.' . Route::currentRouteName()) }}</h2>
			</div>
			<div class="box-content">
				<table class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">

					<thead>
						<tr>
							<th>{{ trans('ranking.user') }}</th>
							<th>Karma</th>
						</tr>
					</thead>

					<tbody>

						@if( $data )
							@foreach( $data as $obj )
								<tr>
									<td><a href="{{ $obj->user->getUrl() }}">{{ $obj->user->getFullname() }}</a></td>
									<td class="inh_nowrap">{{ floor($obj->score) }}</td>
								</tr>
							@endforeach
						@endif

					</tbody>

				</table>
				
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->

@stop


@section('specific-javascript-plugins')
@javascripts('cr_template')
@stop


@section('custom-javascript')
@stop
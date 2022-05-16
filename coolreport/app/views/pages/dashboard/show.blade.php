@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">

	<div class="col-lg-12">
		@if( !isset($embed) || !$embed )
			<div class="box">
				<div class="box-content">
					<div class="btn-group">
						<button class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">{{ trans('dashboard.switchdashboard') }} <span class="caret"></span></button>
						<ul class="dropdown-menu">
							@foreach( $dashboards as $db )
								<li><a href="{{ URL::route('dashboard.show', $db->id) }}">{{ $db->title }}</a></li>
							@endforeach
						</ul>
					</div>
					<div class="btn-group">
						<a class="btn btn-large btn-success" href="{{ URL::route('dashboard.create') }}"><i class="icon-plus"></i> {{ trans('dashboard.createnew') }}</a>
					</div>
				</div>
			</div>
		@endif


		<div class="box">
			<div class="box-header">
				<h2><i class="icon-dashboard"></i><span class="break"></span>{{ $dashboard->title }}</h2>
			</div>

			<div class="box-content form-dashboard action-data"  action="" data-id="{{ $dashboard->id }}" data-type="dashboard" data-redirect-url="{{ URL::route('dashboard.index') }}">
				<ul class="nav tab-menu nav-tabs pull-right">
					@if( isset($embed) && $embed )
						<div style=""><a href="http://coolreport.nimbeo.com" class="btn btn-info btn-setting" style="font-size: x-small; color: white; background: #383e4b; ">{{ trans('dashboard.poweredby') }} <img src="../img/logo.png" height="22px" class="visible-md visible-lg visible-xs visible-sm"></a></div>
					@else
						<li><a class="btn {{ $dashboard->removable ? 'btn-danger action-delete' : 'disabled' }}" href="javascript:void(0)" data-loading-text="<i class='icon-spinner icon-spin'></i>"><i class="icon-trash"></i></a></li>
						<li>
							<a class="btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
								<i class="icon-share"></i> {{ trans('dashboard.share') }} <i class="icon-caret-down"></i>
							</a>
							<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
								<li><a id="share-btn" href="javascript:void(0);"><i class="icon-link"></i> {{ trans('dashboard.publiclink') }}</a></li>
								<li><a id="export-img" href="{{ URL::route('sharedobj.share_img', array('dashboard', $dashboard->id)) }}"><i class="icon-picture"></i> {{ trans('dashboard.image') }}</a></li>
							</ul>
						</li>

						@if( $dashboard->editable )
							<li><a href="{{ URL::route('dashboard.edit', $dashboard->id) }}"><i class="icon-pencil"></i> {{ trans('dashboard.edit') }}</a></li>
						@endif

						

						<div id="box-link" class="well" style="padding-bottom: 20px; display: none;">
						<a id="close-boxlink" style="float:right;" href="javascript:void(0);"><i class="icon-angle-up"></i> {{ trans('dashboard.close') }}</a>
						<h2>{{ trans('dashboard.publiclink') }}</h2>
						<input class="form-control" type="text" readonly>
						<a id="enable-link" style="margin-top:20px; display:none;" class="btn btn-large btn-primary" href="javascript:void(0);">{{ trans('dashboard.enablelink') }}</a>
						<a id="disable-link" style="margin-top:20px;" class="btn btn-large btn-primary" href="javascript:void(0);">{{ trans('dashboard.disablelink') }}</a>
						</div>
					@endif
				</ul>

				@if( !isset($embed) || !$embed)
				<div id="box-link" class="well" style="padding-bottom: 20px; display: none;">
					<a id="close-boxlink" style="float:right;" href="javascript:void(0);"><i class="icon-angle-up"></i> {{ trans('dashboard.close') }}</a>
					<h2>{{ trans('dashboard.publiclink') }}</h2>
					<input class="form-control" type="text" readonly>
					<a id="enable-link" style="margin-top:20px; display:none;" class="btn btn-large btn-primary" href="javascript:void(0);">{{ trans('dashboard.enablelink') }}</a>
					<a id="disable-link" style="margin-top:20px;" class="btn btn-large btn-primary" href="javascript:void(0);">{{ trans('dashboard.disablelink') }}</a>
				</div>
				@endif

				{{-- Items --}}
				@foreach( $dashboard->datum->items as $row )
				<div class="row margin-top-20">
					@foreach( $row as $col )
						<div class="col-xs-{{ 12/count($row) }}">
							<div class="box">
								<div class="box-header">
									<h2><i class="icon-list"></i>{{ $col->title }}</h2>
									<div class="box-icon">
										<a class="btn-minimize" href="widgets.html#"><i class="icon-chevron-up"></i></a>
									</div>
								</div>
								<div class="box-content">
									@if( isset($renders[$col->id]) )
											{{ $renders[$col->id] }}
									@endif
								</div>
							</div>
						</div>
					@endforeach
				</div>
				@endforeach
			</div>
		</div>
	</div><!--/col-->
</div><!--/row-->
	
@stop

@section('custom-css')
@stop


@section('specific-javascript-plugins')
@javascripts('cr_maps')
@stop


@section('custom-javascript')
@if( !isset($embed) || !$embed )
<script>
// Share link
$(function() {
	// Share button
	$('#share-btn').on('click', function(e) {
		// Show link box
		$('#box-link').slideDown();

		// Get link with hash
		$.ajax({
			type: 'POST',
			url: "{{ url::route('sharedobj.share', array('dashboard', $dashboard->id)) }}",
			success: function(data) {
				// Set input
				$('#box-link > input').val("{{ url::to('/') }}/shared/"+data);
			},
			error: function(xhr, type) {
				$('#box-link input').val('Failed to get link');
			}
		});
	});

	// Disable button
	$('#disable-link').on('click', function(e) {
		// Toggle the link state
		$.ajax({
			type: 'POST',
			url: "{{ url::route('sharedobj.toggle', array('dashboard', $dashboard->id, 0)) }}",
			success: function(data) {
				if (data == 0) {
					// Show 'enable' button and hide the 'disable' one
					$('#disable-link').hide();
					$('#enable-link').show();
				}
			},
			error: function(xhr, type) {
				console.log('Error disabling');
			}
		});
	});

	// Enable button
	$('#enable-link').on('click', function(e) {
		// Toggle the link state
		$.ajax({
			type: 'POST',
			url: "{{ url::route('sharedobj.toggle', array('dashboard', $dashboard->id, 1)) }}",
			success: function(data) {
				if (data == 1) {
					// Show 'enable' button and hide the 'disable' one
					$('#disable-link').show();
					$('#enable-link').hide();
				}
			},
			error: function(xhr, type) {
				console.log('Error enabling');
			}
		});
	});

	// Close button
	$('#close-boxlink').on('click', function(e) {
		// Close the box
		$('#box-link').slideUp();
	});
});
</script>
@endif
@stop

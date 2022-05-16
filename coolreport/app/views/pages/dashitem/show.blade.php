@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">

	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-th"></i><span class="break"></span>{{ $dashboard->title }}</h2>
			</div>

			<div class="box-content">
				<ul class="nav tab-menu nav-tabs">
					@if( isset($embed) && $embed )
						<div style="position: absolute; right:1%; top:-1%"><a href="http://coolreport.nimbeo.com" class="btn btn-info btn-setting" style="font-size: x-small; color: white; background: #383e4b; ">{{ trans('dashitems.poweredby') }} <img src="../img/logo.png" height="22px" class="visible-md visible-lg visible-xs visible-sm"></a></div>
					@else
						<li>
							<a class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
								<i class="icon-share"></i> {{ trans('dashitems.share') }} <i class="icon-caret-down"></i>
							</a>
							<ul class="dropdown-menu">
								<li><a id="share-btn" href="javascript:void(0);"><i class="icon-link"></i> {{ trans('dashitems.publiclink') }}</a></li>
								<li><a href="{{ URL::route('sharedobj.share_csv', array('dashitem', $dashboard->id)) }}"><i class="icon-file"></i> CSV</a></li>
								<li><a href="{{ URL::route('sharedobj.share_img', array('dashitem', $dashboard->id)) }}"><i class="icon-picture"></i> {{ trans('dashitems.image') }}</a></li>
							</ul>
						</li>
						@if( $dashboard->editable )
							<li><a href="{{ URL::route('dashitem.edit', $dashboard->id) }}"><i class="icon-pencil"></i> {{ trans('dashitems.edit') }}</a></li>
						@endif

						<li><a href="{{ URL::to('dashitem') }}"><i class="icon-arrow-left"></i> {{ trans('dashitems.backtolist') }}</a></li>
					@endif
				</ul>

				@if( !isset($embed) || !$embed)
				<div id="box-link" class="well" style="padding-bottom: 20px; display: none;">
					<a id="close-boxlink" style="float:right;" href="javascript:void(0);"><i class="icon-angle-up"></i> {{ trans('dashitems.close') }}</a>
					<h2>Public link</h2>
					<input class="form-control" type="text" readonly>
					<a id="enable-link" style="margin-top:20px; display:none;" class="btn btn-large btn-primary" href="javascript:void(0);">{{ trans('dashitems.enablelink') }}</a>
					<a id="disable-link" style="margin-top:20px;" class="btn btn-large btn-primary" href="javascript:void(0);">{{ trans('dashitems.disablelink') }}</a>
				</div>
				@endif

				@if( isset($embed) && $embed )
					<br></br>
				@else
					<div class="row margin-top-20">
						<div class="col-xs-12">
							<span class="pull-right">
								<span class="label label-warning">Created {{ $dashboard->created_at }}</span>
							</span>
						</div>
					</div>
				@endif

				<div class="row margin-top-20">
					<div class="col-xs-12">
						{{ $render }}
					</div>
				</div>

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
var changed = false;
// Share link
$(function() {
	// Share button
	$('#share-btn').on('click', function(e) {
		// Show link box
		$('#box-link').slideDown();

		// Get link with hash
		$.ajax({
			type: 'POST',
			url: "{{ url::route('sharedobj.share', array('dashitem', $dashboard->id)) }}",
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
			url: "{{ url::route('sharedobj.toggle', array('dashitem', $dashboard->id, 0)) }}",
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
			url: "{{ url::route('sharedobj.toggle', array('dashitem', $dashboard->id, 1)) }}",
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

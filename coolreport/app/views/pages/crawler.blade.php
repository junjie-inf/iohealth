@extends('templates.default')

@section('title', trans('sections.' . Route::currentRouteName() ))

@section('content')

<div class="row">
	
	<div class="col-lg-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-file-text-alt"></i><span class="break"></span>{{ trans('sections.crawler') }}</h2>
				<div class="box-icon">
					<a href="{{ URL::previous() }}"><i class="icon-arrow-left"></i><span>Back</span></a>
				</div>
			</div>
			
			<div class="box-content">
						<div class="form-group col-sm-3">
							{{ Form::label('hashtag','Hashtag', array('for' => 'hashtag', 'class' => 'control-label')); }}
							<div class="controls clearfix margin-bottom-10">
								<input class="form-control" name="hashtag" id="hashtag" type="text" value="#">
							</div>
						</div>
						
						<div class="form-group col-sm-1">
							{{ Form::label('minutes','Repeat each', array('for' => 'minutes', 'class' => 'control-label')); }}
							<div class="controls clearfix margin-bottom-10">
								<Input class="form-control" name="minutes" id="minutes" type="number" value="5" min="5" max="60"><span>minutes</span>
							</div>
						</div>

						<div class="form-group col-sm-12" id="import-table" style="overflow: auto">
						</div>
						<div class="clearfix">
							<div class="col-lg-12 inh_nopadding">
								<div class="form-actions clearfix text-center">
									<button id="saveCrawler" type="submit" class="btn btn-primary btn-lg btn-block" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending...">Save changes</button>
								</div>
							</div>
						</div>
					</div>
				</div>			
			</div>
		</div>
	</div><!--/col-->

</div><!--/row-->

@stop


@section('specific-javascript-plugins')
@stop


@section('custom-javascript')
	{{-- inline scripts related to this page --}}
	<script>
	$(document).ready(function(){
			$.get("https://www.sotec-oigo.com:8080/WebServicesTemplate/api/crawler/getConfig").success(function (returnedConfig){
				var config = returnedConfig.split(",");
				$('#hashtag').val(config[0]);
				$('#minutes').val(config[1]);
			});

			$(document).on('click', '#saveCrawler', function(){
				var hashtag = $('#hashtag').val();
				var minutes = $('#minutes').val();

				$.get("https://www.sotec-oigo.com:8080/WebServicesTemplate/api/crawler/setConfig?hashtag="+ hashtag.substr(1) +"&minutes="+ minutes).success(function (data){
				});				
				window.history.back();
            });
	});
	</script>
@stop
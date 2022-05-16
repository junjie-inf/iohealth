<?php
/* View modal report
 * -----------------------------*/ ?>

<script id="tpl-viewReport" type="text/x-handlebars-template">
	<div id="modal-report-{{report_id}}" class="modal container fade action-data"  data-id="{{ data.id }}" data-type="report" tabindex="-1" style="display:none" role="dialog" aria-labelledby="modal-reportLabel" aria-hidden="true">
		<div class="modal-header bg-white">
			<button type="button" class="close big" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h2 class="modal-title blue modal-title-report"><small>{{ data.title }}</small></h2>
		</div>
		<div class="modal-body inh_nopadding">

			<ul class="nav nav-pills nav-tabs">
				<li class="active"><a href="#info" data-toggle="tab"><i class="icon-info-sign"></i> Info</a></li>
				<li><a href="#comments" data-toggle="tab"><i class="icon-comments"></i> Comments ({{n_comments}})</a></li>
				{{#if removable}}
					<li class="pull-right action-delete"><a href="javascript:void(0)"><i class="icon-trash"></i> Remove</a></li>
				{{/if}}
				{{#if editable}}
					<li class="pull-right"><a href="{{editable}}"><i class="icon-pencil"></i> Edit</a></li>
				{{/if}}
			</ul>

			<div id="myTabContent" class="tab-content">
				<div class="tab-pane fade in active" id="info">
				
					<div class="row">
						<div class="col-xs-12">
							<span class="pull-left label label-info">
								<span style="font-weight:normal">Published by</span> <a href="{{ data.user.publicUrl }}" class="white">{{ user_fullname }}</a>
							</span>
							<span class="pull-right">
								<span class="label label-warning">Created {{{ data.created_at }}}</span>
							</span>
						</div>
					</div>
				
					<div class="row">
						<div class="col-xs-12">

						</div>
					</div>
					<div class="row padding-top-20">
						<div class="col-xs-4">
							<dl class="dl-horizontal inh_nomargin-top">
								<dt>Template</dt><dd>{{{ data.template.title }}}</dd>
								<br />
								<dt>Country</dt><dd>{{{ data.country }}}</dd>
								<dt>City</dt><dd>{{{ data.city }}}</dd>
								<dt>Address</dt><dd>{{{ data.address }}}</dd>
								<br />
								<dt>Latitude</dt><dd>{{ data.geo.coordinates.[1] }}</dd>
								<dt>Longitude</dt><dd>{{ data.geo.coordinates.[0] }}</dd>
							</dl>
						</div>
						<div class="col-xs-8">
							<div class="box-content report-data-view well well-small" style="background:#FFF">
								<form class="report-content">
									{{{ content }}}
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="comments">

					<div class="row">
						<div class="col-lg-12 discussions">
							<ul>
								{{#ifEq AUTH '1'}}
									<li class="clearfix">
										<form id="" action="" method="POST" class="crs_nomargin" accept-charset="UTF-8" data-report="{{ report_id }}">
											<textarea class="diss-form input-limiter" name="new_msg_text" data-limit="500" data-clase_alert="text-error" placeholder="Write comment" required style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 44px;"></textarea>
											<button type="submit" class="btn btn-primary margin-top-10 send-comment" data-loading-text="<i class='icon-spinner icon-spin'></i> Sending...">Send</button>
										</form>
									</li>
								{{/ifEq}}
								
								{{#each data.comments}}
									<li>
										<div class="name"><a href="{{this.user_id}}" title="View profile">{{{ this.user.firstname }}} {{{ this.user.surname }}}</a></div>
										<div class="date">{{{ this.created_at }}}</div>

										<div class="message">
											{{#nl2br this.content}}
											{{/nl2br}}
										</div>
									</li>
								{{/each}}
							</ul>
						</div>
					</div>

				</div>
			</div>

		</div>
		<div class="modal-footer bg-white inh_nomargin-top">
			<button type="button" data-dismiss="modal" class="btn btn-primary">&nbsp;&nbsp;Close&nbsp;&nbsp;</button>
		</div>
	</div>
</script>
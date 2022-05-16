<script id="tpl-infowindow-list-reports" type="text/x-handlebars-template">
	<div class="clearfix report-infowindow">
		<div class="task-list">
			<div class="priority normal">
				<span>Reports at this point</span>
				<div style="float:right;">
					<span class="{{#if range.hasPrev}}btn-iw-page{{else}}btn-iw-page-disabled{{/if}} btn-iw-page-prev"><i class="icon-angle-left"></i></span>
					{{range.start}}-{{range.end}} / {{range.total}}
					<span class="{{#if range.hasNext}}btn-iw-page{{else}}btn-iw-page-disabled{{/if}} btn-iw-page-next"><i class="icon-angle-right"></i></span>
				</div>
			</div>
			<div class="insertPoint">
				{{#if reports}}
					{{#each reports}}
						<div class="task normal hover-bg-grey">
							<a data-id="{{DT_RowData.id}}" class="btn-more title" id="more-{{DT_RowData.id}}" href="#{{DT_RowData.id}}" data-toggle="modal">
								<div class="desc">
									<div class="title">{{title}}</div>
									<div class="info">{{address}}</div>
								</div>
								<div class="time">
									<div class="date"><strong>{{comments}}</strong> <i class="icon-comments"></i></div>
									<div class="info">{{created_at}}</div>
								</div>
							</a>
						</div>
					{{/each}}
				{{else}}
					<div style="text-align:center; padding:10px;"><i class="icon-spinner icon-spin icon-5x"></i></div>
				{{/if}}
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="clearfix"></div>
	</div>
</script>
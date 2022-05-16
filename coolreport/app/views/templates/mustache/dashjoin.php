<script id="tpl-template-join" type="text/x-handlebars-template">
	<div class="form-group">
		<label for="template-select" class="control-label">Template <i style="display:none" class="icon-spinner icon-spin"></i></label>
		<div class="row">
			<div class="col-sm-12 controls">
				<select class="form-control">
					{{#each templates}}
						<option value="{{ id }}" {{#if_eq id compare=../prev_data.template}}selected{{/if_eq}}>{{ title }}</option>
					{{/each}}
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 controls">
				<div class="input-group input-group-sm">
					<span class="input-group-addon"><b>{{ trans('dashitems.condition') }}</b></span>
					<input class="form-control cr-join-condition" value="{{ prev_data.condition }}" placeholder={{ trans('dashitems.clicktoeditexpression') }}>
				</div>
			</div>
		</div>
	</div>
</script>
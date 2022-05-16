<?php
/* Create template
 * -----------------------------*/ ?>

<script id="tpl-create-template" type="text/x-handlebars-template">
	<div class="row field-row" data-type="{{ type }}">
		<div class="col-xs-2">
			<div class="form-group">
				<div class="controls">
					<div class="input-group input-group-sm {{#if field.required}}has-warning{{/if}}">
						<span class="input-group-btn">
							<button class="btn btn-info action-up-row"><i class="icon-arrow-up"></i></button>
							<button class="btn btn-info action-down-row"><i class="icon-arrow-down"></i></button>
							<button class="btn btn-danger action-remove-row"><i class="icon-remove"></i></button>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-10">
		{{#each columns}}
			<div class="col-xs-{{this}} col-sm-{{this}} col-md-{{this}}">
				<select class="form-control">
				</select>
			</div>
		{{/each}}
		</div>
	</div>
</script>
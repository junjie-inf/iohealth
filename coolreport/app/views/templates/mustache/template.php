<?php
/* Create template
 * -----------------------------*/ ?>

<script id="tpl-create-template" type="text/x-handlebars-template">
	<div class="row field-row" data-type="{{ type }}">
		{{!-- LEFT --}}
		<div class="col-xs-4 col-sm-4 col-md-4">
			<div class="form-group">
				<div class="controls">
					<div class="input-group input-group-sm {{#if field.required}}has-warning{{/if}}">
						<span class="input-group-addon"><i class="{{ icon }} bigger-120"></i></span>
						
						<input type="text" class="form-control field-name" placeholder="Title" id="{{ field.id }}" name="{{ field.id }}" required value="{{ field.label }}" />
						
						<span class="input-group-btn">
							<button class="btn btn-info action-up-field"><i class="icon-arrow-up"></i></button>
							<button class="btn btn-info action-down-field"><i class="icon-arrow-down"></i></button>
							<button class="btn btn-danger action-remove-field"><i class="icon-remove"></i></button>
						</span>
					</div>
					<div class="pull-left margin-top-5">
						<button type="button" class="btn btn-sm field-required {{#if field.required}}active{{/if}}" data-toggle="button" title="Mark this field as required"><i class="icon-asterisk"></i> Required</button>
						<button type="button" class="btn btn-sm field-anchor {{#if field.show}}active{{/if}}" data-toggle="button" title="Mark this field as title"><i class="icon-eye-open"></i> Show</button>
						{{#ifEq type "date"}}
							<button type="button" class="btn btn-sm btn-primary template-date-filter {{#if main_date}}active{{/if}}" data-toggle="button" title="Mark this field as date filter"><i class="icon-filter"></i> Date filter</button>
						{{/ifEq}}
					</div>
					{{#ifEq type "select"}}
						<div class="pull-right">
							<button type="button" class="btn btn-sm margin-top-5 field-multiple {{#if field.multiple}}active{{/if}}" data-toggle="button"><i class="icon-list"></i> Multiple answers</button>
						</div>
					{{/ifEq}}
				</div>
			</div>
		</div>
		
		{{!-- RIGHT --}}
		<div class="col-xs-8 col-sm-8 col-md-8">
			<div class="form-group">
			
				{{#ifEq type "report"}}
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Template</span>
								<select class="form-control field-template">
								{{#each templates }}
									<option value="{{ id }}" {{#if_eq ../field.template compare=id}}selected{{/if_eq}}> {{ title }} </option>
								{{/each}}
								</select>
							</div>
						</label>
					</div>
				{{/ifEq}}

				{{#if optionable}}
					{{#if field.options}}
						{{#field.options}}
							<div class="controls">
								<label class="inh_nopadding-top">
									<div class="input-group input-group-sm">
										<input type="text" class="form-control field-option" placeholder="Option title" required id="{{id}}" name="{{id}}" value="{{value}}">
										<span class="input-group-btn">
											<button class="btn btn-info up-option" type="button"><i class="icon-arrow-up"></i></button>
											<button class="btn btn-info down-option" type="button"><i class="icon-arrow-down"></i></button>
											<button class="btn btn-danger remove-option" type="button"><i class="icon-remove"></i></button>
										</span>
									</div>
								</label>
							</div>
						{{/field.options}}
					{{/if}}
					
					{{#unless field.options}}
						{{#times 2}}
						<div class="controls">
							<label class="inh_nopadding-top">
								<div class="input-group input-group-sm">
									<input type="text" class="form-control field-option" placeholder="Option title" required>
									<span class="input-group-btn">
										<button class="btn btn-info up-option" type="button"><i class="icon-arrow-up"></i></button>
										<button class="btn btn-info down-option" type="button"><i class="icon-arrow-down"></i></button>
										<button class="btn btn-danger remove-option" type="button"><i class="icon-remove"></i></button>
									</span>
								</div>
							</label>
						</div>
						{{/times}}
					{{/unless}}
					<button class="btn btn-success add-option pull-right" type="button"><i class="icon-plus"></i></button>
				{{/if}}

				{{#ifEq type "file"}}
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Max</span>
								<input type="number" class="form-control field-max" placeholder="Value" value="{{ field.max }}">
							</div>
						</label>
					</div>
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Accept</span>
								<select class="form-control field-accept">
									<option value selected>All files</option>
									<option value="image/*">Images</option>
									<option value="video/*">Videos</option>
									<option value="audio/*">Audios</option>
								</select>
							</div>
						</label>
					</div>
				{{/ifEq}}
				
				{{#ifEq type "number"}}
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Min</span>
								<input type="number" class="form-control field-min" placeholder="Value" value="{{ field.min }}">
							</div>
						</label>
					</div>
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Max</span>
								<input type="number" class="form-control field-max" placeholder="Value" value="{{ field.max }}">
							</div>
						</label>
					</div>
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Step</span>
								<input type="number" class="form-control field-step" placeholder="Value" value="{{ field.step }}">
							</div>
						</label>
					</div>
				{{/ifEq}}
						
				{{#ifEq type "decimal"}}
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Min</span>
								<input type="number" class="form-control field-min" placeholder="Value" value="{{ field.min }}">
							</div>
						</label>
					</div>
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Max</span>
								<input type="number" class="form-control field-max" placeholder="Value" value="{{ field.max }}">
							</div>
						</label>
					</div>
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Sensor</span>
								<select class="form-control field-sensor">
									<option value="">None / Manual</option>
									<option value="luminosity" {{#if_eq field.sensor compare="luminosity"}}selected{{/if_eq}}>Luminosity</option>
									<option value="temperature" {{#if_eq field.sensor compare="temperature"}}selected{{/if_eq}}>Temperature</option>
									<option value="pressure" {{#if_eq field.sensor compare="pressure"}}selected{{/if_eq}}>Pressure</option>
									<option value="compass" {{#if_eq field.sensor compare="compass"}}selected{{/if_eq}}>Compass</option>
									<option value="accel-x" {{#if_eq field.sensor compare="accel-x"}}selected{{/if_eq}}>Acceleration (X)</option>
									<option value="accel-y" {{#if_eq field.sensor compare="accel-y"}}selected{{/if_eq}}>Acceleration (Y)</option>
									<option value="accel-z" {{#if_eq field.sensor compare="accel-z"}}selected{{/if_eq}}>Acceleration (Z)</option>
									<option value="rot-x" {{#if_eq field.sensor compare="rot-x"}}selected{{/if_eq}}>Rotation (X)</option>
									<option value="rot-y" {{#if_eq field.sensor compare="rot-y"}}selected{{/if_eq}}>Rotation (Y)</option>
									<option value="rot-z" {{#if_eq field.sensor compare="rot-z"}}selected{{/if_eq}}>Rotation (Z)</option>
								</select>
							</div>
						</label>
					</div>
				{{/ifEq}}
				
				{{#ifEq type "range"}}
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Min</span>
								<input type="number" class="form-control field-min" placeholder="Value" value="0" required value="{{ field.min }}">
							</div>
						</label>
					</div>
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Max</span>
								<input type="number" class="form-control field-max" placeholder="Value" value="10" required value="{{ field.max }}">
							</div>
						</label>
					</div>
					<div class="controls">
						<label class="inh_nopadding-top">
							<div class="input-group input-group-sm">
								<span class="input-group-addon">Step</span>
								<input type="number" class="form-control field-step" placeholder="Value" value="{{ field.step }}">
							</div>
						</label>
					</div>
				{{/ifEq}}

			</div>
		</div>
		
	</div>
</script>
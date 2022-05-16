<?php
/* Create template
 * -----------------------------*/ ?>

<!-- Main chart options template -->
<script id="tpl-display-options" type="text/x-handlebars-template">
	{{#if_eq chartType compare="table"}}
		<p>No options available.</p>
	{{/if_eq}}

	
	{{#if_eq chartType compare="pie"}}	
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<div class="form-group">
				<label class="control-label">Labels</label>
				<div class="controls">
					<select id="pie-labels-select" class="form-control">
						{{#each allFields}}
							<option value="{{ selId }}">{{ alias }}</option>
						{{/each}}
					</select>
				</div>
			</div>
		</div>
		
		<div class="col-xs-6 col-sm-6 col-md-6">
			<div class="form-group">
				<label class="control-label">Values</label>
				<div class="controls">
					<select id="pie-values-select" class="form-control">
						{{#each numericFields}}
							<option value="{{ selId }}">{{ alias }}</option>
						{{/each}}
						{{#each expressionFields}}
							<option value="{{ selId }}">{{ alias }}</option>
						{{/each}}
					</select>
				</div>
			</div>
		</div>
	</div>
	{{/if_eq}}

	{{#if_eq chartType compare="line"}}
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<div class="form-group">
				<label class="control-label">X Axis</label>
				<div class="controls">
					<label class="control-label" for="line-xaxis-label">Label</label>
					<input id="line-xaxis-label" class="form-control" />
					<label class="control-label" for="line-xaxis-col">Data</label>
					<select id="line-xaxis-col" class="form-control">
						{{#each numericFields}}
							<option value="{{ selId }}" data-type="number">{{ alias }}</option>
						{{/each}}
						{{#each dateFields}}
							<option value="{{ selId }}" data-type="date">{{ alias }}</option>
						{{/each}}
						{{#each expressionFields}}
							<option value="{{ selId }}" data-type="expression">{{ alias }}</option>
						{{/each}}
					</select>
				</div>
			</div>
		</div>
		
		<div class="col-xs-6 col-sm-6 col-md-6">
			<div class="form-group">
				<label class="control-label">Y Axis</label>
				<div class="controls">
					<label class="control-label" for="line-yaxis-label">Label</label>
					<input id="line-yaxis-label" class="form-control" />
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<fieldset class="col-sm-12">
			<div class="form-group">
				<label class="control-label">Series</label>
			</div>

			<div class="well">
				<div class="line-series-container"></div>
				<div class="controls">
					<div class="btn-group">
						<button class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
							<i class="icon-plus"></i> Add series <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" id="db-add-line-series-dropdown">
							{{#each numericFields}}
								<li><a data-id="{{ selId }}" data-alias="{{ alias }}" class="action-add-line-series" href="javascript:void(0)">{{ alias }}</a></li>
							{{/each}}
							{{#each expressionFields}}
								<li><a data-id="{{ selId }}" data-alias="{{ alias }}" class="action-add-line-series" href="javascript:void(0)">{{ alias }}</a></li>
							{{/each}}
						</ul>
					</div>
				</div>
				
			</div>
		</fieldset>
	</div>
	{{/if_eq}}

	{{#if_eq chartType compare="bar"}}
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<div class="form-group">
				<label class="control-label">X Axis</label>
				<div class="controls">
					<label class="control-label" for="bar-xaxis-label">Label</label>
					<input id="bar-xaxis-label" class="form-control" />
					<label class="control-label" for="bar-xaxis-col">Data</label>
					<select id="bar-xaxis-col" class="form-control">
						{{#each allFields}}
							<option value="{{ selId }}" data-type="{{ datatype }}">{{ alias }}</option>
						{{/each}}
					</select>
				</div>
			</div>
		</div>
		
		<div class="col-xs-6 col-sm-6 col-md-6">
			<div class="form-group">
				<label class="control-label">Y Axis</label>
				<div class="controls">
					<label class="control-label" for="bar-yaxis-label">Label</label>
					<input id="bar-yaxis-label" class="form-control" />
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<fieldset class="col-sm-12">
			<div class="form-group">
				<label class="control-label">Series</label>
			</div>

			<div class="well">
				<div class="bar-series-container"></div>
				<div class="controls">
					<div class="btn-group">
						<button class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
							<i class="icon-plus"></i> Add series <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" id="db-add-bar-series-dropdown">
							{{#each numericFields}}
								<li><a data-id="{{ selId }}" data-alias="{{ alias }}" class="action-add-bar-series" href="javascript:void(0)">{{ alias }}</a></li>
							{{/each}}
							{{#each expressionFields}}
								<li><a data-id="{{ selId }}" data-alias="{{ alias }}" class="action-add-bar-series" href="javascript:void(0)">{{ alias }}</a></li>
							{{/each}}
						</ul>
					</div>
				</div>
				
			</div>
		</fieldset>
	</div>
	{{/if_eq}}

	{{#if_eq chartType compare="indicator"}}
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12">
			<div class="form-group">
				<label class="control-label">Labels</label>
				<div class="controls">
					<select id="indicator-labels-select" class="form-control">
						{{#each allFields}}
							<option value="{{ selId }}">{{ alias }}</option>
						{{/each}}
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<fieldset class="col-sm-12">
			<div class="form-group">
				<label class="control-label">Series</label>
			</div>

			<div class="well">
				<div class="indicator-series-container"></div>
				<div class="controls">
					<div class="btn-group">
						<button class="btn btn-large dropdown-toggle btn-primary" data-toggle="dropdown">
							<i class="icon-plus"></i> Add series <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" id="db-add-indicator-series-dropdown">
							{{#each numericFields}}
								<li><a data-id="{{ selId }}" data-alias="{{ alias }}" class="action-add-indicator-series" href="javascript:void(0)">{{ alias }}</a></li>
							{{/each}}
							{{#each expressionFields}}
								<li><a data-id="{{ selId }}" data-alias="{{ alias }}" class="action-add-indicator-series" href="javascript:void(0)">{{ alias }}</a></li>
							{{/each}}
						</ul>
					</div>
				</div>
			</div>
		</fieldset>
	</div>
	{{/if_eq}}

	{{#if_eq chartType compare="map"}}
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<div class="form-group">
			<label class="control-label" for="geolocationfrom-select">Geolocation from</label>
				<div class="controls">		
					<select id="geolocationfrom-select" class="form-control">
						{{#each selectedTemplates}}
							<option value="{{ id }}">{{ title }}</option>
						{{/each}}
					</select>
				</div>
			</div>
		</div>
		
	</div>
	{{/if_eq}}

</script>

<!-- Line series template -->
<script id="tpl-chart-line-series" type="text/x-handlebars-template">
	<div class="row field-row">
		<div class="input-group col-xs-4">
			<div class="input-group input-group-sm">
				<input type="text" 
					class="form-control field-name"
					placeholder="Title"
					value="{{ title }}"
					data-id="{{ id }}"
				/>
				
				<span class="input-group-btn">
					<button class="btn btn-info action-up-field"><i class="icon-arrow-up"></i></button>
					<button class="btn btn-info action-down-field"><i class="icon-arrow-down"></i></button>
					<button class="btn btn-danger action-remove-field"><i class="icon-remove"></i></button>
				</span>
			</div>
			<div class="pull-left margin-top-5">
				{{ title }}
			</div>
		</div>
		<div class="input-group color col-sm-4">
			<span class="input-group-addon"><i class="icon-tint"></i></span>
			<input type="text" value="{{ color }}" class="form-control color-picker">
		</div>
		<div class="input-group col-sm-4">
			<input type="checkbox" {{#if_eq area compare=true}}checked{{/if_eq}}> Show area
		</div>
	</div>
</script>

<!-- Bar series template -->
<script id="tpl-chart-bar-series" type="text/x-handlebars-template">
	<div class="row field-row">
		<div class="input-group col-xs-4">
			<div class="input-group input-group-sm">
				<input type="text"
					class="form-control field-name"
					placeholder="Title"
					value="{{ title }}"
					data-id="{{ id }}"
				/>
				
				<span class="input-group-btn">
					<button class="btn btn-info action-up-field"><i class="icon-arrow-up"></i></button>
					<button class="btn btn-info action-down-field"><i class="icon-arrow-down"></i></button>
					<button class="btn btn-danger action-remove-field"><i class="icon-remove"></i></button>
				</span>
			</div>
			<div class="pull-left margin-top-5">
				{{ title }}
			</div>
		</div>
		<div class="input-group color col-sm-4">
			<span class="input-group-addon"><i class="icon-tint"></i></span>
			<input type="text" value="{{ color }}" class="form-control color-picker">
		</div>
	</div>
</script>

<!-- Indicator series template -->
<script id="tpl-chart-indicator-series" type="text/x-handlebars-template">
	<div class="row field-row">
		<div class="input-group col-xs-4">
			<div class="input-group input-group-sm">
				<input type="text"
					class="form-control field-name"
					placeholder="Title"
					value="{{ title }}"
					data-id="{{ id }}"
				/>

				<span class="input-group-btn">
					<button class="btn btn-info action-up-field"><i class="icon-arrow-up"></i></button>
					<button class="btn btn-info action-down-field"><i class="icon-arrow-down"></i></button>
					<button class="btn btn-danger action-remove-field"><i class="icon-remove"></i></button>
				</span>
			</div>
			<div class="pull-left margin-top-5">
				{{ title }}
			</div>
			</div>
		</div>
	</div>
</script>

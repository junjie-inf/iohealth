<?php
/* Create template
 * -----------------------------*/ ?>

<script id="tpl-create-template" type="text/x-handlebars-template">
 <div class="row field-row" data-type="{{ type }}"
 	{{#if_eq column_type compare='aggregate'}}id={{ unique_id }}{{/if_eq}}
	{{#if_eq column_type compare='expression'}}id={{ unique_id }}{{/if_eq}}>

		{{!-- LEFT --}}
		{{#if_eq column_type compare="group"}}
			<div class="col-xs-6 col-sm-6 col-md-6">
		{{else}}
			{{#if_eq column_type compare="order"}}
				<div class="col-xs-8 col-sm-8 col-md-8">
			{{else}}
				<div class="col-xs-4 col-sm-4 col-md-4">
			{{/if_eq}}
		{{/if_eq}}
			<div class="form-group">
				<div class="controls">
					<div class="input-group input-group-sm {{#if field.required}}has-warning{{/if}}">
						<span class="input-group-addon input-group-addon-{{ column_type }}"><i class="{{ icon }} bigger-120"></i></span>
						<input type="text" 
							class="form-control field-name"
							placeholder="Title"
							id="{{ id }}"
							name="{{ id }}"
							data-type="{{ column_type }}"
							data-datatype="{{ type }}"
							data-template-id="{{ template.id }}"
							data-template-title="{{ template.title }}"
							required value="{{ title }}"

							{{!-- Disable order and group --}}
							{{#if_eq column_type compare="order"}}
								disabled="disabled"
							{{/if_eq}}
							{{#if_eq column_type compare="group"}}
								disabled="disabled"
							{{/if_eq}}
						/>
						
						<span class="input-group-btn">
							<button class="btn btn-info action-up-field"><i class="icon-arrow-up"></i></button>
							<button class="btn btn-info action-down-field"><i class="icon-arrow-down"></i></button>
							<button class="btn btn-danger action-remove-field"><i class="icon-remove"></i></button>
						</span>
					</div>
							{{!--{{#if_eq column_type compare="field"}}--}}
						<div class="pull-left margin-top-5">
							{{ original_title }}
						</div>
							{{!--{{/if_eq}}--}}
				</div>
			</div>
		</div>
		
		{{!-- RIGHT --}}		
		
		{{#if_eq column_type compare="group"}}
			<div class="col-xs-6 col-sm-6 col-md-6">
		{{else}}
			{{#if_eq column_type compare="order"}}
				<div class="col-xs-4 col-sm-4 col-md-4">
			{{else}}
				<div class="col-xs-8 col-sm-8 col-md-8">
			{{/if_eq}}
		{{/if_eq}}
			{{#if_eq type compare="date"}}
				{{!-- Desplegable de seleccion de tipo de fecha --}}
				<div class="form-group">
					<select class="form-control datetype-select">
						<option value="complete" {{#if_eq datetype compare="complete"}}selected{{/if_eq}}>Complete</option>
						<option value="hour" {{#if_eq datetype compare="hour"}}selected{{/if_eq}}>Hours</option>
						<option value="day" {{#if_eq datetype compare="day"}}selected{{/if_eq}}>Days</option>
						<option value="week" {{#if_eq datetype compare="week"}}selected{{/if_eq}}>Weeks</option>
						<option value="month" {{#if_eq datetype compare="month"}}selected{{/if_eq}}>Months</option>
						{{!-- <option value="quarter" {{#if_eq datetype compare="quarter"}}selected{{/if_eq}}>Quarters</option> --}}
						<option value="year" {{#if_eq datetype compare="year"}}selected{{/if_eq}}>Years</option>
					</select>
				</div>
			{{/if_eq}}
			{{#if_eq column_type compare="order"}}
				<div class="form-group">
					<select class="form-control direction-select">
						<option value="asc" {{#if_eq direction compare="asc"}}selected{{/if_eq}}>Ascending</option>
						<option value="desc" {{#if_eq direction compare="desc"}}selected{{/if_eq}}>Descending</option>
					</select>
				</div>
			{{/if_eq}}
			{{#if_eq column_type compare="aggregate"}}
				<div class="col-xs-6 col-sm-6 col-md-6">
					{{#unless_eq type compare="count"}}
						<div class="form-group">
							<select class="form-control">
							{{#each fields}}
								<option value="${{template.id}}.{{id}}" {{#if_eq id compare=../aggregate_column}}selected{{/if_eq}}>{{template.title}} → {{label}} {{aggregate_column}}</option>
							{{/each}}
							</select>
						</div>
					{{/unless_eq}}
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><i class="icon-filter"></i></div>
							<input type="text" class="form-control agg-filter" placeholder="Click to add filter" value="{{ filter }}">
						</div>
					</div>
				</div>
			{{/if_eq}}
			{{#if_eq column_type compare="expression"}}
				<div class="col-xs-12 col-sm-12 col-md-12">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><i class="icon-terminal"></i></div>
							<input type="text" class="form-control cr-expression-condition" placeholder="Click to add expression" value="{{ expression }}">
						</div>
					</div>
				</div>
			{{/if_eq}}
		</div>
		

	</div>
</script>

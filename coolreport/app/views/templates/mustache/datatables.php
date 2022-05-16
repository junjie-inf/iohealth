<?php
/* Template for dynamic report datatables
 * -----------------------------*/ ?>

<script id="tpl-table-crud-report" type="text/x-handlebars-template">
	<a class="btn btn-success" href="{{ url }}"><i class="icon-zoom-in"></i></a>
	
	{{#if editable}}
		<a class="btn btn-info" href="{{ url }}/edit"><i class="icon-edit"></i></a>
	{{else}}
		<a class="btn disabled" href="javascript:void(0)"><i class="icon-edit"></i></a>
	{{/if}}
	
	<a class="btn {{#if removable}}btn-danger action-delete{{else}}disabled{{/if}}" href="javascript:void(0)" data-loading-text="<i class='icon-spinner icon-spin'></i>"><i class="icon-trash"></i></a>
	
	
<!--	<a class="btn btn-danger action-unfollow" href="javascript:void(0)" {{#unless followed}}style="display:none"{{/unless}}
		data-followable-type="report"
		data-followable-id="{{ id }}"
		data-toggle="true"
		data-loading-text="<i class='icon-spinner icon-spin'></i>"
		title="Click to unfollow this">
			<i class="icon-eye-close"></i>
-->	
	</a>
<!--	<a class="btn btn-success action-follow" href="javascript:void(0)" {{#if followed}}style="display:none"{{/if}}
		data-followable-type="report"
		data-followable-id="{{ id }}"
		data-toggle="true"
		data-loading-text="<i class='icon-spinner icon-spin'></i>"
		title="Click to follow this">
			<i class="icon-eye-open"></i>
	</a>
-->
</script>
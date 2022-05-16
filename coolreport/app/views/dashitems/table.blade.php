<div id='chart{{ $chartId }}'>
	@if ( isset($options->dFilter) )
		<label class="control-label" for="filter{{ $chartId }}">{{trans('dashitems.table.filterby')}} <i class="icon-spinner icon-spin" style="display:none"></i></label>
	    <div class="controls">
			<fieldset class="col-sm-3">
    			<select id="filter{{ $chartId }}" >
					<option value="-1">{{trans('dashitems.table.all')}}</option>
					@foreach ( $dFilterValues as $v )
		            	<option value="{{$v}}">{{$v}}</option>
		        	@endforeach
				</select>
			</fieldset>	
			<br>
			<br>
		</div>
	@endif
	<table class="table table-striped table-bordered table-condensed bootstrap-datatable datatable vertical-middle">
	<thead>
		<tr>
			@foreach( $header as $h )
			<th>{{ $h }}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach( $data as $row )
		<tr>
			@foreach( $row as $d )
			<td>{{ $d }}</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
	</table>
</div>

<script>
crHooks.push(function()
{
    @if ( isset($options->dFilter) )
        var datum = {{json_encode($datum)}};
        var filterSelect = $('#filter{{ $chartId }}');
        
        filterSelect.on('change', function() {
            var filterVal = filterSelect.val();
            crHooks = [];
            
            if (filterVal != -1)
           	{
            	if (changed)
            	{
            		datum.data.filter = datum.data.filter.substring(0, datum.data.filter.lastIndexOf("LIKE")) + "LIKE '" + filterVal + "'";	
            	}
            	else
            	{	
            		changed = true;
                	datum.data.filter += "AND ${{ $options->dFilter->template }}.{{ $options->dFilter->field }} LIKE '" + filterVal + "'";
            	}
            }
            else
            {
              	datum.data.filter = datum.data.filter.substring(0, datum.data.filter.lastIndexOf("LIKE")) + "LIKE '%'";
            }

            $.post($SITE_PATH + '/dashitem/preview', {'d': JSON.stringify(datum)}, function(d){
                var newHtml = $(d);
                $('#chart{{ $chartId }}').replaceWith(newHtml);
                for (var i = 0; i < crHooks.length; i++)
                {
                    crHooks[i]();
                }
                newHtml.find('select').val(filterVal);
            });
        });
    @endif
});
</script>
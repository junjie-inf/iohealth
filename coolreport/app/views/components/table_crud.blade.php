<td class="inh_nowrap">
	<a class="btn btn-success" href="{{ $url }}"><i class="icon-zoom-in"></i></a>
	
	@if( $obj->editable )
		<a class="btn btn-info" href="{{ $url }}/edit"><i class="icon-edit"></i></a>
	@else
		<a class="btn disabled" href="javascript:void(0)"><i class="icon-edit"></i></a>
	@endif
	
	<a class="btn {{ $obj->removable ? 'btn-danger action-delete' : 'disabled' }}" href="javascript:void(0)" data-loading-text="<i class='icon-spinner icon-spin'></i>"><i class="icon-trash"></i></a>
	
	@if( class_has_trait($obj, 'Followable') )
		@if( get_class($obj) == 'User' && $obj->id == Auth::user()->getKey() )
			<a class="btn disabled" href="javascript:void(0)">
				<i class="icon-eye-open"></i>
			</a>
		@else
			<a class="btn btn-danger action-unfollow" href="javascript:void(0)" style="{{ $obj->getUsersFromFollowers()->contains(Auth::user()->getKey()) ? '' : 'display:none' }}"
				data-followable-type="{{ lcfirst(get_class($obj)) }}"
				data-followable-id="{{ $obj->id }}"
				data-toggle="true"
				data-loading-text="<i class='icon-spinner icon-spin'></i>"
				title="Click to unfollow this">
					<i class="icon-eye-close"></i>
			</a>
		@endif
	@endif
</td>
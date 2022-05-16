{%
/**
 * Template arguments:
 *  @param Report $obj
 */
%}

@assert('obj')

<a href="javascript: window.open('https://maps.google.com/maps?q={{ $obj->latitude }},{{ $obj->longitude }}&z=17&t=m&output=embed', this.target, 'width=680, height=680'); return false;" class="pull-left"
	data-rel="tooltip"
	data-placement="top"
	data-html="true"
	title="Latitude: {{ $obj->latitude }}, <br />Longitude: {{ $obj->longitude }}"
	>
	<i class="icon-search"></i>
</a>&nbsp; @if( $obj->city ){{ $obj->city }}.@endif @if( $obj->address ){{ $obj->address }}.@endif

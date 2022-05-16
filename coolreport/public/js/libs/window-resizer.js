var WindowResizer = {
	
	canvas: null,
	
	navbar: null,
	
	init: function( $map_canvas, $navbar ) {
		this.canvas = $map_canvas;
		this.navbar = $navbar;
		
		this.windowResizing();
		
		// Set canvas height on windows resize
		$(window).resize(function(e) {
			e.preventDefault();
			WindowResizer.windowResizing();
		});
	},
	
	windowResizing: function() {
		
		this.canvas.height(
			$(window).height() - ( this.navbar.height() + 1 )
		);
		
	}
	
};
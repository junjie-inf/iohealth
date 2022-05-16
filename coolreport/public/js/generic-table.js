$(document).ready(function(){
	
	/* ---------- Datable ---------- */
	
	/**
	 * Returns the "aoColumns" array for dataTable. The HTML markup is:
	 * 
	 * @param {jQuery} $table
	 * @returns {Array}
	 */
	function fillAoColumns( $table ){
		var aoColumns = [];
		$table.find('thead tr th').each(function() {
			if ($(this).hasClass('no_sort') ) {
				aoColumns.push({"bSortable": false});
			} else {
				aoColumns.push(null);
			}
		});
		
		return aoColumns;
	}
	
	/**
	 * Returns the "aaSorting" array for dataTable. The HTML markup is:
	 * 
	 * <th class="default_sort" data-sort-dir="{asc|desc}"></th>
	 * 
	 * With this markup, we can define the column for default sorting, and its direction.
	 * 
	 * @param {jQuery} $table
	 * @param {string} tableClass
	 * @returns {Array} Example: [Array[3, "desc"]]
	 */
	function dt_getDefaultSorting( $table, tableClass ){
		var $th_default_sort = $table.find('thead tr th.default_sort');
		
		//If no sort order is specified, do not sort
		if ($th_default_sort.index() == -1)
			return [];
		
		//return [[ $th_default_sort.index( tableClass + ' thead th' ), $th_default_sort.data('sort-dir') ]];
		return [[ $th_default_sort.index(), $th_default_sort.data('sort-dir') ]];
	}
	
	/**
	 * Init datatables
	 */
	$('.datatable').each(function()
	{
		var $table = $(this);
		
		$table.dataTable({
			"sDom": "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-12'i><'col-lg-12 center'p>>",
			"sPaginationType": "bootstrap",
			"oLanguage": {
				"sLengthMenu": "_MENU_ registros por página"
			},
			"aoColumns": fillAoColumns($table),
			"aaSorting": dt_getDefaultSorting($table, '.datatable') //http://www.joeyconway.com/blog/2013/06/15/datatables-default-sorting-by-class-name/
		});
	});
	
});
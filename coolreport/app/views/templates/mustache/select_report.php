<script id="tpl-select-report-modal" type="text/x-handlebars-template">
	<div class="modal fade" style="background-color: transparent;" id="select-report-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Select report</h4>
				</div>
				<div class="modal-body">
					<table id="selectReportTable" class="table table-striped table-bordered table-condensed bootstrap-datatable vertical-middle">
					<thead>
						<tr>
							<th>Title</th>
							<th class="width-min default_sort" data-sort-dir="desc">Created at</th>
							<th class="width-50 text-center"><i class="icon-comment-alt"></i></th>
							<th class="width-min no_sort"></th>
						</tr>
					</thead>

					<tbody>
					</tbody>
					</table>
				</div>
			</div>
		</div>
			<script>
			$('#selectReportTable').dataTable({
				serverSide: true,
				ajax: $SITE_PATH + '/report/table?mode=all&template={{template}}',
				dom: "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-12'i><'col-lg-12 center'p>>",
				columns: [
					{ 
						name: 'id', 
						data: function( row ) { 
							return row.title; 
						}
					},
					{
						name: 'created_at',
						className: 'inh_nowrap',
						data: 'created_at'
					},
					{
						name: 'comments',
						orderable: false,
						className: 'text-center',
						data: function( row ) {
							return '<a href="">'.replace('_id', row.DT_RowData.id) +
									'<span class="badge badge-success">' + row.comments + '</span>' +
								'</a>';
						}
					},
					{
						name: 'crud',
						className: 'inh_nowrap',
						orderable: false,
						data: function( row, type, set, meta ) {
							return '<a data-row="' + meta.row + '" href="#" class="btn btn-primary btn-report-selected"><i class="icon-link"></i></a>';
						}
					},
				]
			});
		<{{!}}/script>
	</div>
</script>
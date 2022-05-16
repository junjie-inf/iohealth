<script id="tpl-expreditor" type="text/x-handlebars-template">
	<div id="cr-expreditor-modal" class="modal fade" style="background-color: transparent;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title" id="myModalLabel">Expression editor</h2>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-6">
							<input class="form-control cr-expreditor-search" placeholder="Search...">
							<div class="cr-expreditor-tree">
							</div>
						</div>
						<div class="col-lg-6">
							<textarea style="height: 200px" class="form-control cr-expreditor-expression">{{ expr }}</textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn">Close</button>
					<button type="button" class="btn btn-primary cr-expreditor-save">Save changes</button>
				</div>
			</div>
		</div>
	</div>
</script>
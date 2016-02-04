<?php if (@!$gbSkipHeaderFooter) { ?>
				<!-- Begin alert dialog box -->
								<div id="ewMsgBox" class="modal fade" tabindex="-1">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header no-padding">
												<div class="table-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
														<span class="white">&times;</span>
													</button>
													Systems Alert!
												</div>
											</div>
											<div class="modal-body">
												<div id="ewTooltip"></div>
											</div>
											<div class="modal-footer no-margin-top">
												<button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
													<i class="icon-remove"></i>
													Close
												</button>
											</div>
										</div><!-- /.modal-content -->
									</div>
									<!-- / end .modal-dialog -->
								</div><!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- / end table .row -->
					</div><!-- /.page-content -->
				</div><!-- /.main-content -->
				<div class="ace-settings-container" id="ace-settings-container">
					<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
						<i class="icon-cog bigger-150"></i>
					</div>
					<div class="ace-settings-box" id="ace-settings-box">
						<div>
							<div class="pull-left">
								<select id="skin-colorpicker" class="hide">
									<option data-skin="default" value="#438EB9">#438EB9</option>
									<option data-skin="skin-1" value="#222A2D">#222A2D</option>
									<option data-skin="skin-2" value="#C6487E">#C6487E</option>
									<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
								</select>
							</div>
							<span>&nbsp; Choose Skin</span>
						</div>
						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar">
							<label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
						</div>
						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar">
							<label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
						</div>
						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs">
							<label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
						</div>
						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl">
							<label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
						</div>
						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container">
							<label class="lbl" for="ace-settings-add-container">
								Inside
								<b>.container</b>
							</label>
						</div>
					</div>
				</div><!-- /#ace-settings-container -->
			</div><!-- /.main-container-inner -->
			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="icon-double-angle-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>
		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="assets/js/bootstrap.min.js"></script>
		<!-- ace scripts -->
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<!-- page specific plugin scripts -->

		<script src="assets/js/jquery.gritter.min.js"></script> <?php // custom tooltip ?>

		<script src="assets/js/jquery.dataTables.min.js"></script>
		<script src="assets/js/jquery.dataTables.bootstrap.js"></script>
		<script type="text/javascript">
			jQuery(function($) {
				<?php if (ew_CurrentPage() == 'tbl_pensionerlist.php' || ew_CurrentPage() == 'register.php') { ?>
					$('[data-rel=tooltip-ace]').tooltip();
				<?php } else { ?>
					var oTable1 = $('#<?php echo "tbl_".strtolower(str_replace(".php","",ew_CurrentPage()));?>').dataTable( {
					"aoColumns": [
					  { "bSortable": false },
					  null, null,null, null, null,
					  { "bSortable": false }
					] } );
					$('table th input:checkbox').on('click' , function(){
						var that = this;
						$(this).closest('table').find('tr > td:first-child input:checkbox')
						.each(function(){
							this.checked = that.checked;
							$(this).closest('tr').toggleClass('selected');
						});
					});
					$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

					function tooltip_placement(context, source) {
						var $source = $(source);
						var $parent = $source.closest('table')
						var off1 = $parent.offset();
						var w1 = $parent.width();
						var off2 = $source.offset();
						var w2 = $source.width();
						if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
						return 'left';
					}
				<?php } ?>
			})
		</script>
<?php if (!ew_IsMobile()) { ?>
	<!-- footer (end) -->
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile()) { ?>
<script type="text/javascript">
$("#ewPageTitle").html($("#ewPageCaption").text());
</script>
<?php } ?>
<?php if (@$_GET["_row"] <> "") { ?>
<script type="text/javascript">
jQuery.later(1000, null, function() {
	jQuery("#<?php echo $_GET["_row"] ?>").each(function() { this.scrollIntoView(); }
});
</script>
<?php } ?>
<script type="text/javascript">

// Write your global startup script here
// document.write("page loaded");

</script>
</body>
</html>

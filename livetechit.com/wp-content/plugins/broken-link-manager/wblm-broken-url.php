<?php
global $WblmListTable;
$s  = isset($_REQUEST['s']) ? $_REQUEST['s'] : null;
$WblmListTable->prepare_items($s);
?>
<div id="wrapper" class="wblm_tables">
	<!-- Navigation -->
	<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?php echo admin_url('admin.php?page=wblm-dashboard'); ?>">
			<?php echo WBLM_NAME; ?> <span> Ver <?php echo WBLM_VERSION; ?></span>
			</a>
		</div>
	</nav>
	<div id="page-wrapper">
		<div class="row">
			<div class="col-lg-12">
				<h3 class="page-header"><?php _e('Broken URLs', 'wblm') ?></h3>
				<?php get_wblmTopNavi(); ?>
			</div>
			<!-- /.col-lg-12 -->
		</div>            
		<!-- /.row -->
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
					<div class="left"><?php _e('Broken URLs', 'wblm') ?></div>
					<div class="right">
						<form method="get">
					    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
					    <?php $WblmListTable->search_box( 'search', 'search_id' ); ?>
						</form>
					</div>
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<div class="table-responsive">
						<form action="<?php echo admin_url('admin.php?page=wblm-broken'); ?>" id="wpse-list-table-form" method="post">
						<?php $WblmListTable->display(); ?> 
						</form>
						</div>
						<!-- /.table-responsive -->
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
			</div>
			<!-- /.col-lg-12 -->
		</div>            
	</div>
	<!-- /#page-wrapper -->
	<?php get_wblmFooter(); ?>
</div>
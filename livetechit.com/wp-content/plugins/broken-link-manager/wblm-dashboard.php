<?php
global $wpdb;
$tolalUrlsCount = $wpdb->get_var("SELECT COUNT(id) FROM " . TABLE_WBLM  );
$redirectUrlsCount = $wpdb->get_var("SELECT COUNT(id) FROM " . TABLE_WBLM . " where `active` = '1'" );
$brokenlinkCount = $wpdb->get_var("SELECT COUNT(id) FROM " . TABLE_WBLM . " where `active` = '0'" );
$referUrls = $wpdb->get_var("SELECT COUNT(referer) FROM " . TABLE_WBLM_LOG );
?>
<div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="<?php echo admin_url('admin.php?page=wblm-dashboard'); ?>">
                <?php echo WBLM_NAME; ?> <span> Ver <?php echo WBLM_VERSION; ?></span>
                </a>
            </div>
            <!-- /.navbar-header -->
        </nav>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                <h1 class="page-header"><?php _e('Dashboard', 'wblm') ?></h1>
                <?php get_wblmTopNavi(); ?>
                </div>
            </div>            
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-list fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $tolalUrlsCount; ?></div>
                                    <div><?php _e('Total Broken URLs!', 'wblm') ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo admin_url('admin.php?page=wblm-broken'); ?>">
                            <div class="panel-footer">
                                <span class="pull-left"><?php _e('View Details', 'wblm') ?></span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-link fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $redirectUrlsCount; ?></div>
                                    <div><?php _e('Redirected (301) URLS!', 'wblm') ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo admin_url('admin.php?page=wblm-redirect'); ?>">
                            <div class="panel-footer">
                                <span class="pull-left"><?php _e('View Details', 'wblm') ?></span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-unlink fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $brokenlinkCount; ?></div>
                                    <div><?php _e('Broken (404) URLs!', 'wblm') ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo admin_url('admin.php?page=wblm-broken'); ?>">
                            <div class="panel-footer">
                                <span class="pull-left"><?php _e('View Details', 'wblm') ?></span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-line-chart fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $referUrls; ?></div>
                                    <div><?php _e('Total Broken URLs Hits!', 'wblm') ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo admin_url('admin.php?page=wblm-log'); ?>">
                            <div class="panel-footer">
                                <span class="pull-left"><?php _e('View Details', 'wblm') ?></span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> <?php _e('Impressions', 'wblm') ?>
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                         <?php _e('Period', 'wblm') ?>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="<?php echo admin_url('admin.php?page=wblm-dashboard'); ?>"> <?php _e('Yearly', 'wblm') ?> </a>
                                        </li>
                                        <li><a href="<?php echo admin_url('admin.php?page=wblm-dashboard'); ?>"> <?php _e('Monthly', 'wblm') ?> </a>
                                        </li>
                                        <li><a href="<?php echo admin_url('admin.php?page=wblm-dashboard'); ?>"> <?php _e('Daily', 'wblm') ?> </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li><a href="<?php echo admin_url('admin.php?page=wblm-dashboard'); ?>"> <?php _e('Default (Last 15 days)', 'wblm') ?> </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="morris-area-chart"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-8 -->
                <div class="col-lg-4">
                
                <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> <?php _e('Broken URLs', 'wblm') ?>
                        </div>
                        <div class="panel-body">
                            <div id="morris-donut-chart"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                </div>
                <!-- /.col-lg-4 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
        <?php get_wblmFooter(); ?>
</div>
<!-- /#wrapper -->
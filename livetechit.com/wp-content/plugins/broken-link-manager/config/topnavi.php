<ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li>
                            <a href="<?php echo admin_url('admin.php?page=wblm-dashboard'); ?>">
                            <div><i class="fa fa-area-chart fa-fw"></i> Dashboard</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo admin_url('admin.php?page=wblm-redirect'); ?>">
                                <div><i class="fa fa-link  fa-fw"></i> Redirect URLs</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo admin_url('admin.php?page=wblm-broken'); ?>">
                                <div><i class="fa fa-unlink fa-fw"></i> Broken URLs</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo admin_url('admin.php?page=wblm-add-url'); ?>">
                                <div><i class="fa fa-plus fa-fw"></i> Add URLs</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo admin_url('admin.php?page=wblm-log'); ?>">
                                <div><i class="fa fa-list-alt fa-fw"></i> Log</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="<?php echo admin_url('admin.php?page=wblm-settings'); ?>">
                            <i class="fa fa-cogs "></i>
                                <strong>Settings</strong>
                                
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-alerts -->
                </li>
            </ul>
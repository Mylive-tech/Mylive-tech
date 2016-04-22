<?php
global  $wp;

    $_GET['outlet'] = $wp->query_vars['outlet'];
    $_GET['reg']    = $wp->query_vars['reg'];
    setcookie ("wc_point_of_sale_register", $wp->query_vars['reg'] ,time()+3600*24*120, '/');

    $woocommerce_pos_company_logo = get_option('woocommerce_pos_register_layout_admin_bar', 'no');
    if ($woocommerce_pos_company_logo == 'yes'){
        add_filter('show_admin_bar', '__return_false');
    }

include_once( 'header.php' ); 

    WC_POS()->register()->display_register_detail($wp->query_vars['reg']);

include_once( 'footer.php' ); 
?>
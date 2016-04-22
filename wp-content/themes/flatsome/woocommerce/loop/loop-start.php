<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

global $flatsome_opt, $woocommerce_loop;
if(is_cart()){$woocommerce_loop['columns'] = 4;} 
if($woocommerce_loop['columns'] == ""){ $woocommerce_loop['columns'] = $flatsome_opt['category_row_count'];}

$mobile_columns = '2';
if(isset($flatsome_opt['category_row_count_mobile'])){ $mobile_columns = $flatsome_opt['category_row_count_mobile']; }

?>
<div class="row">
	<div class="large-12 columns">
<?php if(!empty($woocommerce_loop)){ ?>
	<ul class="products small-block-grid-<?php echo $mobile_columns; ?> large-block-grid-<?php echo $woocommerce_loop["columns"]; ?>">
<?php } else if (isset( $flatsome_opt['category_row_count'])) { ?>
	<ul class="products small-block-grid-<?php echo $mobile_columns; ?> large-block-grid-<?php echo $flatsome_opt['category_row_count']; ?>">
<?php } else { ?>
	<ul class="products small-block-grid-<?php echo $mobile_columns; ?> large-block-grid-4">
<?php } ?>

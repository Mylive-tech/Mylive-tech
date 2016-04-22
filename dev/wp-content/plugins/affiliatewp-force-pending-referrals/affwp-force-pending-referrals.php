<?php
/**
 * Plugin Name: AffiliateWP - Force Pending Referrals
 * Plugin URI: http://affiliatewp.com
 * Description: Force each referral to have a status of "pending"
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.0
 * License: GPL-2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

add_filter( 'affwp_auto_complete_referral', '__return_false' );
<?php
   header('Content-type: text/javascript');

   $home_dir = preg_replace('^wp-content/plugins/[a-z0-9\-/]+^', '', getcwd());
   include($home_dir . 'wp-load.php');
   global $wpdb;
   
echo"
(function($) { 
$(function() {
	Morris.Line({ 
	element: 'morris-area-chart',
    	data: ["; 
        

$logstats = $wpdb->get_results("SELECT DATE_FORMAT(`date` , '%Y-%m-%d') as date, COUNT(id) as `impressions`, SUM(redirect) as `redirect`, SUM(broken) as `broken` FROM `".TABLE_WBLM_LOG."` GROUP BY DATE_FORMAT(`date` , '%Y-%m-%d') DESC LIMIT 15");

if ($logstats) {
   foreach ($logstats as $log) {   
   echo "{
            period: '$log->date',
            total: $log->impressions,
            redirect: $log->redirect,
            broken: $log->broken
        },"; 
   
   }
}

echo "], \n
        xkey: 'period',
        ykeys: ['total', 'redirect', 'broken'],
        labels: ['Total', '301', '404'],
        pointSize: 2,
        hideHover: 'auto',
        resize: true,
        lineColors: ['#428bca','#5cb85c','#d9534f']
    }); \n \n";

$redirectUrlsCount = $wpdb->get_var("SELECT COUNT(id) FROM " . TABLE_WBLM . " where `active` = '1'" );
$brokenlinkCount = $wpdb->get_var("SELECT COUNT(id) FROM " . TABLE_WBLM . " where `active` = '0'" );

echo "Morris.Donut({
        element: 'morris-donut-chart',
        data: [{
            label: 'Broken (404)',
            value: $brokenlinkCount
        }, {
            label: 'Redirected (301)',
            value: $redirectUrlsCount
        }],
        resize: true
    });

});

})(jQuery);";
   
   
   
   
   
   
?>
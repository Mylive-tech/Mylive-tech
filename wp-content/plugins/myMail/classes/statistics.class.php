<?php if (!defined('ABSPATH')) die('not allowed');


class mymail_statistics {

	private $calendar_table = NULL;

	public function __construct() {


	}

	public function get_dashboard($range = '7 days'){

		$rawdata = $this->get_signups(strtotime('-'.$range), time());

		return array(
			'labels' => $this->get_labels($rawdata),
			'datasets' => $this->get_datasets($rawdata),
		);

	}

	private function get_labels($rawdata){

		global $wp_locale;

		$dates = array_keys($rawdata);

		foreach ($dates as $i => $date) {
			$d = strtotime($date);
			$dates[$i] = $wp_locale->weekday_abbrev[$wp_locale->weekday[date('w', $d)]];
		}

		return $dates;

	}

	private function get_datasets($rawdata){

		return array(
				array(
					'data' => array_values($rawdata),
					'fillColor' => "rgba(111,191,77,0.2)",
					'strokeColor' => "rgba(111,191,77,1)",
					'pointColor' => "rgba(111,191,77,1)",
					'pointStrokeColor' => "#fff",
					'pointHighlightFill' => "#fff",
					'pointHighlightStroke' => "rgba(111,191,77,1)",
				)
			);

	}

	public function get_signups($from = NULL, $to = NULL){

		global $wpdb;

		$from = is_null($from) ? time() : $from;
		$to = is_null($to) ? time()+86399 : $to;

 		$dates = $this->get_calendar_table($from, $to);
 		$count = count($dates);

		$count_before = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}mymail_subscribers WHERE status = 1 AND IF(confirm, confirm, signup) < %d", $from));

		$sql = "SELECT @n:=@n + IFNULL(total,0) total FROM (SELECT {$this->calendar_table}.date, total FROM {$this->calendar_table} LEFT JOIN (SELECT FROM_UNIXTIME(IF(confirm, confirm, signup), '%Y-%m-%d') date, count(*) total FROM {$wpdb->prefix}mymail_subscribers WHERE status = 1 GROUP BY date ) t2 ON {$this->calendar_table}.date= t2.date ORDER BY date) t3 CROSS JOIN (SELECT @n:=$count_before) n LIMIT 0,$count";

		$data = array_combine($dates, array_map('floatval', $wpdb->get_col($sql)));

		return $data;

	}



	private function get_calendar_table($from, $to, $format = 'Y-m-d') {

		global $wpdb;
 		$dates = $this->get_date_range($from, $to, '+1 day', $format);
 		$count = count($dates);

 		if(!$this->calendar_table){
 			$this->calendar_table = "{$wpdb->prefix}mymail_".uniqid();
	 		$sql = "CREATE TEMPORARY TABLE {$this->calendar_table} ( date date );";
			if(false == $wpdb->query($sql)) return false;
 		}else{
	 		$sql = "TRUNCATE {$this->calendar_table};";
			if(false == $wpdb->query($sql)) return false;
 		}

		$sql = "INSERT INTO {$this->calendar_table} (date) VALUES ('".implode("'),('", $dates)."');";

		if(false !== $wpdb->query($sql)) return $dates;

		return false;

	}


	private function get_date_range($first, $last, $step = '+1 day', $format = 'Y-m-d' ) {

		$dates = array();
		$current = $first;

		while( $current <= $last ) {

			$dates[] = date($format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	}


}

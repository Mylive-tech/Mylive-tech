<?php
class wcx_plugin_reports{
	public $stored;
	public $stored_cus;
	public $stored_p;
	public $stored_view;
	public $stored_c;
	public $stored_bc;
	public $stored_bs;
	public $stored_pm;
	public $stored_os;
	public $stored_cd;
	public $stored_coupon;
	
	function __construct(){
		global $wpdb;
		
		// THE COUNTRY NAMES ARRAY
		$countrycodes = array (
			'AF' => 'Afghanistan', 'AX' => '?land Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Zaire', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'C𴥠D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island and Mcdonald Islands', 'VA' => 'Vatican City State', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'KENYA', 'KI' => 'Kiribati', 'KP' => 'Korea, Democratic People\'s Republic of', 'KR' => 'Korea, Republic of', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia, the Former Yugoslav Republic of', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States of', 'MD' => 'Moldova, Republic of', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'R궮ion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'PM' => 'Saint Pierre and Miquelon', 'VC' => 'Saint Vincent and the Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome and Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia and the South Sandwich Islands', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard and Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan, Province of China', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania, United Republic of', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks and Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Minor Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis and Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe'
		);
		
		// FETCHING ORDERS DATA FROM DATABASE
		$query = "
			SELECT  
				YEAR(posts.post_date) AS year, 
				MONTH(posts.post_date) AS month,
				DAY(posts.post_date) AS day,
				posts.post_type as type,
				meta.meta_key as mkey, 
				SUM(meta.meta_value) AS total, 
				1 AS projtotal, 
				COUNT(posts.ID) AS cnt 
				FROM  {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta 
					ON posts.ID = meta.post_id 
			WHERE 
				posts.post_status IN ( 'wc-completed','wc-processing','wc-on-hold','wc-pending' ) 
				AND meta.meta_key IN ( '_cart_discount' ,'_order_total' ,'_order_tax' ,'_order_shipping_tax' ,'_order_shipping' ,'_customer_user', '_refund_amount' ) 
			GROUP BY 
				YEAR(posts.post_date), 
				MONTH(posts.post_date) ,
				DAY(posts.post_date) ,
				posts.post_type,
				meta.meta_key 
			ORDER BY
				YEAR(posts.post_date), 
				MONTH(posts.post_date), 
				DAY(posts.post_date)
				ASC
		";
		$data = $wpdb->get_results(  $query,ARRAY_A);
		$data = json_decode(json_encode($data), FALSE);
		
		// GET PROJECTED SALES OPTION
		$prjopt = get_option( 'wcx_reporting_options_prj' );
		
		// STORING DATA IN A PLEASENT STRUCTURE
		$this -> stored = Array();
		$this -> stored_cus = Array();
		$this -> stored_cus['total'] = 0;
		$this -> stored['total'] = Array(
			"proj" => Array("total" => 0,"cnt" => 0),
			"actual" => Array("total" => 0,"cnt" => 0),
			"refund" => Array("total" => 0,"cnt" => 0),
			"discount" => Array("total" => 0,"cnt" => 0),
			"tax" => Array("total" => 0,"cnt" => 0),
			"shiptax" => Array("total" => 0,"cnt" => 0),
			"ship" => Array("total" => 0,"cnt" => 0)
		);
		foreach ($data as $key => $val) {
			$arrval = object_to_array($val);
			if(!isset( $this -> stored[$arrval['year']])){
				$this -> stored[$arrval['year']] = Array();
				$this -> stored[$arrval['year']]['total'] = Array(
					"proj" => Array("total" => 0,"cnt" => 0),
					"actual" => Array("total" => 0,"cnt" => 0),
					"refund" => Array("total" => 0,"cnt" => 0),
					"discount" => Array("total" => 0,"cnt" => 0),
					"tax" => Array("total" => 0,"cnt" => 0),
					"shiptax" => Array("total" => 0,"cnt" => 0),
					"ship" => Array("total" => 0,"cnt" => 0)
				);
				
			}
			if(!isset( $this -> stored[$arrval['year']][$arrval['month']])){
				$this -> stored[$arrval['year']][$arrval['month']] = Array();
				$this -> stored[$arrval['year']][$arrval['month']]['total'] = Array(
					"proj" => Array("total" => 0,"cnt" => 0),
					"actual" => Array("total" => 0,"cnt" => 0),
					"refund" => Array("total" => 0,"cnt" => 0),
					"discount" => Array("total" => 0,"cnt" => 0),
					"tax" => Array("total" => 0,"cnt" => 0),
					"shiptax" => Array("total" => 0,"cnt" => 0),
					"ship" => Array("total" => 0,"cnt" => 0)
				);
			}
			if(!isset( $this -> stored[$arrval['year']][$arrval['month']][$arrval['day']])) 
				$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']] = Array(
					"proj" => Array("total" => 0,"cnt" => 0),
					"actual" => Array("total" => 0,"cnt" => 0),
					"refund" => Array("total" => 0,"cnt" => 0),
					"discount" => Array("total" => 0,"cnt" => 0),
					"tax" => Array("total" => 0,"cnt" => 0),
					"year" => $arrval['year'],
					"month" => $arrval['month'],
					"shiptax" => Array("total" => 0,"cnt" => 0),
					"ship" => Array("total" => 0,"cnt" => 0)
				);
			if($arrval['type'] == "shop_order"){
				switch ($arrval['mkey']) {
					case "_customer_user":
						$this -> stored_cus[$arrval['total']] = (isset($this -> stored_cus[$arrval['total']]))?$this -> stored_cus[$arrval['total']]+1:1;
						$this -> stored_cus['total']++;
						break;
					case "_cart_discount":
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["discount"]["total"] = $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["discount"]["cnt"] = $arrval['cnt'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["discount"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["discount"]["cnt"] += $arrval['cnt'];
						$this -> stored[$arrval['year']]["total"]["discount"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']]["total"]["discount"]["cnt"] += $arrval['cnt'];
						$this -> stored["total"]["discount"]["total"] += $arrval['total'];
						$this -> stored["total"]["discount"]["cnt"] += $arrval['cnt'];
						break;
					case "_order_total":
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["actual"]["total"] = $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["actual"]["cnt"] = $arrval['cnt'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["actual"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["actual"]["cnt"] += $arrval['cnt'];
						$this -> stored[$arrval['year']]["total"]["actual"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']]["total"]["actual"]["cnt"] += $arrval['cnt'];
						$this -> stored["total"]["actual"]["total"] += $arrval['total'];
						$this -> stored["total"]["actual"]["cnt"] += $arrval['cnt'];
						break;
					case "_order_tax":
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["tax"]["total"] = $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["tax"]["cnt"] = $arrval['cnt'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["tax"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["tax"]["cnt"] += $arrval['cnt'];
						$this -> stored[$arrval['year']]["total"]["tax"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']]["total"]["tax"]["cnt"] += $arrval['cnt'];
						$this -> stored["total"]["tax"]["total"] += $arrval['total'];
						$this -> stored["total"]["tax"]["cnt"] += $arrval['cnt'];
						break;
					case "_order_shipping_tax":
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["shiptax"]["total"] = $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["shiptax"]["cnt"] = $arrval['cnt'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["shiptax"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["shiptax"]["cnt"] += $arrval['cnt'];
						$this -> stored[$arrval['year']]["total"]["shiptax"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']]["total"]["shiptax"]["cnt"] += $arrval['cnt'];
						$this -> stored["total"]["shiptax"]["total"] += $arrval['total'];
						$this -> stored["total"]["shiptax"]["cnt"] += $arrval['cnt'];
						break;
					case "_order_shipping":
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["ship"]["total"] = $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["ship"]["cnt"] = $arrval['cnt'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["ship"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["ship"]["cnt"] += $arrval['cnt'];
						$this -> stored[$arrval['year']]["total"]["ship"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']]["total"]["ship"]["cnt"] += $arrval['cnt'];
						$this -> stored["total"]["ship"]["total"] += $arrval['total'];
						$this -> stored["total"]["ship"]["cnt"] += $arrval['cnt'];
						break;
					default:
						break;
				}
			}
			else
			if($arrval['type'] == "shop_order_refund"){
				switch ($arrval['mkey']) {
					case "_refund_amount":
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["refund"]["total"] = $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']][$arrval['day']]["refund"]["cnt"] = $arrval['cnt'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["refund"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']][$arrval['month']]["total"]["refund"]["cnt"] += $arrval['cnt'];
						$this -> stored[$arrval['year']]["total"]["refund"]["total"] += $arrval['total'];
						$this -> stored[$arrval['year']]["total"]["refund"]["cnt"] += $arrval['cnt'];
						$this -> stored["total"]["refund"]["total"] += $arrval['total'];
						$this -> stored["total"]["refund"]["cnt"] += $arrval['cnt'];
						break;
					default:
						break;
				} 
			}
		}
		$this -> stored = json_decode(json_encode($this -> stored), FALSE);
		
		// FILLING ZERO DUMMY DATA
		foreach ($this -> stored as $year => $yeardet) {
			if($year != "total"){
				$yearproj = 0;
				for($i = 1; $i<13; $i++){
					$dn = date("t", mktime(0,0,0,$i,1,$year));
					if(!isset($yeardet -> $i)){
						$this -> stored -> $year -> $i = new stdClass();
						$this -> stored -> $year -> $i -> total = new stdClass();
						$this -> stored -> $year -> $i -> total -> year = $year;
						$this -> stored -> $year -> $i -> total -> proj = new stdClass();
						$this -> stored -> $year -> $i -> total -> actual = new stdClass();
						$this -> stored -> $year -> $i -> total -> actual -> cnt = 0;
						$this -> stored -> $year -> $i -> total -> actual -> total = 0;
						$this -> stored -> $year -> $i -> total -> refund = new stdClass();
						$this -> stored -> $year -> $i -> total -> refund -> cnt = 0;
						$this -> stored -> $year -> $i -> total -> refund -> total = 0;
						$this -> stored -> $year -> $i -> total -> discount = new stdClass();
						$this -> stored -> $year -> $i -> total -> discount -> cnt = 0;
						$this -> stored -> $year -> $i -> total -> discount -> total = 0;
						$this -> stored -> $year -> $i -> total -> tax = new stdClass();
						$this -> stored -> $year -> $i -> total -> tax -> cnt = 0;
						$this -> stored -> $year -> $i -> total -> tax -> total = 0;
						$this -> stored -> $year -> $i -> total -> shiptax = new stdClass();
						$this -> stored -> $year -> $i -> total -> shiptax -> cnt = 0;
						$this -> stored -> $year -> $i -> total -> shiptax -> total = 0;
						$this -> stored -> $year -> $i -> total -> ship = new stdClass();
						$this -> stored -> $year -> $i -> total -> ship -> cnt = 0;
						$this -> stored -> $year -> $i -> total -> ship -> total = 0;
					}
					$this -> stored -> $year -> $i -> total -> proj -> total = (!isset($prjopt[$year][$i]))?0:$prjopt[$year][$i];	
					$yearproj += (!isset($prjopt[$year][$i]))?0:$prjopt[$year][$i];
					for($j = 1; $j<=$dn; $j++){
						if(!isset($yeardet -> $i -> $j)){
							$this -> stored -> $year -> $i -> $j = new stdClass();
							$this -> stored -> $year -> $i -> $j -> year = $year;
							$this -> stored -> $year -> $i -> $j -> proj = new stdClass();
							$this -> stored -> $year -> $i -> $j -> proj -> total = 0;
							$this -> stored -> $year -> $i -> $j -> actual = new stdClass();
							$this -> stored -> $year -> $i -> $j -> actual -> cnt = 0;
							$this -> stored -> $year -> $i -> $j -> actual -> total = 0;
							$this -> stored -> $year -> $i -> $j -> refund = new stdClass();
							$this -> stored -> $year -> $i -> $j -> refund -> cnt = 0;
							$this -> stored -> $year -> $i -> $j -> refund -> total = 0;
							$this -> stored -> $year -> $i -> $j -> discount = new stdClass();
							$this -> stored -> $year -> $i -> $j -> discount -> cnt = 0;
							$this -> stored -> $year -> $i -> $j -> discount -> total = 0;
							$this -> stored -> $year -> $i -> $j -> tax = new stdClass();
							$this -> stored -> $year -> $i -> $j -> tax -> cnt = 0;
							$this -> stored -> $year -> $i -> $j -> tax -> total = 0;
							$this -> stored -> $year -> $i -> $j -> shiptax = new stdClass();
							$this -> stored -> $year -> $i -> $j -> shiptax -> cnt = 0;
							$this -> stored -> $year -> $i -> $j -> shiptax -> total = 0;
							$this -> stored -> $year -> $i -> $j -> ship = new stdClass();
							$this -> stored -> $year -> $i -> $j -> ship -> cnt = 0;
							$this -> stored -> $year -> $i -> $j -> ship -> total = 0;
						}
					}	
				}
				$this -> stored -> $year -> total -> proj -> total = $yearproj;
			}
		}
		for($iii = -5; $iii < 6; $iii ++){
			$curyearx = date("Y") + $iii;
			if(!isset($this -> stored -> $curyearx)){
				$this -> stored -> $curyearx = new stdClass();
				$this -> stored -> $curyearx -> total = new stdClass();
				$this -> stored -> $curyearx -> total -> proj = new stdClass();
				$this -> stored -> $curyearx -> total -> proj -> total = 0;
				$this -> stored -> $curyearx -> total -> actual = new stdClass();
				$this -> stored -> $curyearx -> total -> actual -> cnt = 0;
				$this -> stored -> $curyearx -> total -> actual -> total = 0;
				$this -> stored -> $curyearx -> total -> refund = new stdClass();
				$this -> stored -> $curyearx -> total -> refund -> cnt = 0;
				$this -> stored -> $curyearx -> total -> refund -> total = 0;
				$this -> stored -> $curyearx -> total -> discount = new stdClass();
				$this -> stored -> $curyearx -> total -> discount -> cnt = 0;
				$this -> stored -> $curyearx -> total -> discount -> total = 0;
				$this -> stored -> $curyearx -> total -> tax = new stdClass();
				$this -> stored -> $curyearx -> total -> tax -> cnt = 0;
				$this -> stored -> $curyearx -> total -> tax -> total = 0;
				$this -> stored -> $curyearx -> total -> shiptax = new stdClass();
				$this -> stored -> $curyearx -> total -> shiptax -> cnt = 0;
				$this -> stored -> $curyearx -> total -> shiptax -> total = 0;
				$this -> stored -> $curyearx -> total -> ship = new stdClass();
				$this -> stored -> $curyearx -> total -> ship -> cnt = 0;
				$this -> stored -> $curyearx -> total -> ship -> total = 0;
				$yearproj = 0;
				for($i = 1; $i<13; $i++){
					$dn = date("t", mktime(0,0,0,$i,1,$curyearx));
					{
						$this -> stored -> $curyearx -> $i = new stdClass();
						$this -> stored -> $curyearx -> $i -> total = new stdClass();
						$this -> stored -> $curyearx -> $i -> total -> curyearx = $curyearx;
						$this -> stored -> $curyearx -> $i -> total -> proj = new stdClass();
						$this -> stored -> $curyearx -> $i -> total -> actual = new stdClass();
						$this -> stored -> $curyearx -> $i -> total -> actual -> cnt = 0;
						$this -> stored -> $curyearx -> $i -> total -> actual -> total = 0;
						$this -> stored -> $curyearx -> $i -> total -> refund = new stdClass();
						$this -> stored -> $curyearx -> $i -> total -> refund -> cnt = 0;
						$this -> stored -> $curyearx -> $i -> total -> refund -> total = 0;
						$this -> stored -> $curyearx -> $i -> total -> discount = new stdClass();
						$this -> stored -> $curyearx -> $i -> total -> discount -> cnt = 0;
						$this -> stored -> $curyearx -> $i -> total -> discount -> total = 0;
						$this -> stored -> $curyearx -> $i -> total -> tax = new stdClass();
						$this -> stored -> $curyearx -> $i -> total -> tax -> cnt = 0;
						$this -> stored -> $curyearx -> $i -> total -> tax -> total = 0;
						$this -> stored -> $curyearx -> $i -> total -> shiptax = new stdClass();
						$this -> stored -> $curyearx -> $i -> total -> shiptax -> cnt = 0;
						$this -> stored -> $curyearx -> $i -> total -> shiptax -> total = 0;
						$this -> stored -> $curyearx -> $i -> total -> ship = new stdClass();
						$this -> stored -> $curyearx -> $i -> total -> ship -> cnt = 0;
						$this -> stored -> $curyearx -> $i -> total -> ship -> total = 0;
					}
					$this -> stored -> $curyearx -> $i -> total -> proj -> total = (!isset($prjopt[$curyearx][$i]))?0:$prjopt[$curyearx][$i];	
					$yearproj += (!isset($prjopt[$curyearx][$i]))?0:$prjopt[$curyearx][$i];
					for($j = 1; $j<=$dn; $j++){
						{
							$this -> stored -> $curyearx -> $i -> $j = new stdClass();
							$this -> stored -> $curyearx -> $i -> $j -> year = $curyearx;
							$this -> stored -> $curyearx -> $i -> $j -> proj = new stdClass();
							$this -> stored -> $curyearx -> $i -> $j -> proj -> total = 0;
							$this -> stored -> $curyearx -> $i -> $j -> actual = new stdClass();
							$this -> stored -> $curyearx -> $i -> $j -> actual -> cnt = 0;
							$this -> stored -> $curyearx -> $i -> $j -> actual -> total = 0;
							$this -> stored -> $curyearx -> $i -> $j -> refund = new stdClass();
							$this -> stored -> $curyearx -> $i -> $j -> refund -> cnt = 0;
							$this -> stored -> $curyearx -> $i -> $j -> refund -> total = 0;
							$this -> stored -> $curyearx -> $i -> $j -> discount = new stdClass();
							$this -> stored -> $curyearx -> $i -> $j -> discount -> cnt = 0;
							$this -> stored -> $curyearx -> $i -> $j -> discount -> total = 0;
							$this -> stored -> $curyearx -> $i -> $j -> tax = new stdClass();
							$this -> stored -> $curyearx -> $i -> $j -> tax -> cnt = 0;
							$this -> stored -> $curyearx -> $i -> $j -> tax -> total = 0;
							$this -> stored -> $curyearx -> $i -> $j -> shiptax = new stdClass();
							$this -> stored -> $curyearx -> $i -> $j -> shiptax -> cnt = 0;
							$this -> stored -> $curyearx -> $i -> $j -> shiptax -> total = 0;
							$this -> stored -> $curyearx -> $i -> $j -> ship = new stdClass();
							$this -> stored -> $curyearx -> $i -> $j -> ship -> cnt = 0;
							$this -> stored -> $curyearx -> $i -> $j -> ship -> total = 0;
						}
					}	
				}
				$this -> stored -> $curyearx -> total -> proj -> total = $yearproj;
			}
		}
		$this -> stored = object_to_array ( $this -> stored );
		
		// SORTING COLLECTED DATA BY DATE
		ksort($this -> stored);	
		foreach ($this -> stored as $y => $yeardet) {
			ksort($this -> stored[$y]);
			foreach ($yeardet as $m => $monthdet) {
				ksort($this -> stored[$y][$m]);
			}
		}
		$this -> stored = json_decode(json_encode($this -> stored), FALSE);	
		
		// FETCHING PRODUCTS DATA FROM DATABASE
		$query = "
			SELECT  
				posts.post_title AS name,
				meta.meta_key as mkey, 
				meta.meta_value as val, 
				posts.ID AS ID
				FROM  {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta 
					ON posts.ID = meta.post_id 
			WHERE 
				posts.post_status IN ( 'publish','private' ) 
				AND posts.post_type IN ( 'product' ) 
				AND meta.meta_key IN ( 'total_sales' ,'_price' ,'post_views_count') 
			ORDER BY
				posts.ID ASC, 
				meta.meta_key ASC
		";
		$data_p = $wpdb->get_results(  $query,ARRAY_A);
		
		// STORING DATA IN A PLEASENT STRUCTURE
		$this -> stored_p = Array();
		$this -> stored_p['total'] = Array("total" => 0,"cnt" => 0,"nolist"=>1);
		foreach ($data_p as $key => $arrval) {
			if(!isset( $this -> stored_p[$arrval['ID']])){
				$this -> stored_p[$arrval['ID']] = Array();
				$this -> stored_p[$arrval['ID']] = Array("total" => 0,"price" => 0,"cnt" => 0,"views" => 0);
			}
			switch ($arrval['mkey']) {
				case "_price":
					$this -> stored_p[$arrval['ID']]["price"] = $arrval['val'];
					break;
				case "post_views_count":
					$this -> stored_p[$arrval['ID']]["views"] = $arrval['val'];
					break;
				case "total_sales":
					$this -> stored_p[$arrval['ID']]["cnt"] = $arrval['val'];
					$this -> stored_p[$arrval['ID']]["total"] = $arrval['val'] * $this -> stored_p[$arrval['ID']]["price"];
					$this -> stored_p['total']["total"] += $this -> stored_p[$arrval['ID']]["total"];
					$this -> stored_p['total']["cnt"] += $arrval['val'];
					$this -> stored_p[$arrval['ID']]["name"] = $arrval['name'];
					$this -> stored_p[$arrval['ID']]["ID"] = $arrval['ID'];
					break;
				default:
					break;
			}
		}
		$this -> stored_view = $this -> stored_p;
		
		// SORTING DATA BY VALUE
		usort($this -> stored_p, "sortbyval");
		$this -> stored_p = json_decode(json_encode($this -> stored_p), FALSE);
		
		// SORTING DATA BY VIEWS COUNT
		usort($this -> stored_view, "sortbyview");
		$this -> stored_view = json_decode(json_encode($this -> stored_view), FALSE);

		// FETCHING CUSTOMERS DATA FROM DATABASE
		$query = "
			SELECT 
				posts.post_title AS name,
				tmeta.meta_value as cnt,  
				pmeta.meta_value as val,  
				terms.name as cat, 
				terms.term_id as catid, 
				posts.ID AS ID
				
				FROM    {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS tmeta ON(posts.ID = tmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS pmeta ON(posts.ID = pmeta.post_id)
				LEFT JOIN {$wpdb->term_relationships} AS rel ON(posts.ID = rel.object_id)
				LEFT JOIN {$wpdb->term_taxonomy} AS taxo ON(rel.term_taxonomy_id = taxo.term_taxonomy_id)
				LEFT JOIN {$wpdb->terms} AS terms ON(taxo.term_id = terms.term_id)
			WHERE 
				posts.post_status IN ( 'publish','private' ) 
				AND posts.post_type IN ( 'product' ) 
				AND tmeta.meta_key IN ( 'total_sales')
				AND pmeta.meta_key IN ( '_price')
				AND taxo.taxonomy IN ( 'product_cat' ) 
			ORDER BY
				posts.ID ASC
		";
		$data_c = $wpdb->get_results(  $query,ARRAY_A);
		$this -> stored_c = Array();
		
		// FILL ZERO DUMMY DATA
		foreach ($data_c as $key => $arrval) {
			if(!isset( $this -> stored_c[$arrval['catid']])){
				$this -> stored_c[$arrval['catid']] = Array();
				$this -> stored_c[$arrval['catid']] = Array("total" => 0,"cnt" => 0,"name" => $arrval['cat'],"id" => $arrval['catid']);
				
			}	
			$this -> stored_c[$arrval['catid']]["cnt"] += $arrval['cnt'];
			$this -> stored_c[$arrval['catid']]["total"] += $arrval['cnt'] * $arrval['val'];
		}
		
		// SORTING DATA BY VALUE
		usort($this -> stored_c, "sortbyval");
		$this -> stored_c = json_decode(json_encode($this -> stored_c), FALSE);
		
		// FETCHING ORDERS DETAILED DATA FROM DATABASE
		$query = "
			SELECT 
				posts.post_status as status,  
				tmeta.meta_value as val,  
				bcmeta.meta_value as billingcountry,  
				bsmeta.meta_value as billingstate,  
				pmmeta.meta_value as paymethod,  
				pmtmeta.meta_value as paymethodtitle,  
				fnmeta.meta_value as firstname,
				lnmeta.meta_value as lastname,
				emmeta.meta_value as email,
				posts.ID AS ID
				
				FROM    {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS tmeta ON(posts.ID = tmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS bcmeta ON(posts.ID = bcmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS bsmeta ON(posts.ID = bsmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS pmtmeta ON(posts.ID = pmtmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS pmmeta ON(posts.ID = pmmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS fnmeta ON(posts.ID = fnmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS lnmeta ON(posts.ID = lnmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS emmeta ON(posts.ID = emmeta.post_id)
			WHERE 
				posts.post_status IN ( 'wc-completed','wc-processing','wc-on-hold','wc-pending' )  
				AND posts.post_type IN ( 'shop_order' ) 
				AND tmeta.meta_key IN ( '_order_total')
				AND bcmeta.meta_key IN ( '_billing_country')
				AND bsmeta.meta_key IN ( '_billing_state')
				AND pmmeta.meta_key IN ( '_payment_method')
				AND pmtmeta.meta_key IN ( '_payment_method_title')
				AND lnmeta.meta_key IN ( '_billing_last_name')
				AND fnmeta.meta_key IN ( '_billing_first_name')
				AND emmeta.meta_key IN ( '_billing_email')
			ORDER BY
				posts.ID ASC
		";
		$data_bc = $wpdb->get_results(  $query,ARRAY_A);
		
		// STRING DATA IN A PLEASENT STRUCTURE
		$this -> stored_bc = Array();
		$this -> stored_bs = Array();
		$this -> stored_pm = Array();
		$this -> stored_os = Array();
		$this -> stored_cd = Array();
		$statuses = Array("wc-completed" => "Completed", "wc-pending" => "Pending", "wc-processing" => "Processing", "wc-on-hold" => "On Hold");
		foreach ($data_bc as $key => $arrval) {
			if(!isset( $this -> stored_cd[$arrval['email']])){
				$this -> stored_cd[$arrval['email']] = Array();
				$this -> stored_cd[$arrval['email']] = Array("total" => 0,"cnt" => 0,"fname" => $arrval['firstname'],"lname" => $arrval['lastname'],"email" => $arrval['email']);
			}
			if(!isset( $this -> stored_os[$arrval['status']])){
				$this -> stored_os[$arrval['status']] = Array();
				$this -> stored_os[$arrval['status']] = Array("total" => 0,"cnt" => 0,"name" => $statuses[$arrval['status']],"id" => $arrval['status']);
			}
			if(!isset( $this -> stored_pm[$arrval['paymethod']])){
				$this -> stored_pm[$arrval['paymethod']] = Array();
				$this -> stored_pm[$arrval['paymethod']] = Array("total" => 0,"cnt" => 0,"name" => $arrval['paymethodtitle'],"id" => $arrval['paymethod']);
				
			}	
			if(!isset( $this -> stored_bc[$arrval['billingcountry']])){
				$this -> stored_bc[$arrval['billingcountry']] = Array();
				$this -> stored_bc[$arrval['billingcountry']] = Array("total" => 0,"cnt" => 0,"name" => $countrycodes[$arrval['billingcountry']],"id" => $arrval['billingcountry']);
			}	
			if(!isset( $this -> stored_bs[$arrval['billingstate']])){
				$this -> stored_bs[$arrval['billingstate']] = Array();
				$this -> stored_bs[$arrval['billingstate']] = Array("total" => 0,"cnt" => 0,"name" =>  $arrval['billingcountry']." - ".$arrval['billingstate'],"id" => $arrval['billingstate']);
			}	
			$this -> stored_cd[$arrval['email']]["cnt"] += 1;
			$this -> stored_cd[$arrval['email']]["total"] += $arrval['val'];
			$this -> stored_os[$arrval['status']]["cnt"] += 1;
			$this -> stored_os[$arrval['status']]["total"] += $arrval['val'];
			$this -> stored_pm[$arrval['paymethod']]["cnt"] += 1;
			$this -> stored_pm[$arrval['paymethod']]["total"] += $arrval['val'];
			$this -> stored_bc[$arrval['billingcountry']]["cnt"] += 1;
			$this -> stored_bc[$arrval['billingcountry']]["total"] += $arrval['val'];
			$this -> stored_bs[$arrval['billingstate']]["cnt"] += 1;
			$this -> stored_bs[$arrval['billingstate']]["total"] += $arrval['val'];
		}
		
		// SORTING DATA BY VALUE
		usort($this -> stored_bc, "sortbyval");
		$this -> stored_bc = json_decode(json_encode($this -> stored_bc), FALSE);
		usort($this -> stored_bs, "sortbyval");
		$this -> stored_bs = json_decode(json_encode($this -> stored_bs), FALSE);
		usort($this -> stored_pm, "sortbyval");
		$this -> stored_pm = json_decode(json_encode($this -> stored_pm), FALSE);
		usort($this -> stored_os, "sortbyval");
		$this -> stored_os = json_decode(json_encode($this -> stored_os), FALSE);
		usort($this -> stored_cd, "sortbyval");
		$this -> stored_cd = json_decode(json_encode($this -> stored_cd), FALSE);
		
		// FETCHING COUPONS DATA FROM DATABASE
		$query = "
			SELECT  
				posts.post_title AS name, 
				posts.ID AS id, 
				meta.meta_value AS val, 
				cmeta.meta_value AS cnt
				FROM  {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta 
					ON posts.ID = meta.post_id 
				LEFT JOIN {$wpdb->postmeta} AS cmeta 
					ON posts.ID = cmeta.post_id 
			WHERE 
				meta.meta_key = 'coupon_amount'
				AND cmeta.meta_key = 'usage_count'
				AND posts.post_type = 'shop_coupon'
				AND posts.post_status IN ( 'publish' )
		";
		$data_coupon = $wpdb->get_results(  $query,ARRAY_A);
		
		// STORING DATA IN A PLEASENT STRUCTURE
		$this -> stored_coupon = Array();
		$this -> stored_coupon["total"] = Array( "cnt" => 0, "total" => 0 ,"nolist" => 1);
		foreach ($data_coupon as $key => $arrval) {
			if(!isset( $this -> stored_coupon[$arrval['id']])){
				$this -> stored_coupon[$arrval['id']] = Array();
				$this -> stored_coupon[$arrval['id']] = Array("total" => $arrval['cnt']*$arrval['val'],"cnt" => $arrval['cnt'],"name" => $arrval['name'],"id" => $arrval['id']);
				
			}	
			$this -> stored_coupon["total"]["cnt"] += $arrval['cnt'];
			$this -> stored_coupon["total"]["total"] += $arrval['cnt'] * $arrval['val'];
		}
		
		// FETCHING NOT USED COUPONS DATA FROM DATABASE
		$query = "
			SELECT  
				posts.post_title AS name,
				posts.ID AS id 
				FROM  {$wpdb->posts} AS posts
			WHERE 
				posts.post_type = 'shop_coupon'
				AND posts.post_status IN ( 'publish' )
		";
		$data_coupon_zero = $wpdb->get_results(  $query,ARRAY_A);
		
		// STORING DATA IN A PLEASENT STRUCTURE
		foreach ($data_coupon_zero as $key => $arrval) {
			if(!isset( $this -> stored_coupon[$arrval['id']])){
				$this -> stored_coupon[$arrval['id']] = Array();
				$this -> stored_coupon[$arrval['id']] = Array("total" => 0,"cnt" => 0,"name" => $arrval['name'],"id" => $arrval['id']);
				
			}	
		}
		
		// SORTING DATA BY VALUE
		usort($this -> stored_coupon, "sortbyval");
		$this -> stored_coupon = json_decode(json_encode($this -> stored_coupon), FALSE);
	}
	
	// THE SEPARATE FUNCTION FOR GRAB PRODUCTS DATA
	public function products($date_from = "2000-01-01" ,$date_to = "" ,$cat = "-1" ,$stat = "-1" ,$product = "-1"){
		global $wpdb;
		
		// SET DEFAULT VALUES
		if($date_to == "") $date_to = date("Y-m-d");
		if($date_to == "") $date_to = date("Y-m-d");
		if($cat == "-1"){$cat=Array();
		$cat[] = "";
		foreach ($this -> stored_c as $key => $val) {
			$cat[] = $val -> id;
		}}
		if($stat == "-1") $stat = Array(
			"wc-completed" => "wc-completed", 
			"wc-on-hold" => "wc-on-hold", 
			"wc-cancelled" => "wc-cancelled", 
			"wc-refunded" => "wc-refunded", 
			"wc-failed" => "wc-failed", 
			"wc-pending" => "wc-pending", 
			"wc-processing" => "wc-processing"
		);
		if($product == "-1"){$product=Array();
		foreach ($this -> stored_c as $key => $val) {
			if(!isset($val -> nolist))$product[] = $val -> ID;
		}}
		
		// FETCHING DETAILED PRODUCTS DATA FROM DATABASE
		$query = "
			SELECT  
				posts.ID,
				posts.post_title AS name,
				GROUP_CONCAT(DISTINCT terms.term_id SEPARATOR ',') as cat,
				GROUP_CONCAT(DISTINCT CONCAT(meta.meta_key,'|->|',meta.meta_value) ORDER BY meta.post_id  SEPARATOR '~|~') as gmx,
				posts.post_date AS cdate,
				posts.post_modified AS mdate
				FROM  {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta ON (posts.ID = meta.post_id) 
				LEFT JOIN {$wpdb->term_relationships} AS rel ON(posts.ID = rel.object_id)
				LEFT JOIN {$wpdb->term_taxonomy} AS taxo ON(rel.term_taxonomy_id = taxo.term_taxonomy_id)
				LEFT JOIN {$wpdb->terms} AS terms ON(taxo.term_id = terms.term_id)
				LEFT JOIN {$wpdb->term_relationships} AS rel1 ON(posts.ID = rel1.object_id)
				LEFT JOIN {$wpdb->term_taxonomy} AS taxo1 ON(rel1.term_taxonomy_id = taxo1.term_taxonomy_id)
				LEFT JOIN {$wpdb->terms} AS terms1 ON(taxo1.term_id = terms1.term_id)
			WHERE 
				posts.post_status IN ( '" . implode( "','", $stat  ) . "' , 'publish','private' )
				AND posts.post_type IN ( 'product' ) 
				AND meta.meta_key IN ( 'total_sales' ,'_price','_stock', '_sku','_regular_price', '_sale_price', '_downloadable','_virtual', '_manage_stock', '_backorders', '_stock_status')
				AND taxo.taxonomy IN ( 'product_cat' ) 
				AND taxo1.taxonomy IN ( 'product_cat' ) 
				AND terms1.term_id IN ( '" . implode( "','", $cat  ) . "' )
				AND posts.ID IN ( '" . implode( "','", $product  ) . "' )
				AND posts.post_date BETWEEN '".$date_from."' AND '".$date_to."'
			GROUP BY
				posts.ID
			ORDER BY
				posts.ID ASC
		";
		$result = $wpdb->get_results(  $query,ARRAY_A);
		
		// STRUCTURIZE THE DATA
		foreach ($result as $key => $val) {
			$tmpmeta = explode("~|~", $val['gmx']);
			$tmparr = Array();
			foreach ($tmpmeta as $keymeta => $valmeta) {
				$tmpexp = explode("|->|", $valmeta);
				$tmparr[$tmpexp[0]] = $tmpexp[1];
			}
			$result[$key]['gmx'] = $tmparr;
		}
		$result = json_decode(json_encode($result), FALSE);
		
		return $result;
	}
	
	// THE SEPARATE FUNCTION FOR GRAB CATEGORIES DATA
	public function categories($date_from = "2000-01-01" ,$date_to = ""){
		global $wpdb;
		
		// SET DEFAULT VALUES
		if($date_to == "") $date_to = date("Y-m-d");
		if($date_to == "") $date_to = date("Y-m-d");
		
		// FETCHING DETAILED CATEGORIES DATA FROM DATABASE
		$query = "
			SELECT  
				posts.post_title AS name,
				meta.meta_value * pmeta.meta_value as total, 
				pmeta.meta_value as val, 
				meta.meta_value as cnt, 
				terms.name as cat, 
				terms.term_id as catid, 
				posts.ID AS ID
				FROM  {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id 
				LEFT JOIN {$wpdb->postmeta} AS pmeta ON(posts.ID = pmeta.post_id)
				LEFT JOIN {$wpdb->term_relationships} AS rel ON(posts.ID = rel.object_id)
				LEFT JOIN {$wpdb->term_taxonomy} AS taxo ON(rel.term_taxonomy_id = taxo.term_taxonomy_id)
				LEFT JOIN {$wpdb->terms} AS terms ON(taxo.term_id = terms.term_id)
			WHERE 
				posts.post_status IN ( 'publish','private' ) 
				AND posts.post_type IN ( 'product' ) 
				AND meta.meta_key IN ( 'total_sales' ) 
				AND pmeta.meta_key IN ( '_price') 
				AND taxo.taxonomy IN ( 'product_cat' ) 
				AND posts.post_date BETWEEN '".$date_from."' AND '".$date_to."'
			ORDER BY
				posts.ID ASC
		";
		$data = $wpdb->get_results(  $query,ARRAY_A);
		
		// STRUCTURIZE THE DATA
		$preresult = Array();
		foreach ($data as $key => $arrval) {
			if(!isset( $preresult[$arrval['catid']])){
				$preresult[$arrval['catid']] = Array();
				$preresult[$arrval['catid']] = Array("total" => 0,"cnt" => 0,"name" => $arrval['cat'],"id" => $arrval['catid']);
				
			}	
			$preresult[$arrval['catid']]["cnt"] += $arrval['cnt'];
			$preresult[$arrval['catid']]["total"] += $arrval['cnt'] * $arrval['val'];
		}
		
		// SORTING DATA BY VALUE
		usort($preresult, "sortbyval");
		$result = json_decode(json_encode($preresult), FALSE);
		
		return $result;
	}
	
	// THE SEPARATE FUNCTION FOR GRAB COUPONS DATA
	public function coupons($date_from = "2000-01-01" ,$date_to = ""){
		global $wpdb;
		
		// SET DEFAULT VALUES
		if($date_to == "") $date_to = date("Y-m-d");
		if($date_to == "") $date_to = date("Y-m-d");
	
		// FETCHING DETAILED COUPONS DATA FROM DATABASE
		$query = "
			SELECT  
				posts.post_title AS name, 
				posts.ID AS id, 
				meta.meta_value AS val, 
				cmeta.meta_value AS cnt
				FROM  {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta 
					ON posts.ID = meta.post_id 
				LEFT JOIN {$wpdb->postmeta} AS cmeta 
					ON posts.ID = cmeta.post_id 
			WHERE 
				meta.meta_key = 'coupon_amount'
				AND cmeta.meta_key = 'usage_count'
				AND posts.post_type = 'shop_coupon'
				AND posts.post_status IN ( 'publish' )
				AND posts.post_date BETWEEN '".$date_from."' AND '".$date_to."'
		";
		$data_coupon = $wpdb->get_results(  $query,ARRAY_A);
		
		// STRUCTURIZE THE DATA
		$result = Array();
		$result["total"] = Array( "cnt" => 0, "total" => 0 ,"nolist" => 1);
		foreach ($data_coupon as $key => $arrval) {
			if(!isset( $result[$arrval['id']])){
				$result[$arrval['id']] = Array();
				$result[$arrval['id']] = Array("total" => $arrval['cnt']*$arrval['val'],"cnt" => $arrval['cnt'],"name" => $arrval['name'],"id" => $arrval['id']);
				
			}	
			$result["total"]["cnt"] += $arrval['cnt'];
			$result["total"]["total"] += $arrval['cnt'] * $arrval['val'];
		}
		
		// FETCHING NOT USED COUPONS DATA
		$query = "
			SELECT  
				posts.post_title AS name,
				posts.ID AS id 
				FROM  {$wpdb->posts} AS posts
			WHERE 
				posts.post_type = 'shop_coupon'
				AND posts.post_status IN ( 'publish' )
		";
		$data_coupon_zero = $wpdb->get_results(  $query,ARRAY_A);
		
		// STRUCTURIZE THE DATA
		foreach ($data_coupon_zero as $key => $arrval) {
			if(!isset( $result[$arrval['id']])){
				$result[$arrval['id']] = Array();
				$result[$arrval['id']] = Array("total" => 0,"cnt" => 0,"name" => $arrval['name'],"id" => $arrval['id']);
				
			}	
		}
		
		// SORTING DATA BY VALUE
		usort($result, "sortbyval");
		$result = json_decode(json_encode($result), FALSE);
		
		return $result;
	}
	
	// THE SEPARATE FUNCTION FOR GRAB ALL ORDERS DATA
	public function stats($date_from = "2000-01-01" ,$date_to = "" ,$country = "-1"){
		global $wpdb;
		$statuses = Array(
			"wc-completed" => "Completed", "wc-on-hold" => "On Hold","wc-cancelled" => "Cancelled","wc-refunded" => "Refunded","wc-failed" => "Failed","wc-pending" => "Pending","wc-processing" => "Processing"
		);
		
		// SET DEFAULT VALUES
		if($date_to == "") $date_to = date("Y-m-d");
		if($date_to == "") $date_to = date("Y-m-d");
		$countrycodes = array (
			'AF' => 'Afghanistan', 'AX' => '?land Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Zaire', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'C𴥠D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island and Mcdonald Islands', 'VA' => 'Vatican City State', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'KENYA', 'KI' => 'Kiribati', 'KP' => 'Korea, Democratic People\'s Republic of', 'KR' => 'Korea, Republic of', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia, the Former Yugoslav Republic of', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States of', 'MD' => 'Moldova, Republic of', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'R궮ion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'PM' => 'Saint Pierre and Miquelon', 'VC' => 'Saint Vincent and the Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome and Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia and the South Sandwich Islands', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard and Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan, Province of China', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania, United Republic of', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks and Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Minor Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis and Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe'
		);
		if($country == "-1"){
			$country=Array();
			foreach ($countrycodes as $key => $val) {
				
				$country[] = $key;
			}
		}
		
		// FETCHING DETAILED CATEGORIES DATA FROM DATABASE
		$query = "
			SELECT 
				posts.post_status as status,  
				tmeta.meta_value as val,  
				bcmeta.meta_value as billingcountry,  
				bsmeta.meta_value as billingstate,  
				pmmeta.meta_value as paymethod,  
				pmtmeta.meta_value as paymethodtitle,  
				fnmeta.meta_value as firstname,
				lnmeta.meta_value as lastname,
				emmeta.meta_value as email,
				posts.ID AS ID
			FROM    {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS tmeta ON(posts.ID = tmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS bcmeta ON(posts.ID = bcmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS bsmeta ON(posts.ID = bsmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS pmtmeta ON(posts.ID = pmtmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS pmmeta ON(posts.ID = pmmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS fnmeta ON(posts.ID = fnmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS lnmeta ON(posts.ID = lnmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS emmeta ON(posts.ID = emmeta.post_id)
			WHERE 
				posts.post_status IN ( 'wc-completed','wc-processing','wc-on-hold','wc-pending' )  
				AND posts.post_type IN ( 'shop_order' ) 
				AND tmeta.meta_key IN ( '_order_total')
				AND bcmeta.meta_key IN ( '_billing_country')
				AND bcmeta.meta_value IN ( '" . implode( "','", $country  ) . "' )
				AND bsmeta.meta_key IN ( '_billing_state')
				AND pmmeta.meta_key IN ( '_payment_method')
				AND pmtmeta.meta_key IN ( '_payment_method_title')
				AND lnmeta.meta_key IN ( '_billing_last_name')
				AND fnmeta.meta_key IN ( '_billing_first_name')
				AND emmeta.meta_key IN ( '_billing_email')
				AND posts.post_date BETWEEN '".$date_from."' AND '".$date_to."'
			ORDER BY
				posts.ID ASC
		";
		$data = $wpdb->get_results(  $query,ARRAY_A);
		
		// STRUCTURIZE THE DATA
		$result = new stdClass();
		$result -> billcountries = Array();
		$result -> billstates = Array();
		$result -> paymethods = Array();
		$result -> orderstatuses = Array();
		$result -> costumers = Array();
		foreach ($data as $key => $arrval) {
			if(!isset( $result -> costumers[$arrval['email']])){
				$result -> costumers[$arrval['email']] = Array();
				$result -> costumers[$arrval['email']] = Array("total" => 0,"cnt" => 0,"fname" => $arrval['firstname'],"lname" => $arrval['lastname'],"email" => $arrval['email']);
				
			}
			if(!isset( $result -> orderstatuses[$arrval['status']])){
				$result -> orderstatuses[$arrval['status']] = Array();
				$result -> orderstatuses[$arrval['status']] = Array("total" => 0,"cnt" => 0,"name" => $statuses[$arrval['status']],"id" => $arrval['status']);
				
			}
			if(!isset( $result -> paymethods[$arrval['paymethod']])){
				$result -> paymethods[$arrval['paymethod']] = Array();
				$result -> paymethods[$arrval['paymethod']] = Array("total" => 0,"cnt" => 0,"name" => $arrval['paymethodtitle'],"id" => $arrval['paymethod']);
				
			}	
			if(!isset( $result -> billcountries[$arrval['billingcountry']])){
				$result -> billcountries[$arrval['billingcountry']] = Array();
				$result -> billcountries[$arrval['billingcountry']] = Array("total" => 0,"cnt" => 0,"name" => $countrycodes[$arrval['billingcountry']],"id" => $arrval['billingcountry']);
				
			}	
			if(!isset( $result -> billstates[$arrval['billingstate']])){
				$result -> billstates[$arrval['billingstate']] = Array();
				$result -> billstates[$arrval['billingstate']] = Array("total" => 0,"cnt" => 0,"name" =>  $arrval['billingcountry']." - ".$arrval['billingstate'],"id" => $arrval['billingstate']);
				
			}	
			$result -> costumers[$arrval['email']]["cnt"] += 1;
			$result -> costumers[$arrval['email']]["total"] += $arrval['val'];
			$result -> orderstatuses[$arrval['status']]["cnt"] += 1;
			$result -> orderstatuses[$arrval['status']]["total"] += $arrval['val'];
			$result -> paymethods[$arrval['paymethod']]["cnt"] += 1;
			$result -> paymethods[$arrval['paymethod']]["total"] += $arrval['val'];
			$result -> billcountries[$arrval['billingcountry']]["cnt"] += 1;
			$result -> billcountries[$arrval['billingcountry']]["total"] += $arrval['val'];
			$result -> billstates[$arrval['billingstate']]["cnt"] += 1;
			$result -> billstates[$arrval['billingstate']]["total"] += $arrval['val'];
		}
		
		// SORTING DATA BY VALUE
		usort($result -> billcountries, "sortbyval");
		$result -> billcountries = json_decode(json_encode($result -> billcountries), FALSE);
		usort($result -> billstates, "sortbyval");
		$result -> billstates = json_decode(json_encode($result -> billstates), FALSE);
		usort($result -> paymethods, "sortbyval");
		$result -> paymethods = json_decode(json_encode($result -> paymethods), FALSE);
		usort($result -> orderstatuses, "sortbyval");
		$result -> orderstatuses = json_decode(json_encode($result -> orderstatuses), FALSE);
		usort($result -> costumers, "sortbyval");
		$result -> costumers = json_decode(json_encode($result -> costumers), FALSE);
		
		return $result;
	}
	
	// THE SEPARATE FUNCTION FOR GRAB RECENT ORDERS DATA
	public function recentorders($date_from = "2000-01-01" ,$date_to = "" ,$stat = "-1"){
		global $wpdb;
		
		// SET DEFAULT VALUES
		if($date_to == "") $date_to = date("Y-m-d");
		if($stat == "-1") $stat = Array(
			"wc-completed" => "wc-completed", 
			"wc-on-hold" => "wc-on-hold", 
			"wc-cancelled" => "wc-cancelled", 
			"wc-refunded" => "wc-refunded", 
			"wc-failed" => "wc-failed", 
			"wc-pending" => "wc-pending", 
			"wc-processing" => "wc-processing"
		);
		
		// FETCHING DETAILED RECENT ORDERS DATA FROM DATABASE
		$query = "
			SELECT 
				posts.ID as ID,
				fnmeta.meta_value as firstname,
				lnmeta.meta_value as lastname,
				emmeta.meta_value as email,
				posts.post_date as pdate,
				posts.post_status as status,
				gameta.meta_value as gross,
				dameta.meta_value as discount,
				sameta.meta_value as shipping,
				stmeta.meta_value as shippingtax,
				otmeta.meta_value as ordertax
				FROM  {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS fnmeta ON(posts.ID = fnmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS lnmeta ON(posts.ID = lnmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS emmeta ON(posts.ID = emmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS gameta ON(posts.ID = gameta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS dameta ON(posts.ID = dameta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS sameta ON(posts.ID = sameta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS stmeta ON(posts.ID = stmeta.post_id)
				LEFT JOIN {$wpdb->postmeta} AS otmeta ON(posts.ID = otmeta.post_id)
			WHERE 
				posts.post_status IN ( '" . implode( "','", $stat  ) . "' )
				AND posts.post_type IN ( 'shop_order' ) 
				AND lnmeta.meta_key IN ( '_billing_last_name')
				AND fnmeta.meta_key IN ( '_billing_first_name')
				AND emmeta.meta_key IN ( '_billing_email')
				AND gameta.meta_key IN ( '_order_total')
				AND dameta.meta_key IN ( '_order_discount')
				AND sameta.meta_key IN ( '_order_shipping')
				AND stmeta.meta_key IN ( '_order_shipping_tax')
				AND otmeta.meta_key IN ( '_order_tax')
				AND posts.post_date BETWEEN '".$date_from."' AND '".$date_to."'
			ORDER BY
				posts.post_date
				DESC
		";
		$query = "
			SELECT 
				posts.ID as ID,
				posts.post_status as status,
				GROUP_CONCAT(DISTINCT CONCAT(meta.meta_key,'|->|',meta.meta_value) ORDER BY meta.post_id  SEPARATOR '~|~') as gmx,
				posts.post_date as pdate
			FROM  {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta ON(posts.ID = meta.post_id)
			WHERE 
				posts.post_status IN ( '" . implode( "','", $stat  ) . "' )
				AND posts.post_type IN ( 'shop_order' )
				AND meta.meta_key IN ( '_billing_first_name','_billing_last_name','_billing_email','_order_total', '_order_discount', '_order_shipping', '_order_shipping_tax', '_order_tax')
				AND posts.post_date BETWEEN '".$date_from."' AND '".$date_to."'
			GROUP BY
				posts.ID
			ORDER BY
				posts.ID ASC
				
		";
		$data = $wpdb->get_results($query,ARRAY_A);
		foreach ($data as $key => $val) {
			$tmpmeta = explode("~|~", $val['gmx']);
			$tmparr = Array();
			foreach ($tmpmeta as $keymeta => $valmeta) {
				$tmpexp = explode("|->|", $valmeta);
				$tmparr[$tmpexp[0]] = $tmpexp[1];
			}
			$data[$key]['gmx'] = $tmparr;
		}
		// STRUCTURIZE THE DATA
		$result = json_decode(json_encode($data), FALSE);
		
		return $result;
	}
}	
?>
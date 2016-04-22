<?php

/**
 * An array of flat rate box pricing - 2016
 * As of Jan 2016 USPS has removed all discounts for Click N Ship/Business/Online rate.  All rates are returning retail rates now.
 * We're keeping both just in case they change their minds later but for now will make the rates the same for both.
 */
return array(

	// Priority Mail Express

		// Priority Mail Express Flat Rate Envelope
		"d13"     => array(
			"retail" => "22.95",
			"online" => "22.95",
		),
		// Priority Mail Express Legal Flat Rate Envelope
		"d30"     => array(
			"retail" => "22.95",
			"online" => "22.95",
		),
		// Priority Mail Express Padded Flat Rate Envelope
		"d63"     => array(
			"retail" => "22.95",
			"online" => "22.95",
		),

	// Priority Mail Boxes

		// Priority Mail Flat Rate Medium Box
		"d17"     => array(
			"retail" => "13.45",
			"online" => "13.45",
		),
		// Priority Mail Flat Rate Medium Box
		"d17b"     => array(
			"retail" => "13.45",
			"online" => "13.45",
		),
		// Priority Mail Flat Rate Large Box
		"d22"     => array(
			"retail" => "18.75",
			"online" => "18.75",
		),

		// Priority Mail Flat Rate Large Box
		"d22a"     => array(
			"retail" => "18.75",
			"online" => "18.75",
		),
		// Priority Mail Flat Rate Small Box
		"d28"     => array(
			"retail" => "6.80",
			"online" => "6.80",
		),

	// Priority Mail Envelopes

		// Priority Mail Flat Rate Envelope
		"d16"     => array(
			"retail" => "6.45",
			"online" => "6.45",
		),
		// Priority Mail Padded Flat Rate Envelope
		"d29"     => array(
			"retail" => "6.80",
			"online" => "6.80",
		),
		// Priority Mail Gift Card Flat Rate Envelope
		"d38"     => array(
			"retail" => "6.45",
			"online" => "6.45",
		),
		// Priority Mail Window Flat Rate Envelope
		"d40"     => array(
			"retail" => "6.45",
			"online" => "6.45",
		),
		// Priority Mail Small Flat Rate Envelope
		"d42"     => array(
			"retail" => "6.45",
			"online" => "6.45",
		),
		// Priority Mail Legal Flat Rate Envelope
		"d44"     => array(
			"retail" => "6.45",
			"online" => "6.45",
		),

	// International Priority Mail Express

		// Priority Mail Express Flat Rate Envelope
		"i13"     => array(
			"retail"    => array(
				'*'  => "61.50",
				'CA' => "41.50"
			)
		),
		// Priority Mail Express Legal Flat Rate Envelope
		"i30"     => array(
			"retail"    => array(
				'*'  => "61.50",
				'CA' => "41.50"
			)
		),
		// Priority Mail Express Padded Flat Rate Envelope
		"i63"     => array(
			"retail"    => array(
				'*'  => "61.50",
				'CA' => "41.50"
			)
		),

	// International Priority Mail

		// Priority Mail Flat Rate Envelope
		"i8"      => array(
			"retail"    => array(
				'*'  => "30.95",
				'CA' => "23.95"
			)
		),
		// Priority Mail Padded Flat Rate Envelope
		"i29"      => array(
			"retail"    => array(
				'*'  => "30.95",
				'CA' => "23.95"
			)
		),
		// Priority Mail Flat Rate Small Box
		"i16"     => array(
			"retail"    => array(
				'*'  => "31.95",
				'CA' => "24.95"
			)
		),
		// Priority Mail Flat Rate Medium Box
		"i9"      => array(
			"retail"    => array(
				'*'  => "67.95",
				'CA' => "45.95"
			)
		),
		// Priority Mail Flat Rate Medium Box
		"i9b"      => array(
			"retail"    => array(
				'*'  => "67.95",
				'CA' => "45.95"
			)
		),
		// Priority Mail Flat Rate Large Box
		"i11"     => array(
			"retail"    => array(
				'*'  => "88.95",
				'CA' => "59.95"
			)
		),
);

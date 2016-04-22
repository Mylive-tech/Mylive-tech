<?php
global $mymail_autoresponder;
$mymail_autoresponder_info = array(

	'units' => apply_filters('mymail_autoresponder_units', array(
		'minute' => __('minute(s)', 'mymail'),
		'hour' => __('hour(s)', 'mymail'),
		'day' => __('day(s)', 'mymail'),
		'week' => __('week(s)', 'mymail'),
		'year' => __('year(s)', 'mymail'),
	)),

	'actions' => apply_filters('mymail_autoresponder_actions', array(
		'mymail_subscriber_insert' => array(
			'label' => __('user signed up', 'mymail'),
			'hook' => 'mymail_subscriber_insert'
		),
		'mymail_subscriber_unsubscribed' => array(
			'label' => __('user unsubscribed', 'mymail'),
			'hook' => 'mymail_subscriber_unsubscribed'
		),
		'mymail_post_published' => array(
			'label' => __('something has been published', 'mymail'),
			'hook' => 'transition_post_status'
		),
		'mymail_autoresponder_timebased' => array(
			'label' => __('at a specific time', 'mymail'),
			'hook' => 'mymail_autoresponder_timebased'
		),
		'mymail_autoresponder_usertime' => array(
			'label' => __('a specific user time', 'mymail'),
			'hook' => 'mymail_autoresponder_usertime'
		),
		'mymail_autoresponder_followup' => array(
			'label' => __('a specific campaign', 'mymail'),
			'hook' => 'mymail_autoresponder_followup'
		),
		'mymail_autoresponder_hook' => array(
			'label' => __('a specific action hook', 'mymail'),
			'hook' => 'mymail_autoresponder_hook'
		),
	)),

);

<?php

$mymail_homepage = array(
		'post_title' => __('Newsletter', 'mymail'),
		'post_status' => 'draft',
		'post_type' => 'page',
		'post_name' => 'newsletter-signup',
		'post_content' => "[newsletter_signup]".__('Signup for the newsletter', 'mymail')."[newsletter_signup_form id=1][/newsletter_signup] [newsletter_confirm]".__('Thanks for your interest!', 'mymail')."[/newsletter_confirm] [newsletter_unsubscribe]".__('Do you really want to unsubscribe?', 'mymail')."[/newsletter_unsubscribe]",
);

<?php
add_action('g1_sidebar_1_begin', 'action_g1_sidebar_1_begin');

function action_g1_sidebar_1_begin() {
	echo do_shortcode('[shareaholic app="share_buttons" id="6696321"]');
}
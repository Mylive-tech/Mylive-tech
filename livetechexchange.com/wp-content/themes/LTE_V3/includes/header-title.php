<!-- Subpage header -->
<section class="subpage_header">

	<?php $subpage = doover_get_option( 'subpage' ); ?>
	<?php if( $subpage['title'] ): ?>
		<h1><?php echo trim( wp_title( '', false ) ); ?></h1>
	<?php endif; ?>
	<?php if( $subpage['breadcrumbs'] ): ?>
		<?php doover_breadcrumbs(); ?>
	<?php endif; ?>
    
    <?php if( ( $contact = doover_get_option( 'btn_get_in_touch' ) ) && $subpage['title']  ): ?>
		<a href="<?php echo get_permalink( $contact );?>" class="call_to_action call_to_action_contact"><span><?php _e('Get in touch with us','doover'); ?></span></a>
	<?php endif; ?>
	
</section>
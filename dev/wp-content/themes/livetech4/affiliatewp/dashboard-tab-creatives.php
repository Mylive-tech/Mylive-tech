<div id="affwp-affiliate-dashboard-creatives" class="affwp-tab-content">

	<h3><?php _e( 'Creatives', 'affiliate-wp' ); ?></h3>

	<?php
	$per_page  = 30;
	$page      = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$args      = array(
		'number' => $per_page,
		'offset' => $per_page * ( $page - 1 )
	);
	$creatives = affiliate_wp()->creative->affiliate_creatives( $args );
	 
	?>

	<?php if( $creatives ) : ?>

		<?php echo affiliate_wp()->creative->affiliate_creatives( $args ); ?>

		<div class="affwp-pagination">
			<?php
			echo paginate_links( array(
				'current'      => $page,
				'total'        => ceil( affiliate_wp()->creatives->count( array( 'status' => 'active' ) ) / $per_page ),
				'add_args'     => array(
					'tab'      => 'creatives'
				)
			) );
			?>
		</div>

	<?php else : ?>
		<p class="affwp-no-results"><?php _e( 'Sorry, there are currently no creatives available.', 'affiliate-wp' ); ?></p>
	<?php endif; ?>
	
</div>
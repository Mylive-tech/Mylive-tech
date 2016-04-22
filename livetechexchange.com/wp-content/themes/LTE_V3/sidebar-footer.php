<?php
/**
 * The Footer widget areas.
 * 

 */
?>

<?php
	$sidebars_count = 0;	
	if ( is_active_sidebar( 'footer-area-1' ) ) $sidebars_count++;
	if ( is_active_sidebar( 'footer-area-2' ) ){
		$sidebars_count++;
		$last = 2;
	}
	if ( is_active_sidebar( 'footer-area-3' ) ){
		$sidebars_count++;
		$last = 3;
	}
	
	if ( ! $sidebars_count ) 
	{
		echo '<br />'; return;
	}
	
	switch( $sidebars_count ){
		case 1:
			$sidebar_class = 'one';
			break; 
		case 2:
			$sidebar_class = 'one_second';
			break; 
		case 3:
			$sidebar_class = 'one_third';
			break; 
	}	
?>

<div class="row clearfix">

	<?php if ( is_active_sidebar( 'footer-area-1' ) ) : ?>
		<div class="widget-area col <?php echo $sidebar_class;?>">
			<?php dynamic_sidebar( 'footer-area-1' ); ?>
		</div>
	<?php endif; ?>	
	
	<?php if ( is_active_sidebar( 'footer-area-2' ) ) : ?>
		<div class="widget-area col<?php echo " ".$sidebar_class;?><?php if( $last == 2 ) echo " last"; ?>">
			<?php dynamic_sidebar( 'footer-area-2' ); ?>
		</div>
	<?php endif; ?>	
	
	<?php if ( is_active_sidebar( 'footer-area-3' ) ) : ?>
		<div class="widget-area col<?php echo " ".$sidebar_class;?><?php if( $last == 3 ) echo " last"; ?>">
			<?php dynamic_sidebar( 'footer-area-3' ); ?>
		</div>
	<?php endif; ?>	
	
</div>
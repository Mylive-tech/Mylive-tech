<?php
/**
 * Opening Hours meta box
 */
?>

<table id="opening-hours" class="clearfix">
	<?php foreach( $days as $i => $day ) : ?>
		<tr>
			<td><label data-day="<?php echo $i ?>"><?php echo $day ?></label> <a class="add-hours" href="#"><i class="dashicons dashicons-plus-alt"></i></a></td>
			<td class="hours">
				<?php if( isset($hours[$i]) ): for( $j=0; $j<count($hours[$i]); $j+=2 ): ?>
					<div class="hours-row"><input value="<?php echo $hours[$i][$j] ?>" name="pos_opening_hours[<?php echo $i ?>][]" type="text"> - <input value="<?php echo $hours[$i][$j+1] ?>" name="pos_opening_hours[<?php echo $i ?>][]" type="text"> <a class="remove-hours" href="#"><i class="dashicons dashicons-dismiss"></i></a></div>
				<?php endfor; endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>

	<tr>
		<td colspan="2">
			<label for="pos_opening_hours_notes"><?php _e( 'Opening Hours notes', 'woocommerce-pos-pro' ) ?></label>
			<textarea id="pos_opening_hours_notes" name="pos_opening_hours_notes" placeholder="<?php _e('eg: Closed public holidays', 'woocommerce-pos-pro' ); ?>"><?php echo get_post_meta( $post->ID, '_opening_hours_notes', true ); ?></textarea>
		</td>
	</tr>
	<input type="hidden" name="pos_opening_hours[8]"><!-- force update postmeta on save -->
</table>
<p><?php printf( __( 'You can add opening hours to your website using a widget. <a href="%s">Read the docs</a> for more information.', 'woocommerce-pos-pro' ), 'http://woopos.com.au/docs/opening-hours/' ); ?></p>

<script type="text/javascript">
	jQuery(function($) {
		$('#opening-hours').on( 'click', '.add-hours', function(e) {
			e.preventDefault();
			var i = $(this).siblings('label').data('day');
			var inputs = '<div class="hours-row"><input name="pos_opening_hours[' + i + '][]" type="text">';
			inputs += ' - <input name="pos_opening_hours[' + i + '][]" type="text">';
			inputs += '<a class="remove-hours" href="#"><i class="dashicons dashicons-dismiss"></i></a></div>';
			$(this).parent('td').next('td.hours').append(inputs);
		});
		$('#opening-hours').on( 'click', '.remove-hours', function(e) {
			e.preventDefault();
			$(this).parent('.hours-row').remove();
		});
	});
</script>
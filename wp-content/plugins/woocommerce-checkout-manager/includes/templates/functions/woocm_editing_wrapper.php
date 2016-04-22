<?php
/**
 * WooCommerce Checkout Manager 
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function wooccm_photo_editor_content() {

	if( is_checkout() == false )
		return;
?>
<div id="caman_content" style="display:none;">

	<div id="wooccmtoolbar">
		<div class="button" id="save"><?php _e( 'Save', 'woocommerce-checkout-manager' ); ?></div>
		<div class="button" id="close"><?php _e( 'Close', 'woocommerce-checkout-manager' ); ?></div>
		<h3><?php _e( 'Photo Editing', 'woocommerce-checkout-manager' ); ?></h3>
	</div>
	<!-- #wooccmtoolbar -->

	<div class="wooccmsidebar">
		<div id="Filters">

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Brightness', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="-100" max="100" step="1" value="0" data-filter="brightness">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Contrast', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="-100" max="100" step="1" value="0" data-filter="contrast">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Saturation', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="-100" max="100" step="1" value="0" data-filter="saturation">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Vibrance', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="-100" max="100" step="1" value="0" data-filter="vibrance">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Exposure', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="-100" max="100" step="1" value="0" data-filter="exposure">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Hue', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="0" max="100" step="1" value="0" data-filter="hue">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Sepia', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="0" max="100" step="1" value="0" data-filter="sepia">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Gamma', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="0" max="10" step="0.1" value="0" data-filter="gamma">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Noise', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="0" max="100" step="1" value="0" data-filter="noise">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Clip', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="0" max="100" step="1" value="0" data-filter="clip">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'Sharpen', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="0" max="100" step="1" value="0" data-filter="sharpen">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Filter">
				<div class="FilterName">
					<p><?php _e( 'StackBlur', 'woocommerce-checkout-manager' ); ?></p>
				</div>
				<!-- .FilterName -->

				<div class="FilterSetting">
					<input type="range" min="0" max="20" step="1" value="0" data-filter="stackBlur">
					<span class="FilterValue">0</span>
				</div>
				<!-- .FilterSetting -->
			</div>
			<!-- .Filter -->

			<div class="Clear"></div>

		</div>
		<!-- #Filters -->

		<div id="PresetFilters">
			<a data-preset="vintage"><?php _e( 'Vintage', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="lomo"><?php _e( 'Lomo', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="clarity"><?php _e( 'Clarity', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="sinCity"><?php _e( 'Sin City', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="sunrise"><?php _e( 'Sunrise', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="crossProcess"><?php _e( 'Cross Process', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="orangePeel"><?php _e( 'Orange Peel', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="love"><?php _e( 'Love', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="grungy"><?php _e( 'Grungy', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="jarques"><?php _e( 'Jarques', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="pinhole"><?php _e( 'Pinhole', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="oldBoot"><?php _e( 'Old Boot', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="glowingSun"><?php _e( 'Glowing Sun', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="hazyDays"><?php _e( 'Hazy Days', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="herMajesty"><?php _e( 'Her Majesty', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="nostalgia"><?php _e( 'Nostalgia', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="hemingway"><?php _e( 'Hemingway', 'woocommerce-checkout-manager' ); ?></a>
			<a data-preset="concentrate"><?php _e( 'Concentrate', 'woocommerce-checkout-manager' ); ?></a>
		</div>
		<!-- #PresetFilters -->

	</div>
	<!-- .wooccmsidebar -->

	<div class="wooccmimageeditor" id="wooccmimageeditorpro"></div>

</div>
<!-- #caman_content -->

<?php

}
add_action('wp_head', 'wooccm_photo_editor_content');
?>
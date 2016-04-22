<tr valign="top" id="service_options">
	<th scope="row" class="titledesc"><?php _e( 'Services', 'woocommerce-shipping-usps' ); ?></th>
	<td class="forminp">
		<table class="usps_services widefat">
			<thead>
				<th class="sort">&nbsp;</th>
				<th><?php _e( 'Name', 'woocommerce-shipping-usps' ); ?></th>
				<th><?php _e( 'Service(s)', 'woocommerce-shipping-usps' ); ?></th>
				<th><?php echo sprintf( __( 'Price Adjustment (%s)', 'woocommerce-shipping-usps' ), get_woocommerce_currency_symbol() ); ?></th>
				<th><?php _e( 'Price Adjustment (%)', 'woocommerce-shipping-usps' ); ?></th>
			</thead>
			<tbody>
				<?php
					$sort = 0;
					$this->ordered_services = array();

					foreach ( $this->services as $code => $values ) {

						if ( isset( $this->custom_services[ $code ]['order'] ) ) {
							$sort = $this->custom_services[ $code ]['order'];
						}

						while ( isset( $this->ordered_services[ $sort ] ) )
							$sort++;

						$this->ordered_services[ $sort ] = array( $code, $values );

						$sort++;
					}

					ksort( $this->ordered_services );

					foreach ( $this->ordered_services as $value ) {
						$code   = $value[0];
						$values = $value[1];
						if ( ! isset( $this->custom_services[ $code ] ) )
							$this->custom_services[ $code ] = array();
						?>
						<tr>
							<td class="sort">
								<input type="hidden" class="order" name="usps_service[<?php echo $code; ?>][order]" value="<?php echo isset( $this->custom_services[ $code ]['order'] ) ? $this->custom_services[ $code ]['order'] : ''; ?>" />
							</td>
							<td>
								<input type="text" name="usps_service[<?php echo $code; ?>][name]" placeholder="<?php echo $values['name']; ?> (<?php echo $this->title; ?>)" value="<?php echo isset( $this->custom_services[ $code ]['name'] ) ? $this->custom_services[ $code ]['name'] : ''; ?>" size="35" />
							</td>
							<td>
								<ul class="sub_services" style="font-size: 0.92em; color: #555">
									<?php foreach ( $values['services'] as $key => $name ) : ?>
									<li style="line-height: 23px;">
										<label>
											<input type="checkbox" name="usps_service[<?php echo $code; ?>][<?php echo $key; ?>][enabled]" <?php checked( ( ! isset( $this->custom_services[ $code ][ $key ]['enabled'] ) || ! empty( $this->custom_services[ $code ][ $key ]['enabled'] ) ), true ); ?> />
											<?php echo $name; ?>
										</label>
									</li>
									<?php endforeach; ?>
								</ul>
							</td>
							<td>
								<ul class="sub_services" style="font-size: 0.92em; color: #555">
									<?php foreach ( $values['services'] as $key => $name ) : ?>
									<li>
										<?php echo get_woocommerce_currency_symbol(); ?><input type="text" name="usps_service[<?php echo $code; ?>][<?php echo $key; ?>][adjustment]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ][ $key ]['adjustment'] ) ? $this->custom_services[ $code ][ $key ]['adjustment'] : ''; ?>" size="4" />
									</li>
									<?php endforeach; ?>
								</ul>
							</td>
							<td>
								<ul class="sub_services" style="font-size: 0.92em; color: #555">
									<?php foreach ( $values['services'] as $key => $name ) : ?>
									<li>
										<input type="text" name="usps_service[<?php echo $code; ?>][<?php echo $key; ?>][adjustment_percent]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ][ $key ]['adjustment_percent'] ) ? $this->custom_services[ $code ][ $key ]['adjustment_percent'] : ''; ?>" size="4" />%
									</li>
									<?php endforeach; ?>
								</ul>
							</td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</td>
</tr>
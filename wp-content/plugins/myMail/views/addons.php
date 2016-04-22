<div class="wrap">

	<h2>MyMail Newsletter Add Ons</h2>

	<h3>Extend the functionality of MyMail</h3>

	<?php

		if ( false === ( $addons = get_transient( 'mymail_addons' ) ) ) {
			$url = 'http://mymailapp.github.io/v1/addons.json';

			$response = wp_remote_get( $url, array());

			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );

			if ( $response_code != 200 || is_wp_error( $response ) ) {
				echo '<div class="error below-h2"><p>There was an error retrieving the list from the server.</p></div>'
				;
				switch($response_code){
					case '403':
				echo '<div class="error below-h2"><p>Seems your host is blocking <strong>'.dirname($url).'</strong>. Please request to white list this domain </p></div>';
						break;
				}

				wp_die();
			}

			$addons = json_decode($response_body);
			set_transient( 'mymail_addons', $addons, 3600 );
		}

	?>

	<ul class="addons-wrap">
		<?php foreach($addons as $addon) {  if(!empty($addon->hidden)) continue;
			$addon->link = isset($addon->link) ? add_query_arg(array(
				'utm_source' => 'MyMail Add On Page',
				'utm_medium' => 'link',
				'utm_campaign' => 'MyMail Add Ons'
			), $addon->link) : '';
		?>
		<li class="mymail-addon <?php if(!empty($addon->is_free)) echo ' is-free' ?><?php if(!empty($addon->is_feature)) echo ' is-feature' ?>">
			<div class="bgimage" style="background-image:url(<?php echo isset($addon->image) ? $addon->image : '' ?>)">
				<?php if(isset($addon->wpslug)) : ?>
					<a href="plugin-install.php?tab=plugin-information&plugin=<?php echo dirname($addon->wpslug) ?>&from=import&TB_iframe=true&width=745&height=745" class="thickbox">&nbsp;</a>
				<?php else : ?>
					<a href="<?php echo $addon->link ?>">&nbsp;</a>
				<?php endif; ?>
			</div>
			<h4><?php echo $addon->name ?></h4>
			<p class="author">by
			<?php
				if($addon->author_url) :
					echo '<a href="'.$addon->author_url.'">'.$addon->author.'</a>';
			 	else :
			 		echo $addon->author;
				 endif;
			?>
			</p>
			<p class="description"><?php echo $addon->description ?></p>
			<div class="action-links">
			<?php if(!empty($addon->wpslug)) : ?>

				<?php if(is_dir(dirname(WP_PLUGIN_DIR . '/' .$addon->wpslug))) : ?>
					<?php if(is_plugin_active($addon->wpslug)) : ?>
						<a class="button" href="<?php echo wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $addon->wpslug, 'deactivate-plugin_' . $addon->wpslug) ?>"><?php _e('Deactivate', 'mymail' ); ?></a>
					<?php elseif(is_plugin_inactive($addon->wpslug)) : ?>
						<a class="button" href="<?php echo wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $addon->wpslug, 'activate-plugin_' . $addon->wpslug) ?>"><?php _e('Activate', 'mymail' ); ?></a>
					<?php endif; ?>
				<?php else : ?>
					<?php if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) : ?>
						<a class="button button-primary" href="<?php echo wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . dirname($addon->wpslug) . '&mymail-addon'), 'install-plugin_' . dirname($addon->wpslug)); ?>"><?php _e('Download from wordpress.org', 'mymail' ); ?></a>
					<?php endif; ?>
				<?php endif; ?>

			<?php else : ?>

					<a class="button button-primary" href="<?php echo $addon->link ?>"><?php _e('Get this Add On', 'mymail' ); ?></a>

			<?php endif; ?>
			</div>
		</li>
		<?php } ?>
	</ul>



<div id="ajax-response"></div>
<br class="clear">
</div>

<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce; ?>

<?php $woocommerce->show_messages(); ?>

<?php do_action('woocommerce_before_customer_login_form'); ?>

<?php if (get_option('woocommerce_enable_myaccount_registration')=='yes') : ?>

<div class="col2-set" id="customer_login">



<?php endif; ?>
<ul class="g1-grid lt-myacc-login">
<li class="g1-column g1-one-fourth g1-valign-middle">
<br>
		<h2 style="text-align:center"><i class="icon-lock g1-icon g1-icon--simple g1-icon--small g1-icon--circle " id="icon-1" style="color:#333"></i> <?php _e( 'Login', 'woocommerce' ); ?></h2>
        <br>
        </li>
        <li class="g1-column g1-one-fourth g1-valign-top">
		<form method="post" class="login">
			
				<label for="username"><?php _e( 'Username or email', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="username" />
			 </li>
			<li class="g1-column g1-one-fourth g1-valign-top">
				<label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" />
			 </li>
			<li class="g1-column g1-one-fourth g1-valign-middle" style="float:right;margin-left:0">
             <br>
				<?php $woocommerce->nonce_field('login', 'login') ?>
				<input type="submit" class="button" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" />
				<a class="lost_password" href="<?php

				$lost_password_page_id = woocommerce_get_page_id( 'lost_password' );

				if ( $lost_password_page_id )
					echo esc_url( get_permalink( $lost_password_page_id ) );
				else
					echo esc_url( wp_lostpassword_url( home_url() ) );

				?>"><?php _e( 'Lost Password?', 'woocommerce' ); ?></a>
			 <br>
		</form>
</li></ul>
<?php if (get_option('woocommerce_enable_myaccount_registration')=='yes') : ?>

<br>
<div class="g1-divider g1-divider--simple g1-divider--icon " id="g1-divider-4"><span><i class="icon-star"></i></span></div>

<h2 class="lt-register-heading"> Don't Have Live-Tech Account? 
<br><span> Register below for your Live-Tech account. Get access &amp; manage subscriptions, orders, downloads, special discounts -- all in one place.</span>
</h2>

<div id="g1-box-counter-1" class="g1-box g1-box--solid  g1-box--icon lt-myaccount-header"><i class="icon-user g1-box__icon"></i><div class="g1-box__inner lt-register-form">

		<h2><?php _e( 'Register', 'woocommerce' ); ?></h2>
		<form method="post" class="register">

			<?php if ( get_option( 'woocommerce_registration_email_for_username' ) == 'no' ) : ?>

				<p class="form-row form-row-first">
					<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="username" id="reg_username" value="<?php if (isset($_POST['username'])) echo esc_attr($_POST['username']); ?>" />
				</p>

				<p class="form-row form-row-last">

			<?php else : ?>

				<p class="form-row form-row-wide">

			<?php endif; ?>

				<label for="reg_email"><?php _e( 'Email', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="email" class="input-text" name="email" id="reg_email" value="<?php if (isset($_POST['email'])) echo esc_attr($_POST['email']); ?>" />
			</p>

			<div class="clear"></div>

			<p class="form-row form-row-first">
				<label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password" id="reg_password" value="<?php if (isset($_POST['password'])) echo esc_attr($_POST['password']); ?>" />
			</p>
			<p class="form-row form-row-last">
				<label for="reg_password2"><?php _e( 'Re-enter password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if (isset($_POST['password2'])) echo esc_attr($_POST['password2']); ?>" />
			</p>
			<div class="clear"></div>

			<!-- Spam Trap -->
			<div style="left:-999em; position:absolute;"><label for="trap">Anti-spam</label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

			<?php do_action( 'register_form' ); ?>

			<p class="form-row">
				<?php $woocommerce->nonce_field('register', 'register') ?>
				<input type="submit" class="button" name="register" value="<?php _e( 'Register', 'woocommerce' ); ?>" />
			</p>

		</form>

	</div></div>

</div>
<?php endif; ?>

<?php do_action('woocommerce_after_customer_login_form'); ?>
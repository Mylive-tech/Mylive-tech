<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$filter_value  = yit_get_filter_args( false );
if( ! empty( $prices ) ) : ?>
    <ul class="yith-wcan-list-price-filter">
        <?php foreach( $prices as $price ) : ?>
            <li class="price-item">
                <?php $is_active = yit_check_active_price_filter( $price['min'], $price['max'] ); ?>
                <?php
                if ( $is_active ) {
                    $filter_value = yit_remove_price_filter_query_args( $filter_value );
                }

                else {
                    $filter_value = array_merge( $filter_value, array( 'min_price' => $price['min'], 'max_price' => $price['max'] ) );
                }
                ?>
                <?php $link_class = $is_active ? 'yith-wcan-price-link active' : 'yith-wcan-price-link'; ?>
                <a class="<?php echo $link_class ?>" href="<?php echo esc_url( add_query_arg( $filter_value, $shop_page_uri ) ) ?>" class="yith-wcan-price-filter-list-link">
                    <?php echo _x( 'From', 'Price filter option: price starts from', 'yith-woocommerce-ajax-navigation' ) . ': ' . wc_price( $price['min'] ) . ' ' . _x( 'To', 'Price filter option: price ends to', 'yith-woocommerce-ajax-navigation' ) . ': ' . wc_price( $price['max'] );  ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
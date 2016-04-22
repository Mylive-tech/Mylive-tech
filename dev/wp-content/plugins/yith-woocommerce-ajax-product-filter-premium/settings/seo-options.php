<?php

$custom_style = array(

    'seo' => array(

        'header'   => array(

            array( 'type' => 'open' ),

            array(
                'name' => __( 'SEO', 'yith-woocommerce-ajax-navigation' ),
                'type' => 'title'
            ),

            array( 'type' => 'close' )
        ),

        'settings' => array(

            array( 'type' => 'open' ),

            array(
                'name' => __( 'Enable SEO option', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Add the "robots" meta tag in the head of the page if filters have been activeted.', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_enable_seo',
                'type' => 'on-off',
                'std'  => 'no',
            ),

            array(
                'name' => __( 'Index and Follow option', 'yith-woocommerce-ajax-navigation' ),
                'desc' => __( 'Meta tag options', 'yith-woocommerce-ajax-navigation' ),
                'id'   => 'yith_wcan_seo_value',
                'type' => 'select',
                'std'  => 'noindex-follow',
                'options' => array(
                    'noindex-nofollow'  => 'noindex, nofollow',
                    'noindex-follow'    => 'noindex, follow',
                    'index-nofollow'    => 'index, nofollow',
                    'index-follow'      => 'index, follow',
                ),
                'custom_attributes' => array(
                    'style' => 'width: 150px;'
                )
            ),

            array( 'type' => 'close' ),
        ),
    )
);

return apply_filters( 'yith_wcan_panel_seo_options', $custom_style );
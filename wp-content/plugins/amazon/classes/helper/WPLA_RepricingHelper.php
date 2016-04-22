<?php

class WPLA_RepricingHelper {
	
	const TABLENAME = 'amazon_listings';


    static public function repriceProducts() {

        // first adjust existing lowest prices upwards, then lower prices which are higher than the lowest price
        $changed_product_ids1 = self::uppriceProducts();
        $changed_product_ids2 = self::downpriceProducts();
        $changed_product_ids3 = self::resetProductsToMaxPrice();

        return array_merge( $changed_product_ids1, $changed_product_ids2, $changed_product_ids3 );
    }


    // adjust items with min/max prices but without lowest price - where price is lower than max_price, increase to max_price
    static public function resetProductsToMaxPrice() {

        $lm                  = new WPLA_ListingsModel();
        $items               = $lm->getItemsWithoutLowestPriceButPriceLowerThanMaxPrice();
        $changed_product_ids = array();
        // $repricing_margin    = floatval( get_option('wpla_repricing_margin') );

        // loop found listings
        foreach ( $items as $item ) {

            // make sure there is a max_price but no lowest price
            if ( $item->lowest_price ) continue;
            if ( ! $item->max_price  ) continue;
            if ( ! $item->post_id    ) continue; // make sure there is a product

            // target price is max price
            $target_price = $item->max_price;

            // check if current price is actually lower than max price
            if ( $item->price >= $target_price ) continue;

            // skip if there is no change in price
            // if ( $target_price == $item->price ) continue;


            // update amazon price in WooCommerce
            update_post_meta( $item->post_id, '_amazon_price', $target_price );

            // update price in listings table
            $data = array( 
                'price'      => $target_price,
                'pnq_status' => 1,                  // mark price as changed
            );
            $lm->updateWhere( array( 'id' => $item->id ), $data );

            // remember post_id for later
            $changed_product_ids[] = $item->post_id;

            WPLA()->logger->info('resetProductsToMaxPrice() - new price for #'.$item->sku.': '.$target_price);
        } // foreach item

        return $changed_product_ids;
    } // resetProductsToMaxPrice()



    // adjust existing lowest prices upwards
    static public function uppriceProducts() {

        $lm                  = new WPLA_ListingsModel();
        $items               = $lm->getItemsWithPriceLowerThanLowestPrice();
        $changed_product_ids = array();
        $repricing_margin    = floatval( get_option('wpla_repricing_margin') );

        // loop found listings
        foreach ( $items as $item ) {

            // make sure there is a product - and min/max prices are set (0 != NULL)
            if ( ! $item->post_id   ) continue;
            if ( ! $item->min_price ) continue;
            if ( ! $item->max_price ) continue;

            // apply margin to lowest price - so we can use lowest price as target reference
            $target_price = round( $item->lowest_price - $repricing_margin, 2 );

            // check if current price is lower than lowest price
            if ( $item->price > $target_price ) continue;

            // make sure we don't go below min_price
            if ( $target_price < $item->min_price ) continue;

            // make sure we don't go above max_price (prevent feed error)
            if ( $item->max_price ) $target_price = min( $target_price, $item->max_price );

            // skip if there is no change in price
            if ( $target_price == $item->price ) continue;


            // update amazon price in WooCommerce
            update_post_meta( $item->post_id, '_amazon_price', $target_price );

            // update price in listings table
            $data = array( 
                'price'      => $target_price,
                'pnq_status' => 1,                  // mark price as changed
            );
            $lm->updateWhere( array( 'id' => $item->id ), $data );

            // remember post_id for later
            $changed_product_ids[] = $item->post_id;

            WPLA()->logger->info('uppriceProducts() - new price for #'.$item->sku.': '.$target_price);
        } // foreach item

        return $changed_product_ids;
    } // uppriceProducts()


    // lower prices which are higher than the lowest price
    static public function downpriceProducts() {

        $lm                  = new WPLA_ListingsModel();
        $items               = $lm->getItemsWithPriceHigherThanLowestPrice();
        $changed_product_ids = array();
        $repricing_margin    = floatval( get_option('wpla_repricing_margin') );

        // loop found listings
        foreach ( $items as $item ) {

            // make sure there is a product - and min/max prices are set (0 != NULL)
            if ( ! $item->post_id   ) continue;
            if ( ! $item->min_price ) continue;
            if ( ! $item->max_price ) continue;

            // apply margin to lowest price - so we can use lowest price as target reference
            $target_price = round( $item->lowest_price - $repricing_margin, 2 );

            // check if current price is higher than lowest price
            if ( $item->price <= $target_price ) continue;

            // make sure we don't go below min_price
            if ( $target_price < $item->min_price ) continue;

            // make sure we don't go above max_price (prevent feed error)
            if ( $item->max_price ) $target_price = min( $target_price, $item->max_price );

            // skip if there is no change in price
            if ( $target_price == $item->price ) continue;


            // update amazon price in WooCommerce
            update_post_meta( $item->post_id, '_amazon_price', $target_price );

            // update price in listings table
            $data = array( 
                'price'      => $target_price,
                'pnq_status' => 1,                  // mark price as changed
            );
            $lm->updateWhere( array( 'id' => $item->id ), $data );

            // remember post_id for later
            $changed_product_ids[] = $item->post_id;

            WPLA()->logger->info('downpriceProducts() - new price for #'.$item->sku.': '.$target_price);
        } // foreach item

        // echo "<pre>";print_r($changed_product_ids);echo"</pre>";#die();
        // echo "<pre>";print_r($items);echo"</pre>";die();

        return $changed_product_ids;
    } // downpriceProducts()

} // class WPLA_RepricingHelper

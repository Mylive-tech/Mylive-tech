<div class="overlay_order_popup" id="overlay_retrieve_sales">
    <div id="retrieve_sales_popup">
        <div class="media-frame-title" style="position: initial;">
            <h1 id="retrieve_sales_popup_title"><?php _e('Retrieve Sales', 'wc_point_of_sale'); ?> - <i><?php echo $data['name']; ?></i></h1>
        </div>
        <span class="close_popup"></span>
        <div id="retrieve_sales_popup_wrap">
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <?php $reg_ = WC_POS()->register()->get_data_names(); ?>
                    <select name="retrieve_from" id="bulk-action-selector-top">
                        <option value="all" data-name="<?php _e('All', 'wc_point_of_sale'); ?>"><?php _e('All', 'wc_point_of_sale'); ?></option>
                        <option value="<?php echo $data['ID']; ?>" data-name="<?php echo $data['name']; ?>"><?php _e('This Register', 'wc_point_of_sale'); ?></option>
                        <?php if(!empty($reg_)){
                            foreach ($reg_ as $reg_id => $reg_name) {
                                if ($reg_id == $data['ID']) continue;
                                echo '<option value="'.$reg_id.'" data-name="'.$reg_name.'">'.$reg_name.'</option>';
                            }
                        } ?>
                    </select>
                    <input type="button" value="<?php _e('Load', 'wc_point_of_sale'); ?>" class="button action" id="btn_retrieve_from">

                </div>
                <p class="search-box">
                    <label for="post-search-input" class="screen-reader-text"><?php _e('Search Orders', 'wc_point_of_sale'); ?>:</label>
                    <input type="search" value="" name="s" id="orders-search-input">
                    <input type="button" value="<?php _e('Search Orders', 'wc_point_of_sale'); ?>" class="button" id="orders-search-submit">
                </p>
            </div>
            <div id="retrieve_sales_popup_inner">
            </div>
        </div>
    </div>
</div>
<div class="overlay_order_popup" id="add_product_custom_meta_overlay_popup">
    <div id="add_product_custom_meta_popup">
        <div class="media-frame-title">
            <h1><?php _e('Product Meta Fields', 'wc_point_of_sale'); ?></h1>
            <div class="clear"></div>
            <span class="close_popup"></span>
        </div>
        <div class="media-frame-wrap">
            <div class="box_content">
                <input type="hidden" id="add_custom_meta_product_id">
                <table id="product_custom_meta_table" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th class="meta_label">
                                <?php _e('Product Attribute', 'wc_point_of_sale'); ?>
                            </th>
                            <th class="meta_attribute">
                                <?php _e('Meta Value', 'wc_point_of_sale'); ?>
                            </th>
                            <th class="remove_meta"></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="media-frame-footer">
            <button class="button" id="add_product_custom_meta"><?php _e('Add Meta', 'wc_point_of_sale'); ?></button>
            <button class="button button-primary" id="save_product_custom_meta"><?php _e('Update Product', 'wc_point_of_sale'); ?></button>
        </div>
    </div>
</div>
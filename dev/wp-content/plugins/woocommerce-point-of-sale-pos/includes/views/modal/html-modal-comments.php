<div class="overlay_order_popup" id="overlay_order_comments">
    <div id="order_comments_popup">
        <div class="media-frame-title">
            <h1><?php _e('Notes', 'woocommerce'); ?></h1>
            <span class="close_popup"></span>
        </div>
        <div class="media-frame-wrap">
            <textarea name="order_comments" id="order_comments"><?php echo $order->customer_note; ?></textarea>
        </div>
        <div class="media-frame-footer">
            <button class="button button-primary" type="button" id="save_order_comments">Add Note</button>
            
        </div>
    </div>
</div>
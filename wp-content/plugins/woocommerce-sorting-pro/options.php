<?php
    /**
     *  Options for Sorting WooCommerce Plugin
     */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    global $woocommerce;

    # Get WooCommerce attributes
    $attributes = $woocommerce->get_attribute_taxonomies();

    # Products Categories
    $product_categories = get_terms( "product_cat" );

    # index for javascript
    if( empty( $sorting_woocommerce_options['attribute'] ) ) 
        $sorting_woocommerce_options['attribute'] = array();

    $index_js = count( $sorting_woocommerce_options['attribute'] );

    # index for loop
    $index = 0;

?>

<div class="wrap">
    <h2>Sorting WooCommerce</h2>
    <p>The plugin will help you manage the drop down sorting menu that appears at the Shop page.</p>

    <h3>Process Existing Products</h3>
    <p>
        Click on the following button if you've just installed the plugin and you already have products in your database.<br/><br/>
        <input type="button" class="button" id="sorting_woocommerce_process_oldies" value="Process products" onclick="sorting_woocommerce_process_oldies();" />
        <div id="sorting_woocommerce_process_results">
            <ul id="sorting_woocommerce_process_results_list">

            </ul>
        </div>
    </p>

    <h3>Mass Assign Attributes</h3>
    <p>
        The products that do not have the attribute you want to sort with set will disappear if you select the sorting in the dropdown menu. This will help you setting a default value for all your products.<br/><br/>
        <label>Which attribute to set?</label><br/>
        <select name="mass_assign_category" id="mass_assign_category">
            <option value="-1">All Categories</option>
            <?php foreach( $product_categories as $c ): ?>
                <option value="<?php echo $c->term_id; ?>"><?php echo $c->name; ?></option>
            <?php endforeach; ?>
        </select><br/><br/>
        <label>Which attribute to set?</label><br/>
        <select name="mass_assign_attribute" id="mass_assign_attribute">
            <?php foreach( $attributes as $att ): ?>
                <option value="<?php echo 'pa_' . $att->attribute_name; ?>"><?php echo $att->attribute_label; ?></option>
            <?php endforeach; ?>
        </select><br/><br/>
        <label>The value to assign  (leave empty to use 0)</label><br/>
        <input type="text" name="mass_assign_value" id="mass_assign_value" /><br/><br/>
        <input type="checkbox" value="1" name="mass_assign_hide" id="mass_assign_hide" checked /> Check to show on the product's page<br/><br/>
        <input type="button" class="button" id="mass_assign_button" value="Mass Assign to all products" onclick="sorting_woocommerce_mass_assign();" />

        <div id="sorting_woocommerce_mass_assign_results">
            <ul id="mass_assign_products_list">

            </ul>
        </div>
    </p>
    
    <form id="sorting_woocommerce" method="POST" action="options.php" onsubmit="return sorting_woocommerce_remove_index();">
        <?php settings_fields('sorting-woocommerce-options-group'); ?>

        <h3>General Settings</h3>

        <table class="form-table">
            <?php
                $checked = '';
                if( !empty( $sorting_woocommerce_options['show_only_these'] ) )
                    $checked = 'checked="checked"';
            ?>
            <tr>
                <td>
                    <input type="checkbox" value="1" name="sorting-woocommerce-options[show_only_these]" <?php echo $checked; ?>/> Check to show only these attributes
                </td>
            </tr>
            <?php
                $checked = '';
                if( !empty( $sorting_woocommerce_options['hide_dropdown_from_shop'] ) )
                    $checked = 'checked="checked"';
            ?>
            <tr>
                <td>
                    <input type="checkbox" value="1" name="sorting-woocommerce-options[hide_dropdown_from_shop]" <?php echo $checked; ?>> Check to hide/remove the sorting drop down menu from the shop.</input>
                </td>
            </tr>
            <?php 
                $asc_icon_url = '';
                if( !empty( $sorting_woocommerce_options['asc_sorting_icon'] ) ) {
                    $asc_icon_url = $sorting_woocommerce_options['asc_sorting_icon'];
                }
            ?>
            <tr>
                <td>
                    ASC Sorting Icon (A-Z)
                    <input type="text" placeholder="Place the icon url's here" value="<?php echo $asc_icon_url; ?>" name="sorting-woocommerce-options[asc_sorting_icon]" /> 
                    <a class="button" href="javascript:;" onclick="sorting_woocommerce_media_upload(this)">Upload</a>
                </td>
            </tr>
            <?php 
                $desc_icon_url = '';
                if( !empty( $sorting_woocommerce_options['desc_sorting_icon'] ) ) {
                    $desc_icon_url = $sorting_woocommerce_options['desc_sorting_icon'];
                }
            ?>
            <tr>
                <td>
                    DESC Sorting Icon  (Z-A)
                    <input type="text" placeholder="Place the icon url's here" value="<?php echo $desc_icon_url; ?>" name="sorting-woocommerce-options[desc_sorting_icon]" /> 
                    <a class="button" href="javascript:;" onclick="sorting_woocommerce_media_upload(this)">Upload</a>
                </td>
            </tr>
        </table>
        
        <h3>Manage Elements - <a href="javascript:;" onclick="sorting_woocommerce_add()" class="button">Add sorting by attribute</a></h3>
        <p>Click on the "Add sorting by attribute" to choose from the list of your <i>custom attributes</i>. When done, click on Save.</p>

        <table id="sorting_attributes_table" class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th>Attribute</th>
                    <th>Ordering</th>
                    <th>Label to appear in dropdown</th>
                    <th>Comparison</th>
                    <th>Order in list</th>
                    <th>Category</th>
                    <th>Set as default for</th>
                    <th>Hide from dropdown</th>
                    <th>Action</th>
                </tr>
            </thead>
            <!-- Used for javascript -->
            <tr id="tr_index_for_js" valign="top" style="display:none">
                <td>
                    <select name="sorting-woocommerce-options[attribute][index][slug]">
                        <?php foreach( $attributes as $att ): ?>
                            <option value="<?php echo 'pa_' . $att->attribute_name; ?>"><?php echo $att->attribute_label; ?></option>
                        <?php endforeach; ?>
                        <?php foreach( $sorting_woocommerce_default_attributes as $slug => $name ): ?>
                            <option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                <td>
                    <select name="sorting-woocommerce-options[attribute][index][order]">
                        <option value="ASC">Ascending</option>
                        <option value="DESC"> Descending</option>
                    </select>
                </td>
                <td>
                    <input style="width: 88% !important" type="text" placeholder="Sort by X from A to Z" name="sorting-woocommerce-options[attribute][index][sorting_label]" />
                </td>
                <td>
                    <select name="sorting-woocommerce-options[attribute][index][comparison]">
                        <option value="meta_value">Meta Value</option>
                        <option value="meta_value_num"> Meta Value Num</option>
                    </select>
                </td>
                <td>
                    <input style="width: 88% !important" type="text" placeholder="0" name="sorting-woocommerce-options[attribute][index][order_in_list]" />
                </td>
                <td>
                    <select data-placeholder="Choose a category or more..." style="width:100%;" tabindex="1" name="sorting-woocommerce-options[attribute][index][categories][]" class="make_me_chozen" multiple>
                        <?php foreach( $product_categories as $cat ): ?>
                            <option value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <select data-placeholder="Choose a category or more..." style="width:100%;" tabindex="1" name="sorting-woocommerce-options[attribute][index][categories_default][]" class="make_me_chozen" multiple>
                        <?php foreach( $product_categories as $cat ): ?>
                            <option value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="checkbox"  name="sorting-woocommerce-options[attribute][index][hide_from_list]" />
                </td>
                <td>
                    <a href="javascript:;" onclick="sorting_woocommerce_remove(this)" class="button">Remove</a>
                </td>
            </tr>
            <!-- End -->
            <?php foreach( $sorting_woocommerce_options['attribute'] as $saved_att ): ?>
            <tr valign="top">
                <!-- <td>Attribute</td> -->
                <td>
                    <select name="sorting-woocommerce-options[attribute][<?php echo $index; ?>][slug]">
                        <?php foreach( $attributes as $att ): ?>
                            <option value="<?php echo 'pa_' . $att->attribute_name; ?>" <?php if( ( $saved_att['slug']) == 'pa_' . $att->attribute_name ) echo 'selected'; ?>><?php echo $att->attribute_label; ?></option>
                        <?php endforeach; ?>
                        <?php foreach( $sorting_woocommerce_default_attributes as $slug => $name ): ?>
                            <option value="<?php echo $slug; ?>" <?php if( $saved_att['slug'] == $slug ) echo 'selected'; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                <!-- <td>Ordering</td> -->
                <td>
                    <?php
                        if( empty( $saved_att['order'] ) ) {
                            $order = "ASC";
                        } else {
                            $order = $saved_att['order'];
                        }
                    ?>
                    <select name="sorting-woocommerce-options[attribute][<?php echo $index; ?>][order]">
                        <option value="ASC" <?php if( $order == 'ASC' ) echo 'selected' ; ?> >Ascending</option>
                        <option value="DESC" <?php if( $order == 'DESC' ) echo 'selected' ; ?>> Descending</option>
                    </select>
                </td>
                <!-- <td>Text to appear in dropdown</td> -->
                <td>
                    <?php
                        $label = $saved_att['sorting_label'];
                    ?>
                    <input style="width: 88% !important" type="text" placeholder="Sort by X from A to Z" name="sorting-woocommerce-options[attribute][<?php echo $index; ?>][sorting_label]" value="<?php echo $label; ?>" />
                </td>
                <!-- <td>Comparison</td> -->
                <td>
                    <?php
                        if( empty( $saved_att['comparison'] ) ) {
                            $comparison = "meta_value";
                        } else {
                            $comparison = $saved_att['comparison'];
                        }
                    ?>
                    <select name="sorting-woocommerce-options[attribute][<?php echo $index; ?>][comparison]">
                        <option value="meta_value" <?php if( $comparison == 'meta_value' ) echo 'selected' ; ?> >Meta Value</option>
                        <option value="meta_value_num" <?php if( $comparison == 'meta_value_num' ) echo 'selected' ; ?>> Meta Value Num</option>
                    </select>
                </td>
                <td>
                    <?php
                        $in_list = '';
                        if( !empty( $saved_att['order_in_list'] ) )
                            $in_list = $saved_att['order_in_list'];
                    ?>
                    <input style="width: 88% !important" type="text" placeholder="0" name="sorting-woocommerce-options[attribute][<?php echo $index; ?>][order_in_list]" value="<?php echo $in_list; ?>" />
                </td>
                <td>
                    <select data-placeholder="Choose a category or more..." style="width:100%;" tabindex="1" name="sorting-woocommerce-options[attribute][<?php echo $index; ?>][categories][]" class="make_me_chozen" multiple>
                        <?php
                            $saved_att['categories'] = empty($saved_att['categories'])?array():$saved_att['categories'];
                            foreach( $product_categories as $cat ): 
                        ?>
                            <option <?php selected( true, in_array( $cat->term_id, $saved_att['categories']) ); ?> value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <select data-placeholder="Choose a category or more..." style="width:100%;" tabindex="1" name="sorting-woocommerce-options[attribute][<?php echo $index; ?>][categories_default][]" class="make_me_chozen" multiple>
                        <?php
                            $saved_att['categories_default'] = empty($saved_att['categories_default'])?array():$saved_att['categories_default'];
                            foreach( $product_categories as $cat ): 
                        ?>
                            <option <?php selected( true, in_array( $cat->term_id, $saved_att['categories_default']) ); ?> value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <?php
                        $checked = '';
                        if( !empty( $saved_att['hide_from_list'] ) )
                            $checked = 'checked="checked"';
                    ?>
                    <input type="checkbox"  name="sorting-woocommerce-options[attribute][<?php echo $index; ?>][hide_from_list]" <?php echo $checked; ?>/>
                </td>
                <td>
                    <a href="javascript:;" onclick="sorting_woocommerce_remove(this)" class="button">Remove</a>
                </td>
            </tr>
            <?php 
                $index++;
                endforeach; 
            ?>
            <tfoot>
                <tr>
                    <th>Attribute</th>
                    <th>Ordering</th>
                    <th>Label to appear in dropdown</th>
                    <th>Comparison</th>
                    <th>Order in list</th>
                    <th>Category</th>
                    <th>Hide from dropdown</th>
                    <th>Action</th>
                </tr>
            </tfoot>
        </table>
        <br/>
        <input class="button-primary" type="submit" value="Save">
    </form>
</div>
<script type="text/javascript">
    var index = <?php echo $index_js; ?>;
    var offset = 0;
    function sorting_woocommerce_add() {
        html = jQuery('#sorting_attributes_table > tbody > tr:first')[0].outerHTML;
        html = html.replace( /index/g, index);
        jQuery('#sorting_attributes_table').append(html);
        jQuery('#sorting_attributes_table > tbody > tr:last').show();
        index++;
    }

    function sorting_woocommerce_remove( obj ) {
        jQuery(obj).parents('tr:first').remove();
    }   

    function sorting_woocommerce_remove_index() {
        jQuery('#tr_index_for_js').remove();
        return true;
    }

    function sorting_woocommerce_process_oldies( offset ) {
        if( typeof offset == 'undefined' ) {
            offset = 0;
            jQuery( '#sorting_woocommerce_process_results_list' ).html('');
        }

        jQuery( '#sorting_woocommerce_process_oldies' ).val('Processing');
        jQuery( '#sorting_woocommerce_process_oldies' ).attr('disabled', 'disabled');

        var data = {
            'action': 'sorting_woocommerce_process_oldies',
            'offset': offset
        };

        jQuery.ajax( {
            'url' : ajaxurl,
            'type': 'POST',
            'data': data
        } ).done( function( response ) {
                response = jQuery.parseJSON( response );
                sorting_woocommerce_oldies_callback( response );    
         } );
    }

    function sorting_woocommerce_oldies_callback( response ) {
        jQuery.each( response.posts, function( index, text ) {
            jQuery('#sorting_woocommerce_process_results_list').append( "<li>" + text + "</li>" );
        } );
        if( response.new_offset != '-1' ) {
            sorting_woocommerce_process_oldies( response.new_offset );
        } else {
            jQuery('#sorting_woocommerce_process_results_list').append( "<li><strong>Done</strong></li>" );
            jQuery( '#sorting_woocommerce_process_oldies' ).val('Process products');
            jQuery( '#sorting_woocommerce_process_oldies' ).removeAttr( 'disabled' );
        }
    }

    function sorting_woocommerce_mass_assign( offset ) {
        if( typeof offset == 'undefined' ) {
            offset = 0;
            jQuery( '#mass_assign_products_list' ).html('');
        }

        jQuery( '#mass_assign_button' ).val('Processing');
        jQuery( '#mass_assign_button' ).attr('disabled', 'disabled');

        var data = {
            'action': 'sorting_woocommerce_mass_assign',
            'offset': offset,
            'value' : jQuery( '#mass_assign_value' ).val(),
            'term' : jQuery( '#mass_assign_attribute' ).val(),
            'is_visible' : jQuery('#mass_assign_hide').is(':checked')?'1':'0',
            'category' : jQuery('#mass_assign_category').val()
        };

        jQuery.ajax( {
            'url' : ajaxurl,
            'type': 'POST',
            'data': data
        } ).done( function( response ) {
                response = jQuery.parseJSON( response );
                sorting_woocommerce_mass_assign_callback( response );    
         } );
    }

    function sorting_woocommerce_mass_assign_callback( response ) {
        jQuery.each( response.posts, function( index, text ) {
            jQuery('#mass_assign_products_list').append( "<li>" + text + "</li>" );
        } );
        if( response.new_offset != '-1' ) {
            sorting_woocommerce_mass_assign( response.new_offset );
        } else {
            jQuery( '#mass_assign_products_list' ).append( "<li><strong>Done</strong></li>" );
            jQuery( '#mass_assign_button' ).val('Process products');
            jQuery( '#mass_assign_button' ).removeAttr( 'disabled' );
        }
    }

    // Uploading files
    var file_frame;
 
    function sorting_woocommerce_media_upload(a_obj){
        event.preventDefault();
     
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          title: jQuery( this ).data( 'Icon' ),
          button: {
            text: jQuery( this ).data( 'Insert' ),
          },
          multiple: false  // Set to true to allow multiple files to be selected
        });
        
        var obj = a_obj;
        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
          // We set multiple to false so only get one image from the uploader
          attachment = file_frame.state().get('selection').first().toJSON();
            
          // Do something with attachment.id and/or attachment.url here
          jQuery(jQuery(obj).siblings('input:first')[0]).val(attachment.url);
        });
     
        // Finally, open the modal
        file_frame.open();
    }
</script>
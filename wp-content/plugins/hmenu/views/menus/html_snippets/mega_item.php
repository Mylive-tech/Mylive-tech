	<?php $the_index = $_GET['index']; ?>
	<?php $plugin_url = $_GET['url']; ?>
    <?php $nav_item_id = $_GET['navItemId']; ?>
    <?php $nav_parent_id = $_GET['parentId']; ?>
    <?php $nav_level = $_GET['lvl']; ?>
    <?php $the_mega_id = $_GET['megaMenuId']; ?>
    <li class="hero_mega_menu hero_sort_item" data-allow-sub="no" data-index="<?php echo $the_index; ?>" id="hero_margin_left_<?php echo $nav_level; ?>" data-level="<?php echo $nav_level; ?>" data-id="<?php echo $nav_item_id; ?>" data-menu-type="mega" data-parent="<?php echo $nav_parent_id; ?>">
        <div class="hero_item_wrap">
            <div class="hero_item_bar rounded_3 hero_mega_bg">
                <div class="hero_item_toggle" data-nav-toggle="close"></div>
                <div class="hero_item_heading size_14 hero_white" id="mega_heading_<?php echo $the_index; ?>">
                    Menu Name
                </div>
                <input type="hidden" style="width:60px;" id="item_order_<?php echo $the_index; ?>" name="item_order_<?php echo $the_index; ?>">
                <input type="hidden" style="width:60px;" id="item_parent_<?php echo $the_index; ?>" name="item_parent_<?php echo $the_index; ?>">
                <input type="hidden" style="width:60px;" id="item_level_<?php echo $the_index; ?>" name="item_level_<?php echo $the_index; ?>">
                <div class="hero_item_edits_holder">
                	<div class="hero_nav_type rounded_30 size_10" id="item_type_<?php echo $the_index; ?>">post</div>
                    <div class="hero_edits rounded_20">
                        <div class="hero_edit_item hero_button_edit" style="background-image:url(<?php echo $plugin_url; ?>/assets/images/admin/edit_icon.png)"></div>
                        <div class="hero_edit_item hero_button_delete" data-main-index="<?php echo $the_index; ?>" data-item-id="<?php echo $nav_item_id; ?>" data-parent-id="<?php echo $nav_parent_id; ?>" data-level="<?php echo $nav_level; ?>" style="background-image:url(<?php echo $plugin_url; ?>/assets/images/admin/delete_icon.png)"></div>
                    </div>
                    <div class="hero_item_drag"></div>
                </div>
            </div>
            <div class="hero_col_12 hero_item_content hero_bottom_line">
                <div class="hero_col_3">                    
                    <label class="size_12">Mega name</label>
                    <input type="text" data-size="lrg" id="mega_name_<?php echo $the_index; ?>" name="mega_name_<?php echo $the_index; ?>">                    
                </div>
                <div class="hero_col_3">                    
                    <label class="size_12">Title</label>
                    <input type="text" data-size="lrg" id="mega_alt_<?php echo $the_index; ?>" name="mega_alt_<?php echo $the_index; ?>">                    
                </div>
                <div class="hero_col_12">
                    <div class="hero_col_4">
                        <label><h2 class="size_14 hero_green">Icon</h2></label>
                        <div class="hero_switch_position"><input type="checkbox" data-size="sml" data-smltoggler="toggle_mega_icon_<?php echo $the_index; ?>" id="mega_icon_<?php echo $the_index; ?>" name="mega_icon_<?php echo $the_index; ?>" value="1"></div>
                    </div>
                    <div class="toggle_mega_icon_<?php echo $the_index; ?>">
                    	<p class="size_12">You can add an icon to display next to your nav item. <a class="hero_open_icons hero_green" data-input-link="mega_icon_content_<?php echo $the_index; ?>" data-panel-toggle="close" data-load-link="hero_load_icons_<?php echo $the_index; ?>">Change Icon</a></p>
                        <div class="hero_col_12">
                        	<div class="hero_col_1">
                            	<div class="hero_selected_icon rounded_3 the_icon_<?php echo $the_index; ?>" data-trigger="hero_load_icons_<?php echo $the_index; ?>">
                                	<div id="hero_inner_icon"></div>
                                </div>
                            </div>
                            <div class="hero_col_4">                                  
                                <select data-size="lrg" id="mega_icon_size_<?php echo $the_index; ?>" name="mega_icon_size_<?php echo $the_index; ?>">
                                    <option value="xsmall" selected="selected">x-small</option>
                                    <option value="small">small</option>
                                    <option value="medium">medium</option>
                                    <option value="large">large</option>
                                </select>                                
                            </div>
                            <div class="hero_col_4">                                  
                                <input type="text" id="mega_icon_color_<?php echo $the_index; ?>" class="color_picker" name="mega_icon_color_<?php echo $the_index; ?>">
                                <input type="hidden" id="mega_icon_content_<?php echo $the_index; ?>" name="mega_icon_content_<?php echo $the_index; ?>">                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero_col_12 hero_item_content hero_bottom_line">
                <div class="hero_col_11">
                    <label class="size_12">Layout</label>
                    <div class="hero_layout_options" id="hero_options_<?php echo $the_index; ?>">
                        <div class="hero_selected_layout rounded_top_3"></div>
                        <div class="hero_option_items">
                            <div class="hero_12 rounded_3" data-id="hero_12" data-cols="1" data-idx="<?php echo $the_index; ?>" data-layout="12"></div>
                            <div class="hero_66 rounded_3" data-id="hero_66" data-cols="2" data-idx="<?php echo $the_index; ?>" data-layout="6,6"></div>
                            <div class="hero_84 rounded_3" data-id="hero_84" data-cols="2" data-idx="<?php echo $the_index; ?>" data-layout="8,4"></div>
                            <div class="hero_48 rounded_3" data-id="hero_48" data-cols="2" data-idx="<?php echo $the_index; ?>" data-layout="4,8"></div>
                            <div class="hero_444 rounded_3" data-id="hero_444" data-cols="3" data-idx="<?php echo $the_index; ?>" data-layout="4,4,4"></div>
                            <div class="hero_633 rounded_3" data-id="hero_633" data-cols="3" data-idx="<?php echo $the_index; ?>" data-layout="6,3,3"></div>
                            <div class="hero_336 rounded_3" data-id="hero_336" data-cols="3" data-idx="<?php echo $the_index; ?>" data-layout="3,3,6"></div>
                            <div class="hero_3333 rounded_3" data-id="hero_3333" data-cols="4" data-idx="<?php echo $the_index; ?>" data-layout="3,3,3,3"></div>
                            <div class="hero_22224 rounded_3" data-id="hero_22224" data-cols="5" data-idx="<?php echo $the_index; ?>" data-layout="2,2,2,2,4"></div>
                            <div class="hero_42222 rounded_3" data-id="hero_42222" data-cols="5" data-idx="<?php echo $the_index; ?>" data-layout="4,2,2,2,2"></div>                            
                            <div class="hero_222222 rounded_3" data-id="hero_222222" data-cols="6" data-idx="<?php echo $the_index; ?>" data-layout="2,2,2,2,2,2"></div>
                            <input type="hidden" style="width:60px;" id="mega_layout_<?php echo $the_index; ?>" name="mega_layout_<?php echo $the_index; ?>" data-change="hero_id_<?php echo $the_index; ?>">
                        </div>
                    </div>
                </div>
                <div class="hero_mega_playground rounded_playground_3 the_playground_<?php echo $the_index; ?>">
                    <div class="mega_col_holder mega_cols_<?php echo $the_index; ?>" id="hero_id_<?php echo $the_index; ?>">
                        <!-- LOAD IN COLS -->
                    </div>
                    <div class="hero_menu_col_options rounded_3 options_<?php echo $the_index; ?>" data-placement="">
                    	<div class="hero_close_options rounded_30"></div>
                        <ul class="size_11 hero_white">
                            <li class="hero_option_one" data-popup="post" data-main-index="<?php echo $the_index; ?>" data-mega-id="<?php echo $the_mega_id; ?>">Posts<div class="hero_mega_option_image"></div></li>
                            <li class="hero_option_two" data-popup="text" data-main-index="<?php echo $the_index; ?>" data-mega-id="<?php echo $the_mega_id; ?>">Text<div class="hero_mega_option_image"></div></li>
                            <li class="hero_option_three" data-popup="list" data-main-index="<?php echo $the_index; ?>" data-mega-id="<?php echo $the_mega_id; ?>">List<div class="hero_mega_option_image"></div></li>
                            <li class="hero_option_four" data-popup="contact" data-main-index="<?php echo $the_index; ?>" data-mega-id="<?php echo $the_mega_id; ?>">Contact/HTML<div class="hero_mega_option_image"></div></li>
                            <!--<li class="hero_option_five" data-popup="woo" data-main-index="<?php echo $the_index; ?>" data-mega-id="<?php echo $the_mega_id; ?>">Products<div class="hero_mega_option_image"></div></li>
                            <li class="hero_option_six" data-popup="slider" data-main-index="<?php echo $the_index; ?>" data-mega-id="<?php echo $the_mega_id; ?>">Slider<div class="hero_mega_option_image"></div></li>-->
                            <li class="hero_option_seven" data-popup="map" data-main-index="<?php echo $the_index; ?>" data-mega-id="<?php echo $the_mega_id; ?>">Map<div class="hero_mega_option_image"></div></li>
                            <li class="hero_option_eight" data-popup="images" data-main-index="<?php echo $the_index; ?>" data-mega-id="<?php echo $the_mega_id; ?>">Images<div class="hero_mega_option_image"></div></li>
                        </ul>
                    </div>
                </div>                
                <div class="hero_col_12">
                    <div class="hero_col_4">
                        <label><h2 class="size_14 hero_green">Nav item active</h2></label>
                        <div class="hero_switch_position"><input type="checkbox" data-size="sml" id="mega_nav_active_<?php echo $the_index; ?>" name="mega_nav_active_<?php echo $the_index; ?>" value="1"></div>
                    </div>
                    <div class="hero_col_4">
                        <label><h2 class="size_14 hero_green">Hide nav item on mobile</h2></label>
                        <div class="hero_switch_position"><input type="checkbox" data-size="sml" id="mega_mobile_active_<?php echo $the_index; ?>" name="mega_mobile_active_<?php echo $the_index; ?>" value="1"></div>
                    </div>
                </div>
            </div>
            <div class="hero_col_12">
            	<div class="hero_col_8">
                    <label>
                        <h2 class="size_18 hero_red weight_600">Background</h2>
                        <p class="size_12 hero_grey">Enable background for this mega menu.</p>
                    </label>
                </div>
                <div class="hero_col_4">
                    <input type="checkbox" data-size="lrg" id="mega_background_<?php echo $the_index; ?>" data-smltoggler="toggle_mega_background_<?php echo $the_index; ?>" name="mega_background_<?php echo $the_index; ?>" value="1" data-toggler="true">
                </div>
            </div>
            <div class="toggle_mega_background_<?php echo $the_index; ?>">
                <div class="hero_col_12">
                    <div class="hero_col_8">
                        <label><h2 class="size_14 hero_green">Background Position</h2></label>
                        <p class="size_12 hero_grey">Position your background.</p>
                    </div>
                    <div class="hero_col_4">
                        <select data-size="lrg" id="mega_background_position_<?php echo $the_index; ?>" name="mega_background_position_<?php echo $the_index; ?>">
                            <option value="center" selected="selected">Center</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="bottom right">Bottom, Right</option>
                            <option value="bottom left">Bottom, Left</option>
                            <option value="top right">Top, Right</option>
                            <option value="top left">Top, Left</option>                        
                        </select>  
                    </div>
                </div>
                <div class="hero_col_12">
                    <div class="hero_col_8">
                        <label><h2 class="size_14 hero_green">Background Url</h2></label>
                        <p class="size_12 hero_grey">Mega menu background url, use your own url if you like.</p>
                    </div>
                    <div class="hero_col_4">
                        <input type="text" data-size="lrg" data-hero_type="img" id="mega_background_url_<?php echo $the_index; ?>" name="mega_background_url_<?php echo $the_index; ?>" value="logo">
                    </div>
                </div>
                <div class="hero_col_12">
                    <div class="hero_col_8">
                        <div class="hero_button_auto green_button rounded_3 hero_media_uploader" data-connect-with="mega_background_url_<?php echo $the_index; ?>" data-multiple="false" data-size="full">Add background</div>
                    </div>
                </div>
            </div>
        </div>
    </li>
              
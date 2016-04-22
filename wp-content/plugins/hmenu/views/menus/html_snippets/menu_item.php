	<?php $the_index = $_GET['index']; ?>
    <?php $plugin_url = $_GET['url']; ?>
    <?php $nav_item_id = $_GET['navItemId']; ?>
    <?php $nav_parent_id = $_GET['parentId']; ?>
    <?php $nav_level = $_GET['lvl']; ?>
    <?php $the_type = $_GET['the_type']; ?>
    <li class="hero_sort_item" data-allow-sub="yes" data-index="<?php echo $the_index; ?>" id="hero_margin_left_<?php echo $nav_level; ?>" data-level="<?php echo $nav_level; ?>" data-id="<?php echo $nav_item_id; ?>" data-menu-type="<?php echo $the_type; ?>" data-parent="<?php echo $nav_parent_id; ?>">
		<div class="hero_item_wrap">
			<div class="hero_item_bar rounded_3 hero_bar_red">
				<div class="hero_item_toggle" data-nav-toggle="close"></div>
				<div class="hero_item_heading size_14 hero_white" id="ni_heading_<?php echo $the_index; ?>">
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
			<div class="hero_col_12 hero_item_content">
				<div class="hero_col_3">					
                    <label class="size_12">Label</label>
                    <input type="text" data-size="lrg" id="ni_name_<?php echo $the_index; ?>" name="ni_name_<?php echo $the_index; ?>">					
				</div>
				<div class="hero_col_3">					
                    <label class="size_12">Title attribute</label>
                    <input type="text" data-size="lrg" id="ni_alt_<?php echo $the_index; ?>" name="ni_alt_<?php echo $the_index; ?>">					
				</div>
                <?php
					if($the_type == 'method'){
				?>   
                <div class="hero_col_3">					
                    <label class="size_12">Javascript method</label>
                    <input type="text" data-size="lrg" id="ni_event_function_<?php echo $the_index; ?>" name="ni_event_function_<?php echo $the_index; ?>">					
				</div> 
                <?php 
					}
				?>
				<?php
					if($the_type == 'custom'){
				?>
                    <div class="hero_col_3">					
                        <label class="size_12">URL</label><br>
                        <input type="text" data-size="lrg" id="ni_url_<?php echo $the_index; ?>" name="ni_url_<?php echo $the_index; ?>">					
                    </div>
                <?php 
					}
				?>
                <?php
					if($the_type != 'method'){
				?>
				<div class="hero_col_3">					                                  	
                    <label class="size_12">Target</label>
                    <select data-size="lrg" id="ni_target_<?php echo $the_index; ?>" name="ni_target_<?php echo $the_index; ?>">
                        <option value="_blank">New Page</option>
                        <option value="_self">Same Window</option>
                    </select>					
				</div>
                <?php 
					}
				?>
                <?php
					if($the_type == 'custom'){
				?>
                    <div class="hero_col_6" style="float:right">                    
                    	<p class="size_11 hero_grey">http://www.example.com</p>
                    </div>
                <?php 
					}
				?>
                <div class="hero_col_12 hero_bottom_line">
                    <div class="hero_col_4">
                        <label><h2 class="size_14 hero_green">Icon</h2></label>
                        <div class="hero_switch_position"><input type="checkbox" data-size="sml" data-smltoggler="toggle_ni_icon_<?php echo $the_index; ?>" id="ni_icon_<?php echo $the_index; ?>" name="ni_icon_<?php echo $the_index; ?>" value="1"></div>
                    </div>
                    <div class="toggle_ni_icon_<?php echo $the_index; ?>">
                    	<p class="size_12">You can add an icon to display next to your nav item. <a class="hero_open_icons hero_green" data-input-link="ni_icon_content_<?php echo $the_index; ?>" data-panel-toggle="close" data-load-link="hero_load_icons_<?php echo $the_index; ?>">Change Icon</a></p>
                        <div class="hero_col_12">
                        	<div class="hero_col_1">
                            	<div class="hero_selected_icon rounded_3 the_icon_<?php echo $the_index; ?>" data-trigger="hero_load_icons_<?php echo $the_index; ?>">
                                	<div id="hero_inner_icon"></div>
                                </div>
                            </div>
                            <div class="hero_col_4">                                  
                                <select data-size="lrg" id="ni_icon_size_<?php echo $the_index; ?>" name="ni_icon_size_<?php echo $the_index; ?>">
                                    <option value="xsmall" selected="selected">x-small</option>
                                    <option value="small">small</option>
                                    <option value="medium">medium</option>
                                    <option value="large">large</option>
                                </select>                                
                            </div>
                            <div class="hero_col_4">                                  
                                <input type="text" id="ni_icon_color_<?php echo $the_index; ?>" class="color_picker" name="ni_icon_color_<?php echo $the_index; ?>">
                                <input type="hidden" id="ni_icon_content_<?php echo $the_index; ?>" name="ni_icon_content_<?php echo $the_index; ?>">                                
                            </div>
                        </div>
                        <!--<div class="hero_load_icons rounded_3" id="hero_load_icons_<?php echo $the_index; ?>">
                        	
                        </div>-->
                    </div>
                </div>   
				<!--<div class="hero_col_12">
                    <div class="hero_col_4">
                        <label><h2 class="size_14 hero_green">Nav item active</h2></label>
                        <div class="hero_switch_position"><input type="checkbox" data-size="sml" id="ni_nav_active_<?php echo $the_index; ?>" name="ni_nav_active_<?php echo $the_index; ?>" value="1"></div>
                    </div>
                    <div class="hero_col_6">
                        <label><h2 class="size_14 hero_green">Hide nav item on mobile</h2></label>
                        <div class="hero_switch_position"><input type="checkbox" data-size="sml" id="ni_mobile_active_<?php echo $the_index; ?>" name="ni_mobile_active_<?php echo $the_index; ?>" value="1"></div>
                    </div>
                </div>-->
			</div>
		</div>
		<ul class="transfer_items not_sortable"></ul>
	</li>
              
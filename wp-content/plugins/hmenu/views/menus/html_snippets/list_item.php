	<?php $the_index = $_GET['index']; ?>
    <?php $main_index = $_GET['mainIndex']; ?>
    <?php $content_index = $_GET['contentIndex']; ?>
    <?php $plugin_url = $_GET['url']; ?>
    <?php $list_item_id = $_GET['listId']; ?>
    <li class="hero_list_sort_item" data-index="<?php echo $the_index; ?>" data-main-index="<?php echo $main_index; ?>" data-content-index="<?php echo $content_index; ?>" data-id="<?php echo $list_item_id; ?>" data-menu-type="basic">
		<div class="hero_item_wrap">
			<div class="hero_item_bar rounded_3 hero_bar_red">
				<div class="hero_item_toggle" data-nav-toggle="close"></div>
				<div class="hero_item_heading size_14 hero_white" id="list_heading_<?php echo $the_index; ?>">
					Menu Name
				</div>
                <input type="hidden" style="width:60px;" id="list_item_order_<?php echo $the_index; ?>" name="list_item_order_<?php echo $the_index; ?>">
				<div class="hero_item_edits_holder">
                	<div class="hero_nav_type rounded_30 size_10" id="item_type_<?php echo $the_index; ?>">post</div><!---->
					<div class="hero_edits rounded_20">
						<div class="hero_edit_item hero_button_edit" style="background-image:url(<?php echo $plugin_url; ?>/assets/images/admin/edit_icon.png)"></div>
                        <div class="hero_edit_item hero_button_delete hero_delete_list" data-main-index="<?php echo $main_index; ?>" data-content-index="<?php echo $content_index; ?>" data-id="<?php echo $the_index; ?>" style="background-image:url(<?php echo $plugin_url; ?>/assets/images/admin/delete_icon.png)"></div>
                    </div>
					<div class="hero_item_drag"></div>
				</div>
			</div>
			<div class="hero_col_12 hero_item_content">
				<div class="hero_col_3">
                    <label class="size_12">Label</label>
                    <input type="text" data-size="lrg" id="list_name_<?php echo $the_index; ?>" name="list_name_<?php echo $the_index; ?>">					
				</div>
				<div class="hero_col_3">					
                    <label class="size_12">Title attribute</label>
                    <input type="text" data-size="lrg" id="list_alt_<?php echo $the_index; ?>" name="list_alt_<?php echo $the_index; ?>">					
				</div>
				<div class="hero_col_3 the_list_input_<?php echo $the_index; ?>">					
                    <label class="size_12">URL</label><br>
                    <input type="text" data-size="lrg" id="list_url_<?php echo $the_index; ?>" name="list_url_<?php echo $the_index; ?>">					
				</div>
				<div class="hero_col_3">					                                    	
                    <label class="size_12">Target</label>
                    <select data-size="lrg" id="list_target_<?php echo $the_index; ?>" name="list_target_<?php echo $the_index; ?>">
                        <option value="_blank">New Page</option>
                        <option value="_self">Same Window</option>
                    </select>					
				</div>
                <div class="hero_col_12">
                    <div class="hero_col_2">
                        <label><h2 class="size_14 hero_green">Icon</h2></label>
                        <div class="hero_switch_position"><input type="checkbox" data-size="sml" data-smltoggler="toggle_list_icon_<?php echo $the_index; ?>" id="list_icon_<?php echo $the_index; ?>" name="list_icon_<?php echo $the_index; ?>" value="1"></div>
                    </div>
                    <div class="hero_col_3">
                        <label><h2 class="size_14 hero_green">Description</h2></label>
                        <div class="hero_switch_position"><input type="checkbox" data-size="sml" data-smltoggler="toggle_desc_icon_<?php echo $the_index; ?>" id="list_desc_<?php echo $the_index; ?>" name="list_desc_<?php echo $the_index; ?>" value="1"></div>
                    </div>
                    <div class="toggle_list_icon_<?php echo $the_index; ?>">
                        <div class="hero_col_12">
                            <p class="size_12">You can add an icon to display next to your nav item. <a class="hero_open_icons hero_green" data-input-link="list_icon_content_<?php echo $the_index; ?>" data-panel-toggle="close" data-load-link="list_icon_content_<?php echo $the_index; ?>">Change Icon</a></p>
                            <div class="hero_col_12">
                                <div class="hero_col_1">
                                    <div class="hero_selected_icon rounded_3 the_list_icon_<?php echo $the_index; ?>">
                                        <div id="hero_inner_icon"></div>
                                    </div>
                                </div>
                                <div class="hero_col_4">                                  
                                    <select data-size="lrg" id="list_icon_size_<?php echo $the_index; ?>" name="list_icon_size_<?php echo $the_index; ?>">
                                        <option value="xsmall" selected="selected">x-small</option>
                                        <option value="small">small</option>
                                        <option value="medium">medium</option>
                                        <option value="large">large</option>
                                    </select>
                                    <input type="hidden" id="list_icon_content_<?php echo $the_index; ?>" name="list_icon_content_<?php echo $the_index; ?>" value="icon_none">                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="toggle_desc_icon_<?php echo $the_index; ?>">
                    <p class="size_12">Description: Add a short description to your list item.</p>
                    <div class="hero_col_12">
                    	<textarea data-size="lrg" id="list_description_<?php echo $the_index; ?>" name="list_description_<?php echo $the_index; ?>" rows="2"></textarea> 
                    </div>                  
                </div>
			</div>
		</div>
		<ul class="transfer_items"></ul>
	</li>
              
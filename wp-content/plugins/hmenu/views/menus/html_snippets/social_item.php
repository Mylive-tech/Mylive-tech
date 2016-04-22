	<?php $the_index = $_GET['index']; ?>
    <?php $plugin_url = $_GET['url']; ?>
    <?php $the_icon_class = $_GET['class']; ?>
    <li class="hero_list_sort_item" data-index="<?php echo $the_index; ?>" id="<?php echo $the_icon_class; ?>">
		<div class="hero_item_wrap">
			<div class="hero_item_bar rounded_3 hero_bar_red">
				<div class="hero_item_toggle" data-nav-toggle="close"></div>
				<div class="hero_item_heading size_14 hero_white" id="social_heading_<?php echo $the_index; ?>">
					Menu Name
				</div>
                <div class="hero_social_icon hero_social_icon_display_<?php echo $the_index; ?>">
                	<div id="inner_icon"></div>
                </div>
                <input type="hidden" style="width:60px;" id="social_item_order_<?php echo $the_index; ?>" name="social_item_order_<?php echo $the_index; ?>">
                <input type="hidden" id="social_icon_content_<?php echo $the_index; ?>" name="social_icon_content_<?php echo $the_index; ?>">
				<div class="hero_item_edits_holder">
                	<div class="hero_nav_type rounded_30 size_10" id="item_type_<?php echo $the_index; ?>">social</div>
					<div class="hero_edits rounded_20">
						<div class="hero_edit_item hero_button_edit" style="background-image:url(<?php echo $plugin_url; ?>/assets/images/admin/edit_icon.png)"></div>
                        <div class="hero_edit_item hero_button_delete hero_delete_social" data-index="<?php echo $the_index; ?>" style="background-image:url(<?php echo $plugin_url; ?>/assets/images/admin/delete_icon.png)"></div>
                    </div>
					<div class="hero_item_drag"></div>
				</div>
			</div>
			<div class="hero_col_12 hero_item_content">
				<div class="hero_col_3">					
                    <label class="size_12">Label</label>
                    <input type="text" data-size="lrg" id="social_name_<?php echo $the_index; ?>" name="list_name_<?php echo $the_index; ?>">					
				</div>
				<div class="hero_col_2">					                                    	
                    <label class="size_12">Target</label>
                    <select data-size="lrg" id="social_target_<?php echo $the_index; ?>" name="social_target_<?php echo $the_index; ?>">
                        <option value="_blank">New Page</option>
                        <option value="_self">Same Window</option>
                    </select>					
				</div>
                <div class="hero_col_3">                        
                    <label class="size_12">Icon size</label><br> 
                    <select data-size="lrg" id="social_icon_size_<?php echo $the_index; ?>" name="social_icon_size_<?php echo $the_index; ?>">
                        <option value="xsmall" selected="selected">x-small</option>
                        <option value="small">small</option>
                        <option value="medium">medium</option>
                        <option value="large">large</option>
                    </select>                        
                </div>
                <div class="hero_col_2">                        
                    <label class="size_12">Icon color</label><br>  
                    <input type="text" id="social_icon_color_<?php echo $the_index; ?>" class="color_picker" name="social_icon_color_<?php echo $the_index; ?>">                        
                </div>
                <div class="hero_col_2">                         
                    <label class="size_12">Icon hover color</label><br> 
                    <input type="text" id="social_icon_hover_color_<?php echo $the_index; ?>" class="color_picker" name="social_icon_hover_color_<?php echo $the_index; ?>">                        
                </div>
                <div class="hero_col_12">					
                    <label class="size_12">URL</label><br>
                    <input type="text" data-size="lrg" id="social_url_<?php echo $the_index; ?>" name="social_url_<?php echo $the_index; ?>">					
				</div>
			</div>
		</div>
		<ul class="transfer_items"></ul>
	</li>
              
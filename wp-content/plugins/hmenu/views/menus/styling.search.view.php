<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/styling.search.view.js"></script>
<div class="hero_views">
    <div class="hero_col_12">
    	<h2 class="hero_red size_18 weight_600">
            Search field<br />
            <strong class="size_11 hero_grey">Search field options.</strong>
        </h2>
        <!-- START: FORM -->
            <form>  
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<label><h2 class="size_14 hero_green">Enable navigation search</h2></label><div class="hero_switch_position"><input type="checkbox" data-size="sml" id="search_active" name="search_active" value="1"></div>
                        <p class="size_12 hero_grey">Enable the search field for use in your navigation.</p>   
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">                        
                        <div class="hero_col_12">
                            <label>
                                <h2 class="size_18 hero_red weight_600">Search Type</h2>
                                <p class="size_12 hero_grey">Select the type of search field.</p>
                            </label>
                        </div>
                        <div class="hero_col_12">
                        	<div class="hero_col_4">
                            	<label><h2 class="size_14 hero_green">Classic search</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="search_classic" name="search_type" value="classic" data-toggleimage="true"></div>
                                <div class="search_type img_search_classic image_search_classic"></div>
                            </div>
                        	<div class="hero_col_4">
                            	<label><h2 class="size_14 hero_green">Lightbox search</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="search_slide" name="search_type" value="slide" data-toggleimage="true"></div>
                                <div class="search_type img_search_slide image_search_slide"></div>
                            </div>
                        	<div class="hero_col_4">
                            	<label><h2 class="size_14 hero_green">Full width search</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="search_full" name="search_type" value="full" data-toggleimage="true"></div>
                                <div class="search_type img_search_full image_search_full"></div>
                            </div>
                        </div>  
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Search Placeholder Text</h2>
                            <p class="size_12 hero_grey">Set the placeholder text for the search input field.</p>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_4">
                             	<label>Placeholder Text</label>
                            	<input type="text" data-size="lrg" id="search_placeholder" name="search_placeholder">
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->   
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Search Dimensions</h2>
                            <p class="size_12 hero_grey">This is the width of the search field.</p>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                            	<label>Width</label>
                                <input type="text" data-size="lrg" data-hero_type="px" id="search_width" name="search_width">
                            </div>
                            <div class="hero_col_2">
                            	<label>Height</label>
                                <input type="text" data-size="lrg" data-hero_type="px" id="search_height" name="search_height">
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->  
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Background</h2>
                            <p class="size_12 hero_grey">Set the background color of the input field, if set to lightbox this will automatically set the background color of the lightbox.</p>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                             	<label>Background Color</label>
                            	<input type="text" id="backgroundColor" class="color_picker" name="backgroundColor" value="#FFFFFF">
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Fonts</h2>
                        </div>  
                    	<label><h2 class="size_14 hero_green">Text size and color</h2></label>
                        <p class="size_12 hero_grey">This will style the search field.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                             	<label>Font Family</label>
                            	<select data-size="lrg" id="fontFamily" name="fontFamily" data-height="200">
                                    <option value="arial">Arial</option>
                                    <option value="times">Times New Roman</option>
                                    <option value="chiller">Chiller</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font Weight</label>
                            	<select data-size="lrg" id="fontWeight" name="fontWeight">
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Lighter</option>
                                    <option value="inherit">Inherit</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font Size</label>
                            	<input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="fontSize" name="fontSize">
                            </div>
                             <div class="hero_col_2">
                             	<label>Font Sizing</label>
                            	<select data-size="lrg" id="fontSizing" name="fontSizing">
                                    <option>px</option>
                                    <option>em</option>
                                </select>
                            </div>
                            <div class="hero_col_2">
                             	<label>Font Color</label>
                            	<input type="text" id="fontColor" class="color_picker" name="fontColor" value="#DC4551">
                            </div>
                        </div> 
                        <label><h2 class="size_14 hero_green">Search icon size</h2></label>
                        <p class="size_12 hero_grey">This will define the size for your search icon.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                                <select data-size="lrg" id="iconSize" name="iconSize">
                                    <option value="xsmall" selected="selected">x-small</option>
                                    <option value="small">small</option>
                                    <option value="medium">medium</option>
                                    <option value="large">large</option>
                                </select> 
                            </div>
                        </div>                   
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                        <div class="hero_col_6">
                            <label>
                                <h2 class="size_18 hero_red weight_600">Padding</h2>
                                <p class="size_12 hero_grey">This will add padding to the search field.</p>
                            </label>
                            <div class="hero_col_12">
                                <div class="hero_col_3">
                                	<label>Left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingLeft" name="paddingLeft" class="padding">
                                </div>
                                <div class="hero_col_3">
                                	<label>Right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingRight" name="paddingRight" class="padding">
                                </div>
                                <div class="hero_col_3">
                                	<label>Top</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingTop" name="paddingTop" class="padding">
                                </div>
                                <div class="hero_col_3">
                                	<label>Bottom</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingBottom" name="paddingBottom" class="padding">
                                </div>
                            </div>  
                        </div>
                        <div class="hero_col_6">
                            <div class="hero_search_padding_example"></div>
                        </div>                                              
                    </div>
                <!-- END: ACTIVATION -->    
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">                        
                        <div class="hero_section_toggle" data-toggle-section="main_responsive">
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Border Arround Search Field</h2>
                                        <p class="size_12 hero_grey">This will add a border to your search field, if you select lightbox, this will automatically set the border below the text field.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="border" name="border" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Transparency</label>
                                    <select data-size="lrg" id="borderTransparency" name="borderTransparency">
                                        <option value="0.1">10%</option>
                                        <option value="0.2">20%</option>
                                        <option value="0.3">30%</option>
                                        <option value="0.4">40%</option>
                                        <option value="0.5">50%</option>
                                        <option value="0.6">60%</option>
                                        <option value="0.7">70%</option>
                                        <option value="0.8">80%</option>
                                        <option value="0.9">90%</option>
                                        <option value="1.0">100%</option>
                                    </select>
                                </div>
                                <div class="hero_col_2">
                                    <label>Border color</label>
                                    <input type="text" id="borderColor" class="color_picker" name="borderColor" value="#DC4551">
                                </div>
                            </div>
                            <div class="hero_col_12">
                            	<label class="hero_col_12">
                                    <h2 class="size_14 hero_green">Border radius</h2>
                                </label>
                                <p class="size_12 hero_grey">This will allow you to add rounded corners to your search field, automatically disabled if lightbox is selected.</p>
                                <div class="hero_col_2">
                                    <label>Top Left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_top" class="border_radius" name="border_radius_top">
                                </div>
                                <div class="hero_col_2">
                                    <label>Top Right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_top_right" class="border_radius" name="border_radius_top_right">
                                </div>
                                <div class="hero_col_2">
                                    <label>Bottom Right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_bottom_right" class="border_radius" name="border_radius_bottom_right">
                                </div>
                                <div class="hero_col_2">
                                    <label>Bottom Left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_bottom_left" class="border_radius" name="border_radius_bottom_left">
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->      
            </form>
        <!-- END: FORM -->
    </div>
</div>
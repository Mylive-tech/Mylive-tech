<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/styling.main.view.js"></script>
<script type="text/javascript">
	
	//load
	jQuery(function(){
		console_log("LOAD: " + plugin_url);		
	});
	
</script>
<div class="hero_views">
    <div class="hero_col_12">
    	<h2 class="hero_red size_18 weight_600">
            Menu bar dimensions<br />
            <strong class="size_11 hero_grey">Choose between a navigation bar that runs the full width of the screen, or at a fixed width. (Bound by theme container)</strong>
        </h2>
        <!-- START: FORM -->
            <form>      	
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                        <div class="hero_col_4">
                            <label><h2 class="size_14 hero_green">Full width</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="menu_dimension_full" name="menu_dimentions" value="full" data-toggleimage="true"></div>
                            <div class="search_type menu_full_width image_menu_dimension_full"></div>
                        </div>
                        <div class="hero_col_4">
                            <label><h2 class="size_14 hero_green">Fixed width</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="menu_dimension_fixed" name="menu_dimentions" value="fixed" data-toggleimage="true"></div>
                            <div class="search_type menu_fixed_width image_menu_dimension_fixed"></div>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                            	<label>Menu width</label>
                            	<input type="text" data-size="lrg" data-hero_type="px" id="menuBarWidth" name="menuBarWidth" value="100%">
                            </div>
                            <div class="hero_col_3">
                            	<label>Menu height</label>
                            	<input type="text" data-size="lrg" data-hero_type="px" id="menuBarHeight" name="menuBarHeight" value="100px">
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Navigation width</h2>
                            <p class="size_12 hero_grey">Choose between navigation items that run the full width of the menu bar as indicated above, or at a fixed width in the middle of the menu bar.</p>
                        </div>                   	
                        <div class="hero_col_4">
                            <label><h2 class="size_14 hero_green">Full width</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="nav_dimension_full" name="nav_dimentions" value="full" data-toggleimage="true"></div>
                            <div class="search_type nav_full_width image_nav_dimension_full" id="nav_change_one"></div>
                        </div>
                        <div class="hero_col_4">
                            <label><h2 class="size_14 hero_green">Fixed width</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="nav_dimension_fixed" name="nav_dimentions" value="fixed" data-toggleimage="true"></div>
                            <div class="search_type nav_fixed_width image_nav_dimension_fixed" id="nav_change_two"></div>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                            	<label>Navigation width</label>
                            	<input type="text" data-size="lrg" data-hero_type="px" id="navBarWidth" name="navBarWidth" value="100%">
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->               
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_section_toggle" data-toggle-section="main_responsive">                       
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Color Presets</h2>
                                        <p class="size_12 hero_grey">Use preset colors if you dont want to use your own. This will filter down to the subnavigation as well. This will affect all background styling and text coloring.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="preset" name="preset" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_preset_holder">
                                    <!-- LOAD PRESETS -->
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <h2 class="size_14 hero_green">Current selection</h2>
                                <p class="size_12 hero_grey hero_preset_selection">Dark purple navigation with light purple hover and white text.</p>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Background color</h2>
                        </div>                   	
                        <label><h2 class="size_14 hero_green">Default background color | Gradients</h2></label><div class="hero_switch_position"><input type="checkbox" data-size="sml" id="bgMenuGradient" name="bgMenuGradient" value="1" data-smltoggler="menuGradientToggle"></div>
                        <p class="size_12 hero_grey">This will be the menu background color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                                <label>Start color</label>
                            	<input type="text" id="bgMenuStartColor" class="color_picker" name="bgMenuStartColor" value="#DC4551">
                            </div>
                            <div class="menuGradientToggle">
                                <div class="hero_col_2">
                                	<label>End color</label>
                                    <input type="text" id="bgMenuEndColor" class="color_picker" name="bgMenuEndColor" value="#DC4551">
                                </div>
                                <div class="hero_col_3">                                
                                	<label>Gradient direction</label>
                                    <select data-size="lrg" id="bgMenuGradientPath" name="bgMenuGradientPath">
                                        <option value="vertical">Vertical</option>
                                        <option value="horizontal">Horizontal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hero_col_2">
                                <label>Transparency</label>
                            	<select data-size="lrg" id="bgMenuTransparency" name="bgMenuTransparency">
                                 	<option value="0.0">0%</option>
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
                        </div>                      
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">    
                    	<div class="hero_section_toggle" data-toggle-section="main_responsive">                    
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Background image</h2>
                                        <p class="size_12 hero_grey">This will add a background image to your main navigation.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="bgMainImage" name="bgMainImage" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <div class="hero_button_auto green_button rounded_3 hero_media_uploader" data-connect-with="bgMainImageUrl" data-multiple="false" data-size="full">Add background image</div>
                                </div>
                                <div class="hero_col_4">
                                    <div class="hero_main_background rounded_3"></div>
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<label><h2 class="size_14 hero_green">Background image url</h2></label>
                        			<p class="size_12 hero_grey">This is the source url for your background image.</p>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="img" id="bgMainImageUrl" name="bgMainImageUrl" value="#">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<label><h2 class="size_14 hero_green">Background position</h2></label>
                                    <p class="size_12 hero_grey">Set the position for your main navigation background image.</p>
                                </div>
                                <div class="hero_col_4">
                                    <select data-size="lrg" id="bgMainImagePosition" name="bgMainImagePosition">
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
                            		<label><h2 class="size_14 hero_green">Background repeat</h2></label>
                                    <p class="size_12 hero_grey">Set the repeat for your main navigation background image.</p>
                                </div>
                                <div class="hero_col_4">
                                    <select data-size="lrg" id="bgMainImageRepeat" name="bgMainImageRepeat">
                                        <option value="inherit">Inherit</option>
                                        <option value="no-repeat">No Repeat</option>   
                                        <option value="repeat">Repeat</option>       
                                        <option value="repeat-x">Repeat X</option>   
                                        <option value="repeat-y">Repeat Y</option>           
                                    </select> 
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Hover type and color</h2>
                        </div>  
                        <div class="hero_col_12">
                        	<div class="hero_col_4">
                            	<label><h2 class="size_14 hero_green">Background hover</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="nav_hover_background" name="nav_hover_type" value="background" data-toggleimage="true"></div>
                                <div class="search_type hover_bg image_nav_hover_background"></div>
                            </div>
                        	<div class="hero_col_4">
                            	<label><h2 class="size_14 hero_green">Underline</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="nav_hover_underline" name="nav_hover_type" value="underline" data-toggleimage="true"></div>
                                <div class="search_type hover_underline image_nav_hover_underline"></div>
                            </div>
                        	<div class="hero_col_4">
                            	<label><h2 class="size_14 hero_green">Border</h2></label><div class="hero_switch_position"><input type="radio" data-size="sml" id="nav_hover_border" name="nav_hover_type" value="border" data-toggleimage="true"></div>
                                <div class="search_type hover_border image_nav_hover_border"></div>
                            </div>
                        </div>                        
                        <label>
                            <h2 class="size_14 hero_green hero_main_text">
                            	Hover background color | Gradients
                            </h2>
                        </label>
                        <div class="hero_switch_position hero_main_gradient_toggle"><input type="checkbox" data-size="sml" id="bgHoverGradient" name="bgHoverGradient" value="1" data-smltoggler="menuHoverGradientToggle"></div>
                        <p class="size_12 hero_grey hero_sub_text">This will be the menu text hover color and size.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                            	<label>Start color</label>
                            	<input type="text" id="bgHoverStartColor" class="color_picker" name="bgHoverStartColor" value="#DC4551">
                            </div>
                            <div class="menuHoverGradientToggle hero_main_gradient_display">
                                <div class="hero_col_2">
                                	<label>End color</label>
                                    <input type="text" id="bgHoverEndColor" class="color_picker" name="bgHoverEndColor" value="#DC4551">
                                </div>
                                <div class="hero_col_3">
                                	<label>Gradient direction</label>
                                    <select data-size="lrg" id="bgHoverGradientPath" name="bgHoverGradientPath">
                                        <option value="vertical">Vertical</option>
                                        <option value="horizontal">Horizontal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hero_col_2">
                            	<label>Transparency</label>
                            	<select data-size="lrg" id="bgHoverTransparency" name="bgHoverTransparency">
                                	<option value="0.0">0%</option>
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
                        </div>
                    </div>
                <!-- END: ACTIVATION -->
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">                        
                        <div class="hero_section_toggle" data-toggle-section="main_responsive">
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Border Arround Menu Bar</h2>
                                        <p class="size_12 hero_grey">This will add a border to your navigation bar.</p>
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
                                <div class="hero_col_4">
                                	<label>Border type</label>
                                    <select data-size="lrg" id="borderType" name="borderType">
                                        <option value="border-top">Border top</option>
                                        <option value="border-bottom">Border bottom</option>
                                        <option value="border-left">Border left</option>
                                        <option value="border-right">Border right</option>
                                        <option value="border">Border arround</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hero_col_12">
                            	<label class="hero_col_12">
                                    <h2 class="size_14 hero_green">Border radius</h2>
                                </label>
                                <p class="size_12 hero_grey">This will allow you to add rounded corners to your menu.</p>
                                <div class="hero_col_2">
                                    <label>Top left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_top" class="ms_border_radius" name="border_radius_top">
                                </div>
                                <div class="hero_col_2">
                                    <label>Top right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_top_right" class="ms_border_radius" name="border_radius_top_right">
                                </div>
                                <div class="hero_col_2">
                                    <label>Bottom right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_bottom_right" class="ms_border_radius" name="border_radius_bottom_right">
                                </div>
                                <div class="hero_col_2">
                                    <label>Bottom left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_bottom_left" class="ms_border_radius" name="border_radius_bottom_left">
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->   
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Fonts</h2>
                        </div>  
                    	<label><h2 class="size_14 hero_green">Font size and color</h2></label>
                        <p class="size_12 hero_grey">This will style the main navigation items.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                             	<label>Font family</label>
                            	<select data-size="lrg" id="fontFamily" name="fontFamily" data-height="200">
                                    <option value="arial">Arial</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font weight</label>
                            	<select data-size="lrg" id="fontWeight" name="fontWeight">
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Lighter</option>
                                    <option value="inherit">Inherit</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font size</label>
                            	<input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="fontSize" name="fontSize">
                            </div>
                             <div class="hero_col_2">
                             	<label>Font sizing</label>
                            	<select data-size="lrg" id="fontSizing" name="fontSizing">
                                    <option>px</option>
                                    <option>em</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font color</label>
                            	<input type="text" id="fontColor" class="color_picker" name="fontColor" value="#DC4551">
                            </div>
                        </div>
                        <label><h2 class="size_14 hero_green">Hover font color</h2></label>
                        <p class="size_12 hero_grey">This will style the main navigation hover text color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                            	<label>Font hover color</label>
                            	<input type="text" id="fontHoverColor" class="color_picker" name="fontHoverColor" value="#DC4551">
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                        <div class="hero_col_6">
                            <label>
                                <h2 class="size_18 hero_red weight_600">Padding</h2>
                                <p class="size_12 hero_grey">This will add padding to the left and right of each nav item.</p>
                            </label>
                            <div class="hero_col_12">
                                <div class="hero_col_3">
                                	<label>Left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingLeft" name="paddingLeft">
                                </div>
                                <div class="hero_col_3">
                                	<label>Right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingRight" name="paddingRight">
                                </div>
                            </div>  
                        </div>
                        <div class="hero_col_6">
                            <div class="hero_padding_example"></div>
                        </div>                                              
                    </div>
                <!-- END: ACTIVATION -->  
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">    
                    	<div class="hero_section_toggle" data-toggle-section="main_responsive">                    
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Menu Shadow</h2>
                                        <p class="size_12 hero_grey">This will add a shadow to your menu bar. (Not supported in older browsers)</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="shadow" name="shadow" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Shadow distance</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="shadow_distance" name="shadow_distance" class="ms_shadow_radius">
                                </div>
                                <div class="hero_col_2">
                                    <label>Shadow blur</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="shadow_blur" name="shadow_blur" class="ms_shadow_radius">
                                </div>
                                <div class="hero_col_2">
                                    <label>Transparency</label>
                                    <select data-size="lrg" id="shadowTransparency" name="shadowTransparency">
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
                                    <label>Color</label>
                                    <input type="text" id="shadowColor" class="color_picker" name="shadowColor" value="#DC4551">
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_col_12">
                            <div class="hero_col_8">
                                <label>
                                    <h2 class="size_18 hero_red weight_600">Logo url settings</h2>
                                    <p class="size_12 hero_grey">Set the hyperlink/url and target for your logo. Default hyperlink/url will link to home page.</p>
                                </label>
                            </div>
                            <div class="hero_col_4">
                                
                            </div>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_8">
                                <label>
                                    <h2 class="size_14 hero_green">Logo url</h2>
                                    <p class="size_12 hero_grey">Set the hyperlink/url for your logo.</p>
                                </label>
                            </div>
                            <div class="hero_col_4">
                                <input type="text" data-size="lrg" id="logoLink" name="logoLink" >
                            </div>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_8">
                                <label>
                                    <h2 class="size_14 hero_green">Logo alt</h2>
                                    <p class="size_12 hero_grey">Set alt attribute for the logo.</p>
                                </label>
                            </div>
                            <div class="hero_col_4">
                                <input type="text" data-size="lrg" id="logoAlt" name="logoAlt" >
                            </div>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_8">
                                <label>
                                    <h2 class="size_14 hero_green">Logo url target</h2>
                                    <p class="size_12 hero_grey">Set the target for your logo url.</p>
                                </label>
                            </div>
                            <div class="hero_col_4">
                                <select data-size="lrg" id="logoLinkTarget" name="logoLinkTarget">
                                    <option value="_blank">New Page</option>
                                    <option value="_self">Same Window</option>
                                </select>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">                        
                        <div class="hero_section_toggle" data-toggle-section="main_responsive">
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Main logo</h2>
                                        <p class="size_12 hero_grey">This is the logo that will be displayed on your navigation bar. For best results, use a png.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="logo" name="logo" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<div class="hero_button_auto green_button rounded_3 hero_media_uploader" data-connect-with="logoUrl" data-multiple="false">Add logo</div>
                                </div>
                                <div class="hero_col_4">
                                    <div class="hero_main_logo rounded_3"></div>
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<label><h2 class="size_14 hero_green">Logo source url</h2></label>
                        			<p class="size_12 hero_grey">This is the source url for your logo, you can also make use of your own url.</p>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="img" id="logoUrl" name="logoUrl" value="#">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<label><h2 class="size_14 hero_green">Max logo height (%)</h2></label>
                        			<p class="size_12 hero_grey">This determines the height of your logo in percentage (%)</p>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="perc" id="logoHeight" name="logoHeight">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<label><h2 class="size_14 hero_green">Logo padding left</h2></label>
                        			<p class="size_12 hero_grey">This adds padding to your logo on the left side.</p>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="px" id="logoPaddingLeft" name="logoPaddingLeft">
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">                        
                        <div class="hero_section_toggle" data-toggle-section="main_responsive">
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Mobile logo</h2>
                                        <p class="size_12 hero_grey">This is the logo for you mobile device. For best results, use a png.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="mobileLogo" name="mobileLogo" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<div class="hero_button_auto green_button rounded_3 hero_media_uploader" data-connect-with="mobileLogoUrl" data-multiple="false">Add logo</div>
                                </div>
                                <div class="hero_col_4">
                                    <div class="hero_mobile_logo rounded_3"></div>
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<label><h2 class="size_14 hero_green">Logo url</h2></label>
                        			<p class="size_12 hero_grey">This is the source url for your mobile logo, you can also make use of your own url.</p>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="img" id="mobileLogoUrl" name="mobileLogoUrl" value="#">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<label><h2 class="size_14 hero_green">Max mobile logo height (%)</h2></label>
                        			<p class="size_12 hero_grey">This determines the height of your mobile logo in percentage (%)</p>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="perc" id="mobileLogoHeight" name="mobileLogoHeight">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<label><h2 class="size_14 hero_green">Mobile logo padding left</h2></label>
                        			<p class="size_12 hero_grey">This adds padding to your mobile logo on the left side.</p>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="px" id="mobileLogoPaddingLeft" name="mobileLogoPaddingLeft">
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->      
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_section_toggle" data-toggle-section="main_responsive">
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Sticky menu</h2>
                                        <p class="size_12 hero_grey">This enables your menu bar to be sticky. Also known as fixed navigation.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="sticky" name="sticky" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_14 hero_green">Sticky menu height</h2>
                                        <p class="size_12 hero_grey">This will be the height of the menu bar when you scroll.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="px" id="stickyHeight" name="stickyHeight">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_14 hero_green">Activation distance</h2>
                                        <p class="size_12 hero_grey">Determines the distance needed to scroll before sticky menu is activated.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="px" id="stickyActivate" name="stickyActivate">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_14 hero_green">Sticky Menu Transparency</h2>
                                        <p class="size_12 hero_grey">Set the transparency of your sticky menu.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <select data-size="med" id="stickyTransparency" name="stickyTransparency">
                                    	<option value="0.0">0%</option>
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
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_14 hero_green">Sticky Menu Color</h2>
                                        <p class="size_12 hero_grey">Set the background color of your sticky menu.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" id="bgStickyStart" class="color_picker" name="bgStickyStart" value="#DC4551">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_14 hero_green">Sticky menu logo</h2> 
                                        <p class="size_12 hero_grey">This is the logo that will be displayed when sticky is activated.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                	<input type="checkbox" data-size="sml" id="stickyLogoActive" name="stickyLogoActive" value="1">                                    
                                </div>                                
                            </div>
                            <div class="hero_col_12">                                
                                <div class="hero_col_8">
                            		<div class="hero_button_auto green_button rounded_3 hero_media_uploader" data-connect-with="stickyUrl">Add logo</div>
                                </div>
                                <div class="hero_col_4">
                                    <div class="hero_main_sticky_logo rounded_3"></div>
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_14 hero_green">Sticky menu logo URL</h2>
                                        <p class="size_12 hero_grey">This is the source url for your sticky logo, you can also make use of your own url.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="img" id="stickyUrl" name="stickyUrl" value="#">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                            		<label><h2 class="size_14 hero_green">Sticky logo padding left</h2></label>
                        			<p class="size_12 hero_grey">This adds padding to your sticky logo on the left side.</p>
                                </div>
                                <div class="hero_col_4">
                                    <input type="text" data-size="lrg" data-hero_type="px" id="stickyLogoPaddingLeft" name="stickyLogoPaddingLeft">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Sticky menu font and size</h2>
                                        <p class="size_12 hero_grey">Setup the sticky menu font sizing and color.</p>
                                    </label>
                                </div>
                            </div>
                            <div class="hero_col_11">
                            	<label><h2 class="size_14 hero_green">Sticky menu font size and color</h2></label>
                            	<p class="size_12 hero_grey">This will style the sticky navigation items.</p>                 
                            </div>
                            <div class="hero_col_12">                            	               
                                <div class="hero_col_2">
                             		<label>Font weight</label>
                                    <select data-size="lrg" id="stickyFontWeight" name="stickyFontWeight">
                                        <option value="bolder">Bolder</option>
                                        <option value="bold">Bold</option>
                                        <option value="lighter">Lighter</option>
                                        <option value="inherit">Inherit</option>
                                        <option value="normal">Normal</option>
                                    </select>
                                </div>
                                <div class="hero_col_2">
                             		<label>Font size</label>
                                    <input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="stickyFontSize" name="stickyFontSize">
                                </div>
                                 <div class="hero_col_2">
                             		<label>Font sizing</label>
                                    <select data-size="lrg" id="stickyFontSizing" name="stickyFontSizing">
                                        <option>px</option>
                                        <option>em</option>
                                    </select>
                                </div>
                                <div class="hero_col_2">
                             		<label>Font color</label>
                                    <input type="text" id="stickyFontColor" class="color_picker" name="stickyFontColor" value="#DC4551">
                                </div>
                            </div>
                            <div class="hero_col_11">
                            	<label><h2 class="size_14 hero_green">Sticky menu hover font color</h2></label>
                                <p class="size_12 hero_grey">This will style the sticky navigation hover text color.</p>
                            </div>
                           	<div class="hero_col_12">
                                <div class="hero_col_2">
                                    <input type="text" id="stickyFontHoverColor" class="color_picker" name="stickyFontHoverColor" value="#DC4551">
                                </div>                                
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">  
                    	<div class="hero_section_toggle" data-toggle-section="main_responsive">                      
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Arrows for Navigation</h2>
                                        <p class="size_12 hero_grey">This will enable a small dropdown arrow on each navigation item.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="arrows" name="arrows" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Transparency</label>
                                    <select data-size="lrg" id="arrowTransparency" name="arrowTransparency" data-height="150">
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
                                    <label>Arrow color</label>
                                    <input type="text" id="arrowColor" class="color_picker" name="arrowColor" value="#DC4551">
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->   
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_section_toggle" data-toggle-section="main_line_devider"> 
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Line divider styling</h2>
                                        <p class="size_12 hero_grey">This will enable a line divider between your navigational items.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="devider" name="devider" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Transparency</label>
                                    <select data-size="lrg" id="deviderTransparency" name="deviderTransparency" data-height="150">
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
                                    <label>Divider color</label>
                                    <input type="text" id="deviderColor" class="color_picker" name="deviderColor" value="#DC4551">
                                </div>
                                <div class="hero_col_3">
                                    <label>Sizing</label>
                                    <select data-size="lrg" id="deviderSizing" name="deviderSizing">
                                        <option value="small">Small</option>
                                        <option value="full">Full</option>
                                    </select>
                                </div>
                                <div class="hero_col_5">
                                    <div class="hero_line_example"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_section_toggle" data-toggle-section="main_grp_devider"> 
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Group divider styling</h2>
                                        <p class="size_12 hero_grey">This will enable a group line divider between your navigational sections: <br>eg Navigation, Search, Social, Logo and Woo Cart .</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="groupDevider" name="groupDevider" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Transparency</label>
                                    <select data-size="lrg" id="groupTransparency" name="groupTransparency" data-height="50">
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
                                    <label>Divider color</label>
                                    <input type="text" id="groupColor" class="color_picker" name="groupColor" value="#DC4551">
                                </div>
                                <div class="hero_col_3">
                                    <label>Sizing</label>
                                    <select data-size="lrg" id="groupSizing" name="groupSizing">
                                        <option value="small">Small</option>
                                        <option value="full">Full</option>
                                    </select>
                                </div>
                                <div class="hero_col_5">
                                    <div class="hero_grp_example"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->               
            </form>
        <!-- END: FORM -->
    </div>
</div>
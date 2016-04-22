<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/styling.standard.view.js"></script>
<div class="hero_views">
    <div class="hero_col_12">
    	<h2 class="hero_red size_18 weight_600">
            Background color<br />
            <strong class="size_11 hero_grey">Choose the background color for your dropdown menus.</strong>
        </h2>
        <!-- START: FORM -->
            <form>      	
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                        <label><h2 class="size_14 hero_green">Default background color | Gradients</h2></label><div class="hero_switch_position"><input type="checkbox" data-size="sml" id="bgDropGradient" name="bgDropGradient" value="1" data-smltoggler="menuGradientToggle"></div>
                        <p class="size_12 hero_grey">This will be the dropdown menu background color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                            	<label>Start color</label>
                            	<input type="text" id="bgDropStartColor" class="color_picker" name="bgDropStartColor" value="#DC4551">
                            </div>
                            <div class="menuGradientToggle">
                                <div class="hero_col_2">
                                	<label>End color</label>
                                    <input type="text" id="bgDropEndColor" class="color_picker" name="bgDropEndColor" value="#DC4551">
                                </div>
                                <div class="hero_col_3">
                                	<label>Gradient direction</label>
                                    <select data-size="lrg" id="bgDropGradientPath" name="bgDropGradientPath">
                                        <option value="vertical">Vertical</option>
                                        <option value="horizontal">Horizontal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hero_col_2">
                            	<label>Transparency</label>
                            	<select data-size="lrg" id="bgDropTransparency" name="bgDropTransparency">
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
                        <label><h2 class="size_14 hero_green">Hover background color | Gradients</h2></label><div class="hero_switch_position"><input type="checkbox" data-size="sml" id="bgHoverGradient" name="bgHoverGradient" value="1" data-smltoggler="menuHoverGradientToggle"></div>
                        <p class="size_12 hero_grey">This will be the dropdown background hover color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                            	<label>Start color</label>
                            	<input type="text" id="bgHoverStartColor" class="color_picker" name="bgHoverStartColor">
                            </div>
                            <div class="menuHoverGradientToggle">
                                <div class="hero_col_2">
                                	<label>End color</label>
                                    <input type="text" id="bgHoverEndColor" class="color_picker" name="bgHoverEndColor">
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
                    	<div class="hero_col_12">
                            <h2 class="size_18 hero_red weight_600">Fonts</h2>
                        </div>  
                    	<label><h2 class="size_14 hero_green">Font size and color</h2></label>
                        <p class="size_12 hero_grey">This will style the dropdown navigation items.</p>
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
                        <label><h2 class="size_14 hero_green">Hover font color</h2></label>
                        <p class="size_12 hero_grey">This will style the dropdown navigation hover text color.</p>
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
                                <h2 class="size_18 hero_red weight_600">Dropdown Width</h2>
                                <p class="size_12 hero_grey">This is the width of the dropdown.</p>
                            </label>
                            <div class="hero_col_12">
                                <div class="hero_col_5">
                                	<label>Custom width(px)</label>
                                	<input type="text" data-size="lrg"  data-hero_type="px" id="width" name="width">
                                </div>
                            </div>  
                        </div>
                        <div class="hero_col_6">
                            <div class="hero_drop_width_example"></div>
                        </div>                                              
                    </div>
                <!-- END: ACTIVATION -->                 
                 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                        <div class="hero_col_6">
                            <label>
                                <h2 class="size_18 hero_red weight_600">Padding</h2>
                                <p class="size_12 hero_grey">This will add padding to the dropdown nav item.</p>
                            </label>
                            <div class="hero_col_12">
                                <div class="hero_col_3">
                                	<label>Top</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingTop" name="paddingTop" class="ds_padding">
                                </div>
                                <div class="hero_col_3">
                                	<label>Right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingRight" name="paddingRight" class="ds_padding">
                                </div>
                                <div class="hero_col_3">
                                	<label>Bottom</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingBottom" name="paddingBottom" class="ds_padding">
                                </div>
                                <div class="hero_col_3">
                                	<label>Left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingLeft" name="paddingLeft" class="ds_padding">
                                </div>
                            </div>  
                        </div>
                        <div class="hero_col_6">
                            <div class="hero_drop_padding_example"></div>
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
                                        <p class="size_12 hero_grey">This will add a shadow to your dropdown menu. (Not supported in older browsers)</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="shadow" name="shadow" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Shadow distance</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="shadow_distance" name="shadow_distance" class="ds_shadow_radius">
                                </div>
                                <div class="hero_col_2">
                                    <label>Shadow blur</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="shadow_blur" name="shadow_blur" class="ds_shadow_radius">
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
                        <div class="hero_section_toggle" data-toggle-section="main_responsive">
                            <div class="hero_col_12">
                                <div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Border Arround Dropdown Menu</h2>
                                        <p class="size_12 hero_grey">This will add a border to your dropdown menu.</p>
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
                                <p class="size_12 hero_grey">This will allow you to add rounded corners to your dropdown menu.</p>
                                <div class="hero_col_2">
                                    <label>Top left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_top" class="ds_border_radius" name="border_radius_top">
                                </div>
                                <div class="hero_col_2">
                                    <label>Top right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_top_right" class="ds_border_radius" name="border_radius_top_right">
                                </div>
                                <div class="hero_col_2">
                                    <label>Bottom right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_bottom_right" class="ds_border_radius" name="border_radius_bottom_right">
                                </div>
                                <div class="hero_col_2">
                                    <label>Bottom left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="border_radius_bottom_left" class="ds_border_radius" name="border_radius_bottom_left">
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
                                        <h2 class="size_18 hero_red weight_600">Arrows for Dropdowns</h2>
                                        <p class="size_12 hero_grey">This will enable a small dropdown arrow on each dropdown menu item.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="arrows" name="arrows" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Transparency</label>
                                    <select data-size="lrg" id="arrowTransparency" name="arrowTransparency" data-height="100">
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
                    	<div class="hero_section_toggle" data-toggle-section="main_responsive">       
                            <div class="hero_col_12">
                            	<div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Dropdown line divider styling</h2>
                                        <p class="size_12 hero_grey">This will enable a line divider between your dropdown menu items.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="devider" name="devider" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Transparency</label>
                                    <select data-size="lrg" id="deviderTransparency" name="deviderTransparency" data-height="50">
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
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->            
            </form>
        <!-- END: FORM -->
    </div>
</div>
<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/styling.mega.view.js"></script>
<div class="hero_views">
    <div class="hero_col_12">
    	<h2 class="hero_red size_18 weight_600">
            Background color<br />
            <strong class="size_11 hero_grey">Choose the background color for your mega menus.</strong>
        </h2>
        <!-- START: FORM -->
            <form>      	
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                        <label><h2 class="size_14 hero_green">Default background color | Gradients</h2></label><div class="hero_switch_position"><input type="checkbox" data-size="sml" id="bgDropGradient" name="bgDropGradient" value="1" data-smltoggler="menuGradientToggle"></div>
                        <p class="size_12 hero_grey">This will be the mega menu background color.</p>
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
                        <p class="size_12 hero_grey">This will be the mega menu background hover color.</p>
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
                                        <option value="value_1">Vertical</option>
                                        <option value="value_2">Horizontal</option>
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
                    	<label><h2 class="size_14 hero_green">Main headings</h2></label>
                        <p class="size_12 hero_grey">This will style your mega menu headings. <a class="toggle_visual hero_green size_12" data-height="400" data-toggle="close" data-to-toggle="mega_style_ref">Visual reference</a></p>
                        <div class="hmenu_mega_ref" id="mega_style_ref">
                        	
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                            	<label>Font family</label>
                            	<select data-size="lrg" id="fontFamily_0" name="fontFamily_0" data-height="200">
                                    <option value="arial">Arial</option>
                                    <option value="times">Times New Roman</option>
                                    <option value="chiller">Chiller</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font weight</label>
                            	<select data-size="lrg" id="fontWeight_0" name="fontWeight_0">
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Lighter</option>
                                    <option value="inherit">Inherit</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font size</label>
                            	<input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="fontSize_0" name="fontSize_0">
                            </div>
                             <div class="hero_col_2">
                             	<label>Font sizing</label>
                            	<select data-size="lrg" id="fontSizing_0" name="fontSizing_0">
                                    <option>px</option>
                                    <option>em</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font color</label>
                            	<input type="text" id="fontColor_0" class="color_picker" name="fontColor_0" value="#DC4551">
                            </div>
                        </div>
                        <label><h2 class="size_14 hero_green">Body</h2></label>
                        <p class="size_12 hero_grey">This will style your mega menu body text.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                            	<label>Font family</label>
                            	<select data-size="lrg" id="fontFamily_1" name="fontFamily_1" data-height="200">
                                    <option value="arial">Arial</option>
                                    <option value="times">Times New Roman</option>
                                    <option value="chiller">Chiller</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font weight</label>
                            	<select data-size="lrg" id="fontWeight_1" name="fontWeight_1">
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Lighter</option>
                                    <option value="inherit">Inherit</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font size</label>
                            	<input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="fontSize_1" name="fontSize_1">
                            </div>
                             <div class="hero_col_2">
                             	<label>Font sizing</label>
                            	<select data-size="lrg" id="fontSizing_1" name="fontSizing_1">
                                    <option>px</option>
                                    <option>em</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font color</label>
                            	<input type="text" id="fontColor_1" class="color_picker" name="fontColor_1" value="#DC4551">
                            </div>
                        </div>
                        <label><h2 class="size_14 hero_green">List menu item</h2></label>
                        <p class="size_12 hero_grey">This will style your mega menu list items.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                            	<label>Font family</label>
                            	<select data-size="lrg" id="fontFamily_2" name="fontFamily_2" data-height="200">
                                    <option value="arial">Arial</option>
                                    <option value="times">Times New Roman</option>
                                    <option value="chiller">Chiller</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font weight</label>
                            	<select data-size="lrg" id="fontWeight_2" name="fontWeight_2">
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Lighter</option>
                                    <option value="inherit">Inherit</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font size</label>
                            	<input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="fontSize_2" name="fontSize_2">
                            </div>
                             <div class="hero_col_2">
                             	<label>Font sizing</label>
                            	<select data-size="lrg" id="fontSizing_2" name="fontSizing_2">
                                    <option>px</option>
                                    <option>em</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font color</label>
                            	<input type="text" id="fontColor_2" class="color_picker" name="fontColor_2" value="#DC4551">
                            </div>
                        </div>
                        <label><h2 class="size_14 hero_green">List menu description</h2></label>
                        <p class="size_12 hero_grey">This will style your mega menu list descriptions.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_3">
                            	<label>Font family</label>
                            	<select data-size="lrg" id="fontFamily_3" name="fontFamily_3" data-height="200">
                                    <option value="arial">Arial</option>
                                    <option value="times">Times New Roman</option>
                                    <option value="chiller">Chiller</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font weight</label>
                            	<select data-size="lrg" id="fontWeight_3" name="fontWeight_3">
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Lighter</option>
                                    <option value="inherit">Inherit</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font size</label>
                            	<input type="text" data-size="lrg" class="hero_int_only" maxlength="2" id="fontSize_3" name="fontSize_3">
                            </div>
                             <div class="hero_col_2">
                             	<label>Font sizing</label>
                            	<select data-size="lrg" id="fontSizing_3" name="fontSizing_3">
                                    <option>px</option>
                                    <option>em</option>
                                </select>
                            </div>
                             <div class="hero_col_2">
                             	<label>Font color</label>
                            	<input type="text" id="fontColor_3" class="color_picker" name="fontColor_3" value="#DC4551">
                            </div>
                        </div>
                        <label><h2 class="size_14 hero_green">Hover font color</h2></label>
                        <p class="size_12 hero_grey">This will style the text hover color.</p>
                        <div class="hero_col_12">
                            <div class="hero_col_2">
                            	<input type="text" id="fontHoverColor" class="color_picker" name="fontHoverColor" value="#DC4551">
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION --> 
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_col_8">
                            <h2 class="size_18 hero_red weight_600">Dropdown Width</h2>
                            <p class="size_12 hero_grey">The width of the mega menu is determined by the width of your menu bar dimensions. <br>This can be found in Styling &rsaquo; Main Navigation</p>
                        </div>
                        <div class="hero_col_4"> 
                        	<div class="hero_button_auto green_button rounded_3" id="goto_main_nav">View dimensions</div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->   
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                        <div class="hero_col_6">
                            <label>
                                <h2 class="size_18 hero_red weight_600">Padding</h2>
                                <p class="size_12 hero_grey">This will add padding to the mega menu.</p>
                            </label>
                            <div class="hero_col_12">
                                <div class="hero_col_3">
                                	<label>Top</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingTop" name="paddingTop" class="ms_padding">
                                </div>
                                <div class="hero_col_3">
                                	<label>Right</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingRight" name="paddingRight" class="ms_padding">
                                </div>
                                <div class="hero_col_3">
                                	<label>Bottom</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingBottom" name="paddingBottom" class="ms_padding">
                                </div>
                                <div class="hero_col_3">
                                	<label>Left</label>
                                    <input type="text" data-size="lrg" data-hero_type="px" id="paddingLeft" name="paddingLeft" class="ms_padding">
                                </div>
                            </div>  
                        </div>
                        <div class="hero_col_6">
                            <div class="hero_mega_padding_example"></div>
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
                                        <p class="size_12 hero_grey">This will add a shadow to your mega menu. (Not supported in older browsers)</p>
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
                                    <select data-size="lrg" id="shadowTransparency" name="shadowTransparency" data-height="150">
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
                                        <h2 class="size_18 hero_red weight_600">Border Arround Mega Menu</h2>
                                        <p class="size_12 hero_grey">This will add a border to your mega menu.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="border" name="border" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Transparency</label>
                                    <select data-size="lrg" id="borderTransparency" name="borderTransparency" data-height="150">
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
                                <p class="size_12 hero_grey">This will allow you to add rounded corners to your mega menu.</p>
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
                    	<div class="hero_section_toggle" data-toggle-section="main_responsive">       
                            <div class="hero_col_12">
                            	<div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Mega column divider</h2>
                                        <p class="size_12 hero_grey">This will enable a line divider between your mega menu columns.</p>
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
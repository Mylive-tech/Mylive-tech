<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/layout.edit.view.js"></script>
<div class="hero_views">
    <div class="hero_col_12">
    	<h2 class="hero_red size_18 weight_600 hero_col_8 hero_padding_bot_20">
            Layout &amp; Order<br />
            <strong class="size_11 hero_grey">Choose your menu structure by simply switching on or off what you need and drag and drop elements to create the layout you want.Items will snap left, right or center.</strong>
        </h2>
        <!-- START: FORM -->
            <form>             	
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">                    	
                    	<div class="hero_sandbox_holder">
                        	<div class="hero_sandbox_labels">
                            	<div class="hero_sand_left rounded_3">Left</div>
                            	<div class="hero_sand_center rounded_3">Center</div>
                            	<div class="hero_sand_right rounded_3">Right</div>
                            </div>
                        	<div class="hero_main_sandbox">
                            	<ul class="hero_position_left connect_nav_items">
                                </ul>
                            	<ul class="hero_position_center connect_nav_items"> 
                                </ul>
                                <ul class="hero_position_right connect_nav_items">
                                </ul>
                            </div>
                        </div>
                        <div class="hero_col_12">
                            <div class="hero_col_12">
                                Select which items you want activated on your navigation. 
                            </div>
                        </div>
                        <div class="hero_col_2">   
                            <div class="hero_field_wrap">                         	
                                <label><h2 class="size_12 hero_green">Logo</h2></label>
                                <div class="hero_switch_position_right"><input type="checkbox" data-size="sml" id="logo" name="logo" value="1"></div>
                            </div>
                        </div>
                        <div class="hero_col_2">
                            <div class="hero_field_wrap">  
                                <label><h2 class="size_12 hero_green">Menu</h2></label>
                                <div class="hero_switch_position_right"><input type="checkbox" data-size="sml" id="menu" name="menu" value="1"></div>
                            </div>                                 
                        </div>
                        <div class="hero_col_2">   
                            <div class="hero_field_wrap">                         	
                                <label><h2 class="size_12 hero_green">Search</h2></label>
                                <div class="hero_switch_position_right"><input type="checkbox" data-size="sml" id="search" name="search" value="1"></div> 
                            </div>
                        </div>
                        <div class="hero_col_2">
                            <div class="hero_field_wrap">  
                                <label><h2 class="size_12 hero_green">Social</h2></label>
                                <div class="hero_switch_position_right"><input type="checkbox" data-size="sml" id="social" name="social" value="1"></div>
                            </div>                                 
                        </div>
                        <div class="hero_col_2">
                           <div class="hero_field_wrap">  
                                <label><h2 class="size_12 hero_green">WooCart</h2></label>
                                <div class="hero_switch_position_right"><input type="checkbox" data-size="sml" id="cart" name="cart" value="1"></div>
                            </div>                                 
                        </div>
                    </div>
                <!-- END: ACTIVATION -->      
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14">
                    	<div class="hero_col_12">
                            <div class="hero_col_8">
                                <label>
                                    <h2 class="size_18 hero_red weight_600">Menu z-index</h2>
                                    <p class="size_12 hero_grey">Set Menu Z-index.</p>
                                </label>
                            </div>
                            <div class="hero_col_4">
                                <input type="text" data-size="sml" id="zindex" name="zindex" value="100" onKeyPress="return num_only(event);" >
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->              
            </form>
        <!-- END: FORM -->
    </div>
</div>
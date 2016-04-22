<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/layout.structure.view.js"></script>
<div class="hero_nav_views">
	<div class="hmenu_structure_loader">
        <div class="hmenu_inner_loader">
        	<div>
            	SAVING
            </div>
        </div>
    </div>
	<div class="hero_col_12">
    	<div class="hero_col_3">
        	<div class="hero_nav_sidebar">
            	<div class="hero_sidebar_inner">                	
                    <div class="hero_sidebar_nav_wrapper">
                        <h1 class="hero_red size_18 nav_side_bar_head">
                            Menu Setup<br />
                            <strong class="size_11 hero_grey">Start adding to your navigation by using the box below.</strong>
                        </h1>
                        <ul class="hero_accordion hero_sidebar_list">
                            <li class="hero_list_sort_item">
                                <div class="hero_item_wrap">
                                    <div class="hero_item_bar hero_bar_grey">
                                        <div class="hero_item_toggle trigger_pages" data-nav-toggle="close"></div>
                                        <div class="hero_item_heading size_14 hero_white">
                                            Pages
                                        </div>                                        
                                    </div>
                                    <div class="hero_col_12 hero_item_content">
                                   		<div class="hero_sub" id="nav_pages">
                                            <div class="hero_sidebar_content">
                                                <div class="load_nav_misc">
                                                    <!-- LOAD PAGES -->
                                                </div>
                                                <div class="hero_sidebar_button add_to_navigation rounded_3 hero_white">Add to menu</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="hero_list_sort_item">
                                <div class="hero_item_wrap">
                                    <div class="hero_item_bar hero_bar_grey">
                                        <div class="hero_item_toggle" data-nav-toggle="close"></div>
                                        <div class="hero_item_heading size_14 hero_white">
                                        	Mega Menu
                                        </div>                                        
                                    </div>
                                    <div class="hero_col_12 hero_item_content">
                                   		<div class="hero_sub">
                                            <form id="mega_menu_form">
                                                <div class="hero_sidebar_content">
                                                    Add a mega menu
                                                    <div class="col-md-12">
                                                        <input type="text" data-size="lrg" id="mega_menu_name" name="mega_menu_name" value="" placeholder="Mega menu name">
                                                    </div>
                                                    <div class="hero_sidebar_button add_mega_to_navigation rounded_3 hero_white">Add mega menu</div>
                                                </div>  
                                            </form>                              
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="hero_list_sort_item">
                                <div class="hero_item_wrap">
                                    <div class="hero_item_bar hero_bar_grey">
                                        <div class="hero_item_toggle" data-nav-toggle="close"></div>
                                        <div class="hero_item_heading size_14 hero_white">
                                            Custom Link
                                        </div>                                        
                                    </div>
                                    <div class="hero_col_12 hero_item_content">
                                   		<div class="hero_sub">
                                            <form id="custom_menu_form">
                                                <div class="hero_sidebar_content">
                                                    <div class="col-md-12">
                                                        <label>Label:</label>
                                                        <input type="text" data-size="lrg" id="custom_name" name="custom_name" value="" placeholder="Custom label">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>Link:</label>
                                                        <input type="text" data-size="lrg" id="custom_url" name="custom_url" value="" placeholder="Link/url">
                                                        <p class="size_11 hero_grey">http://www.example.com</p>
                                                    </div>
                                                    <div class="hero_sidebar_button add_custom_to_navigation rounded_3 hero_white">Add custom link</div>
                                                </div>  
                                            </form> 
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="hero_list_sort_item">
                                <div class="hero_item_wrap">
                                    <div class="hero_item_bar hero_bar_grey">
                                        <div class="hero_item_toggle" data-nav-toggle="close"></div>
                                        <div class="hero_item_heading size_14 hero_white">
                                            Custom Method
                                        </div>                                        
                                    </div>
                                    <div class="hero_col_12 hero_item_content">
                                   		<div class="hero_sub">
                                            <form id="custom_method_menu_form">
                                                <div class="hero_sidebar_content">
                                                    <div class="col-md-12">
                                                        <label>Label:</label>
                                                        <input type="text" data-size="lrg" id="custom_method_name" name="custom_method_name" value="" placeholder="Custom label">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>Method:</label>
                                                        <input type="text" data-size="lrg" id="custom_method" name="custom_method" value="" placeholder="onClick Event">
                                                    </div>
                                                    <div class="hero_sidebar_button add_custom_method_to_navigation rounded_3 hero_white">Add custom method</div>
                                                </div>  
                                            </form> 
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="hero_list_sort_item">
                                <div class="hero_item_wrap">
                                    <div class="hero_item_bar hero_bar_grey">
                                        <div class="hero_item_toggle" data-nav-toggle="close"></div>
                                        <div class="hero_item_heading size_14 hero_white">
                                            Categories
                                        </div>                                        
                                    </div>
                                    <div class="hero_col_12 hero_item_content">
                                   		<div class="hero_sub" id="nav_categories">
                                            <div class="hero_sidebar_content">
                                                <div class="load_nav_misc">
                                                    <!-- LOAD PAGES -->
                                                </div>
                                                <div class="hero_sidebar_button add_to_navigation rounded_3 hero_white">Add to menu</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>                        
                    </div>                
                </div>
            </div>
        </div>
        <div class="hero_col_9 hero_main_nav_items">
            <h1 class="hero_red size_18">
                Structure<br />
                <strong class="size_11 hero_grey">Here you wil do a complete navigation structure</strong>
            </h1>        
            <!-- START: FORM -->
                <form>
                    <div class="hero_section_holder hero_grey size_14">
                    	
                        <!-- error message -->
                        <div class="hmenu_nav_error hero_red size_12">
                        	You currently dont have any items in your menu.
                        </div>
                    
                        <!-- sortable items -->
                        <ul class="sort main_holder hero_accordion">
                        </ul>
                        
                        <!-- hidden items - this is used to reorder the items -->
                        <div class="hidden_items" data-current-id="" data-current-lvl="">
                            <ul class="hero_dummy"></ul>
                        </div>
                        
                    </div>            	
                </form>
            <!-- END: FORM -->
        </div>
    </div>
</div>

<div class="hero_side_icon_panel">
	<h1 class="hero_red size_18 nav_side_bar_head">
        Icons<br />
        <strong class="size_11 hero_grey">Select an Icon to add to your navigational item.</strong>
    </h1>
    <div class="hero_col_12">
    	<select data-size="lrg" id="icon_select" name="icon_select">
        </select>
    </div>
    
    <div class="hero_col_12 icons_load_global">    	
    </div>
</div>













<script type="text/javascript" src="<?php echo $_GET['vp']; ?>js/styling.product.view.js"></script>
<div class="hero_views">
    <div class="hero_col_12">
    	<h2 class="hero_red size_18 weight_600">
            Woo Cart Icon Styling<br />
            <strong class="size_11 hero_grey">Set the styling your your Woo Cart icon.</strong>
        </h2>
        <!-- START: FORM -->
            <form>
                <!-- START: ACTIVATION -->
                    <div class="hero_section_holder hero_grey size_14"> 
                    	<div class="hero_section_toggle" data-toggle-section="main_responsive">       
                            <div class="hero_col_12">
                            	<div class="hero_col_8">
                                    <label>
                                        <h2 class="size_18 hero_red weight_600">Woo Cart</h2>
                                        <p class="size_12 hero_grey">Choose your color and size for the Woo Cart icon.</p>
                                    </label>
                                </div>
                                <div class="hero_col_4">
                                    <input type="checkbox" data-size="lrg" id="cart" name="cart" value="1" data-toggler="true">
                                </div>
                            </div>
                            <div class="hero_col_12">
                                <div class="hero_col_2">
                                    <label>Icon Size</label>
                                    <select data-size="lrg" id="iconProductSize" name="iconProductSize">
                                        <option value="xsmall" selected="selected">x-small</option>
                                        <option value="small">small</option>
                                        <option value="medium">medium</option>
                                        <option value="large">large</option>
                                    </select>
                                </div>
                                <div class="hero_col_2">
                                    <label>Icon color</label>
                                    <input type="text" id="iconProductColor" class="color_picker" name="iconProductColor" value="#DC4551">
                                </div>
                                <div class="hero_col_2">
                                    <label>Icon Hover color</label>
                                    <input type="text" id="iconProductHoverColor" class="color_picker" name="iconProductHoverColor" value="#DC4551">
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: ACTIVATION -->            
            </form>
        <!-- END: FORM -->
    </div>
</div>
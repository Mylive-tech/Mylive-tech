<?php
    global $tbtestimonials;
    $q = new WP_Query( array( 'post_type' => 'testimonial', 'post_status' => 'publish', 'posts_per_page' => -1 ) );
    wp_enqueue_script( 'jquery' );
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>TB-Testimonials Shortcode Generator</title>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
    <?php wp_head(); ?>
    <script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo plugins_url( '', __FILE__ ); ?>/tinymce.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo plugins_url( 'window.css', __FILE__ ); ?>" />
    <base target="_self" />
    <style type="text/css" media="screen">
        .blue-button {
            background-image: -moz-linear-gradient(center top , rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0)), url("data:image/png;base64,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")!important;
            background-color: #269CE9!important;
            border-color: #269CE9!important;
            color: white !important;
            text-shadow: -1px -1px 1px #73ADD3 !important;
            display:block;
            width:100%;
            padding:10px 0!important;
            margin-top:10px!important;
            font-size:1.1em!important;
        }
        .blue-button:hover {
            background-color:#41b4ff!important;
        }
    </style>
</head>
<body id="tbt-shortcode-generator" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none; font-size:62.5%;">

    <form onsubmit="insert_testimonial('<?php echo $tbtestimonials->post_type; ?>');">
        <h3>What to output</h3>
        <select id="what-to-output" style="padding:3px; width:100%; font-size:1.4em;">
            <optgroup style="border-bottom:1px solid #ccc; padding:5px 0;">
                <option value="all">All Testimonials</option>
            </optgroup>
            <optgroup style="border-bottom:1px solid #ccc; padding:5px 0;">
                <option value="random">Random Testimonial</option>
            </optgroup>
            <?php
                if( $q->have_posts() ){
                    echo '<optgroup label="" style="border-bottom:1px solid #ccc; padding:5px 0;">';
                    while( $q->have_posts() ){
                        $q->the_post();
                        printf( '<option value="%d" title="%s">ID: %d -- %s -- %s&hellip;</option>', get_the_ID(), esc_attr( get_the_content() ), get_the_ID(), get_the_title(), substr( get_the_excerpt(), 0, 20 ) );
                    }
                    echo '</optgroup>';
                }
            ?>
        </select>

        <h3 style="margin-top:5px;">Output Template</h3>
        <select id="output-template" style="padding:3px; width:100%; font-size:1.4em;">
            <?php foreach( $tbtestimonials->templates as $template ) : ?>
                <option value="<?php echo $template->name(); ?>"<?php if( $template->name() == 'shortcode' ) echo ' selected="selected"'; ?>><?php echo $template->name(); ?></option>
            <?php endforeach; ?>
        </select>

        <div style="height:162px; overflow:hidden; position:relative;">
            <div id="no-option-notice" style="display:none; height:162px; overflow:hidden; position:absolute; top:0; left:0; width:100%; background-color:#f1f1f1; z-index:5;">
                <h3 style="padding:75px 0; text-align:center; color:#999; text-shadow:#fff 1px 1px 0; font-size:2em; font-weight:normal;">Other options are not available when only a single testimonial is to be displayed.</h3>
            </div>
            <div id="listing-categories" style="font-size:1.4em; margin-top:5px; background-color:#f1f1f1; position:relative; z-index:2;" >
                <h3>Category</h3>
                <select id="category" style="display:block; width:98%; padding:1%; font-size:1.0em; margin-top:3px;">
                    <?php $terms = get_terms( 'tbtestimonial_category', array( 'hide_empty' => true, 'orderby' => 'ID', 'order' => 'asc' ) ); ?>
                    <option value="all">All Categories</option>
                    <?php foreach( $terms as $term ) : ?>
                        <option value="<?php echo $term->name; ?>"><?php echo $term->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="listing-options" style="font-size:1.4em; background-color:#f1f1f1; position:relative; z-index:2;" >
                <h3 style="margin:5px 0 0;">Options</h3>
                <div style="float:left; width:46%; padding:1%; font-size:1.0em;">
                    <label for="order">Order</label>
                    <select id="order" style="font-size:inherit; display:block; margin-top:3px; width:98%; padding:1%;">
                        <option value="desc">Newest to Oldest</option>
                        <option value="asc">Oldest to Newest</option>
                    </select>
                </div>
                <div style="float:right; width:46%; padding:1%; font-size:1.0em;">
                    <label for="orderby">Order By:</label>
                    <select id="orderby" style="font-size:inherit; display:block; margin-top:3px; width:98%; padding:1%;">
                        <option value="ID">ID</option>
                        <option value="author">Author</option>
                        <option value="title">Title</option>
                        <option value="date">Date</option>
                        <option value="modified">Modified</option>
                        <option value="parent">Parent</option>
                        <option value="rand">Rand</option>
                        <option value="menu_order">menu_order</option>
                        <option value="meta_value">meta_value</option>
                        <option value="meta_value_num">meta_value_num</option>
                    </select>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
        <p style="border-top:1px solid #dfdfdf;padding-top:10px; margin-top:10px;">
            <input id="insert-shortcode-button" type="submit" value="Insert Shortcode" class="blue-button color" />
        </p>
    </form>

</body>
</html>

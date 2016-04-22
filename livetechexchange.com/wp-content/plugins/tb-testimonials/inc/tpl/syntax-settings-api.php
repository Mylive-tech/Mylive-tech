<?php
    global $tbtestimonials;

    # edit template
    if( isset( $_POST['tbtestimonials_template'] ) && count( $_POST['tbtestimonials_template'] ) > 0 )
    {
        # nonce
        if( ! isset( $_POST['tbestimonials_template_nonce'] ) || ! wp_verify_nonce( $_POST['tbestimonials_template_nonce'], 'tbt-template-nonce' ) ) return;

        foreach( $_POST['tbtestimonials_template'] as $template_name => $syntax ){
            $tbtestimonials->templates[$template_name]->set_syntax( stripslashes( $syntax  ) );
        }
        $tbtestimonials->update_templates();
        printf( '<div class="updated fade"><p>Changes saved.</p></div>' );
    }

    # new template
    if( isset( $_POST['tbtestimonials_new_template'] ) && count( $_POST['tbtestimonials_new_template'] ) > 0 )
    {
        if( isset( $_POST['new_template_nonce'] ) && wp_verify_nonce( $_POST['new_template_nonce'], 'new-template' ) )
        {
            extract( $_POST['tbtestimonials_new_template'] );
            if( isset( $name ) && ! empty( $name ) )
            {
                $tbtestimonials->add_template( stripslashes( $name ), stripslashes( $description ), stripslashes( $syntax ) );
                $tbtestimonials->update_templates();
                printf( '<div class="updated fade"><p>Added new template: %s</p></div>', stripslashes( $name ) );
            }
        }
    }

    # delete template
    if( isset( $_GET['delete'] ) && ! empty( $_GET['delete'] ) && isset( $_GET['nonce'] ) && ! empty( $_GET['nonce'] ) )
    {
        if( $tbtestimonials->delete_template( urldecode( $_GET['delete'] ) ) )
            $tbtestimonials->update_templates();
            printf( '<div class="updated fade"><p>Deleted template: %s.</p></div>', urldecode( $_GET['delete'] ) );
    }
?>
<div class="wrap">
    <h2>TB-Testimonials Output Templates</h2>

    <div id="tbt-documentation-tabs">
        <ul id="tabs">
            <li><a href="#templates">Templates</a></li>
            <li><a href="#tags">Tags</a></li>
            <li><a href="#new">Create New Template</a></li>
        </ul>
        <div class="clear"></div>

        <div id="tab-content-container">
            <div class="tab-content" id="templates">
                <?php if( $templates = $tbtestimonials->templates ) : ?>
                    <form action="<?php echo remove_query_arg( array( 'delete', 'nonce' ) ); ?>#templates" method="post">
                        <?php wp_nonce_field( 'tbt-template-nonce', 'tbestimonials_template_nonce' ); ?>

                        <p style="width:800px;">
                            You can edit an existing output template or create a new one using the <a target="_blank" href="http://twig.sensiolabs.org/doc/templates.html" rel="external">Twig Template Syntax</a>. There are a variety of included tags for you to utilze in your templates that are listed below. If you need more tags, you can add a new tag or function to the API easily. See the Documentation for examples.
                        </p>

                        <table class="form-table">
                            <?php foreach( $templates as $template ) : ?>
                                <tr>
                                    <th>
                                        <h3><?php echo $template->name(); ?></h3>
                                        <small><?php echo $template->description(); ?></small>
                                        <?php if( ! in_array( $template->name(), array( 'widget', 'shortcode', 'listing' ) ) ) : ?>
                                            <a href="<?php echo add_query_arg( array( 'delete' => urlencode( $template->name() ), 'nonce' => wp_create_nonce( sprintf( 'delete-%s', $template->name() ) ) ) ); ?>" class="delete-template">Delete Template</a>
                                        <?php endif; ?>
                                    </th>
                                    <td>
                                        <textarea id="<?php echo sanitize_title( $template->name() ); ?>" name="tbtestimonials_template[<?php echo sanitize_title( $template->name() ); ?>]" rows="10" cols="50"><?php echo $template->get(); ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>

                        <p class="submit">
                            <input type="submit" class="button-primary" value="Save Changes" />
                        </p>
                    </form>
                <?php endif; ?>
            </div>
            <div class="tab-content new-template-wrap" id="new">
                <form action="<?php echo remove_query_arg( array( 'delete', 'nonce' ) ); ?>#new" method="post">
                    <?php wp_nonce_field( 'new-template', 'new_template_nonce' ); ?>
                    <table class="form-table">
                        <tr>
                            <th><label for="template-name">Template Name</label> <span style="color:#b10803; font-size:.7em; padding-left:5px; ">*Required</span></th>
                            <td><input id="template-name" name="tbtestimonials_new_template[name]" class="widefat" /></td>
                        </tr>
                        <tr>
                            <th><label for="template-description">Description</label></th>
                            <td><input id="template-description" name="tbtestimonials_new_template[description]" class="widefat" /></td>
                        </tr>
                        <tr>
                            <th><label for="new-template-syntax">Syntax</label></th>
                            <td><textarea name="tbtestimonials_new_template[syntax]" id="new-template-syntax" cols="10" rows="5"></textarea></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button-primary" value="Save Changes" />
                    </p>
                </form>
            </div>
            <div class="tab-content new-template-wrap" id="tags"style="padding-bottom:20px">
                <p>All templates have default tags that can be used in them. Alternatively, you can register new tags to use in your templates if needed. See the <a href="<?php echo admin_url( 'edit.php?post_type=testimonial&page=tbtestimonials-documentation#examples' ); ?>">Documentation</a> page for more details.</p>
                <div id="tbt-tag-listing">
                    <div class="l">
                        <h3>Default Tags</h3>
                        <?php $tags = array( 'permalink', 'gravatar', 'testimonial', 'author_prefix', 'author', 'company_url', 'company_name', 'testimonial_excerpt' ); ?>
                        <ul>
                            <?php foreach( $tags as $t ) : ?>
                                <li><code>{{ <?php echo $t; ?> }}</code></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="r">
                        <?php
                            do_action( 'tbt_template_functions', $tbtestimonials->twig );
                            $globals = $tbtestimonials->twig->getGlobals();

                            if( count( $globals ) > 0 ) : ?>
                                <h3>User Defined Tags</h3>
                                <ul>
                                    <?php foreach( $globals as $tag => $value ) : ?>
                                        <?php
                                            $type = 'variable';
                                            $example = sprintf( '{{ %s }}', $tag );
                                        ?>
                                        <li>
                                            <?php echo $tag; ?> -
                                            <?php
                                                if( is_array( $value ) ) {
                                                    $type = 'array';
                                                    $example = sprintf( "{%% for x in %s %%}    /* do something with {{ x }} */    {%% endfor %%}", $tag );
                                                }
                                                if( is_object( $value ) ) {
                                                    $type = 'object';
                                                    $example = '';
                                                }
                                            ?>

                                            <strong><?php echo ucwords( $type ); ?></strong>
                                            <?php if( ! empty( $example ) ) : ?>
                                                - <code><?php echo $example; ?></code>
                                            <?php endif; ?>

                                            <?php if( $type == 'object' ) : ?>
                                                <ul style="margin-left:40px;">
                                                    <?php foreach( $value as $property => $property_value ) : ?>
                                                        <li><code><?php printf( '{{ %s.%s }}', $tag, $property ); ?></code></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

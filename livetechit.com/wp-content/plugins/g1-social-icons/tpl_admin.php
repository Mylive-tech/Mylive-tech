<div class="wrap">
    <h2><?php echo __( 'G1 Social Icons', 'g1_theme' ); ?></h2>
    <form action="options.php" method="post">
        <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />

        <?php settings_fields(G1_Social_Icons()->get_option_name()); ?>

        <table id="g1-social-icons">
            <thead>
                <tr>
                    <th></th>
                    <th><?php echo __( 'Name', 'g1_theme' ); ?></th>
                    <th><?php echo __( 'Label', 'g1_theme' ); ?></th>
                    <th><?php echo __( 'Caption', 'g1_theme' ); ?></th>
                    <th><?php echo __( 'Link', 'g1_theme' ); ?></th>
                    <th><?php echo __( 'Linking', 'g1_theme' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ( G1_Social_Icons()->get_items() as $g1_name => $g1_data ) {
                        $g1_color = $g1_data['color'];
                        $icon_path = G1_Social_Icons()->get_icon_path( $g1_name, '32' );

                        G1_Social_Icons_Admin()->render_item( $g1_name, $g1_color, $icon_path );
                    }
                ?>
            </tbody>
        </table>
    </form>
</div>

<script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            $( "#g1-social-icons tbody" ).sortable();

            $( "#g1-social-icons tbody tr" ).each(function () {
                var $this = $(this);

                $this.mouseover(function () {
                    $this.addClass('g1-hover');
                });

                $this.mouseout(function () {
                    $this.removeClass('g1-hover');
                });
            });
        });
    })(jQuery);
</script>


<tr>
    <td>
        <img style="background-color:<?php echo $color; ?>;" src="<?php echo esc_url( $icon_path ); ?>" alt="<?php echo esc_attr( $name ); ?>" />
    </td>
    <td>
        <?php echo esc_html( $name ); ?>
    </td>
    <td>
        <input type="text" name="<?php echo esc_attr( $base_option_name.'[label]' ); ?>" value="<?php echo esc_attr( $value['label'] ) ?>" />
    </td>
    <td>
        <input type="text" name="<?php echo esc_attr( $base_option_name.'[caption]' ); ?>" value="<?php echo esc_attr( $value['caption'] ) ?>" />
    </td>
    <td>
        <input type="text" name="<?php echo esc_attr( $base_option_name.'[link]' ); ?>" value="<?php echo esc_attr( $value['link'] ) ?>" />
    </td>
    <td>
        <select name="<?php echo esc_attr( $base_option_name.'[linking]' ); ?>">
            <option value="standard" <?php selected( $value['linking'], 'standard' ); ?>><?php echo __( 'open in the same window', 'g1_theme' ); ?></option>
            <option value="new-window" <?php selected( $value['linking'], 'new-window' ); ?>><?php echo __( 'open in the new window', 'g1_theme' ); ?></option>
        </select>
    </td>
</tr>
<?php
/*
Plugin Name: WooCommerce Subcategories widget
Plugin URI: https://github.com/darkdelphin/WooCommerce-Subcategories-widget
Description: Shows subcategories from chosen or current active category
Version: 1.4.1
Author: Pavel Burov (Dark Delphin)
Author URI: http://pavelburov.com
*/

if ( !defined('ABSPATH') ) die;

class Woocommerce_subcategories_widget extends WP_Widget {

	// Constructor
	function Woocommerce_subcategories_widget() {

		$params = array(
                    'classname' => 'woocommerce_subcategories_widget',
		    'description' => 'Shows subcategories of chosen category' // plugin description that is showed in Widget section of admin panel
		);

		// id, name, other parameters
		$this->WP_Widget('woocommerce_subcategories_widget', 'WooCommerce Subcategories', $params);
	}

	function widget( $args, $instance ) {
                

            $input = array(
				'title' => $instance['title'],
				'show_active' => $instance['show_active'],
				'catslist' => $instance['catslist'],
				'hide_empty_cats' => $instance['hide_empty_cats'],
				'hide_children' => $instance['hide_children'],
				'show_product_count' => $instance['show_product_count'],
				'show_product_count_brackets' => $instance['show_product_count_brackets'],
				'show_parent_category' => $instance['show_parent_category'],
				'show_same_level' => $instance['show_same_level'],
				'show_category_thumbnail' => $instance['show_category_thumbnail'],
				'thumbnail_size' => $instance['thumbnail_size'],
				'show_category_title' => $instance['show_category_title']
			);

            $this->get_categories($input, $args);                    
	}

	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_active'] = !empty($new_instance['show_active']) ? 1 : 0;
		$instance['catslist'] = strip_tags($new_instance['catslist']);
		$instance['hide_empty_cats'] = !empty($new_instance['hide_empty_cats']) ? 1 : 0;
		$instance['hide_children'] = !empty($new_instance['hide_children']) ? 1 : 0;
		$instance['show_product_count'] = !empty($new_instance['show_product_count']) ? 1 : 0;
		$instance['show_product_count_brackets'] = !empty($new_instance['show_product_count_brackets']) ? 1 : 0;
		$instance['show_parent_category'] = !empty($new_instance['show_parent_category']) ? 1 : 0;
		$instance['show_same_level'] = !empty($new_instance['show_same_level']) ? 1 : 0;
		$instance['show_category_thumbnail'] = !empty($new_instance['show_category_thumbnail']) ? 1 : 0;
		$instance['thumbnail_size'] = strip_tags($new_instance['thumbnail_size']);
		$instance['show_category_title'] = !empty($new_instance['show_category_title']) ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );

		$title = esc_attr( $instance['title'] );
		$show_active = isset( $instance['show_active'] ) ? (bool) $instance['show_active'] : false;
		$catslist = esc_attr( $instance['catslist'] );
		$hide_empty_cats = isset( $instance['hide_empty_cats'] ) ? (bool) $instance['hide_empty_cats'] : false;
		$hide_children = isset( $instance['hide_children'] ) ? (bool) $instance['hide_children'] : false;
		$show_product_count = isset( $instance['show_product_count'] ) ? (bool) $instance['show_product_count'] : false;
		$show_product_count_brackets = isset( $instance['show_product_count_brackets'] ) ? (bool) $instance['show_product_count_brackets'] : false;
		$show_parent_category = isset( $instance['show_parent_category'] ) ? (bool) $instance['show_parent_category'] : false;
		$show_same_level = isset( $instance['show_same_level'] ) ? (bool) $instance['show_same_level'] : false;
		$show_category_thumbnail = isset( $instance['show_category_thumbnail'] ) ? (bool) $instance['show_category_thumbnail'] : false;
		$thumbnail_size = esc_attr( $instance['thumbnail_size'] );
		$show_category_title = isset( $instance['show_category_title'] ) ? (bool) $instance['show_category_title'] : false;

		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>     
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_active') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_active') ); ?>"<?php checked( $show_active ); ?> />
			<label for="<?php echo $this->get_field_id('show_active'); ?>"><?php _e( 'Show subcategories of current active category' ); ?></label>
		</p>
		<p>
			<?php echo __('Or choose permanent category below:') ?>
		</p>
		<p>
			<input type="hidden" id="<?php echo $this->get_field_id('catslist'); ?>-selected" value="<?php echo $catslist; ?>"> 
			<select class="widefat" id="<?php echo $this->get_field_id('catslist'); ?>" name="<?php echo $this->get_field_name('catslist'); ?>">
				<?php
				$all_tax = get_transient('woocommerce_subcategories_all_tax');

				if(empty($all_tax))
				{
					$taxlist = get_terms('product_cat', 'hide_empty=0');

					ob_start();
					foreach ($taxlist as $tax) 
					{
						if(get_term_children( $tax->term_id, 'product_cat' )) 
						{
							echo '<option value="'.$tax->term_id.'">'.$tax->name.'</option>';						
						}
					}
					$output = ob_get_clean();
					set_transient('woocommerce_subcategories_all_tax', $output, 60*60*24);

					echo $output;
				}
				else echo $all_tax;
				?>
			</select>
			<script>
				console.log( document.querySelector('#<?php echo $this->get_field_id("catslist"); ?>').value = document.querySelector('#<?php echo $this->get_field_id("catslist"); ?>-selected').value );
			</script>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('hide_empty_cats') ); ?>" name="<?php echo esc_attr( $this->get_field_name('hide_empty_cats') ); ?>"<?php checked( $hide_empty_cats ); ?> />
			<label for="<?php echo $this->get_field_id('hide_empty_cats'); ?>"><?php _e( 'Hide empty categories' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('hide_children') ); ?>" name="<?php echo esc_attr( $this->get_field_name('hide_children') ); ?>"<?php checked( $hide_children ); ?> />
			<label for="<?php echo $this->get_field_id('hide_children'); ?>"><?php _e( 'Hide subcategories of deeper levels' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_product_count') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_product_count') ); ?>"<?php checked( $show_product_count ); ?> />
			<label for="<?php echo $this->get_field_id('show_product_count'); ?>"><?php _e( 'Show product count' ); ?></label>
			(<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_product_count_brackets') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_product_count_brackets') ); ?>"<?php checked( $show_product_count_brackets ); ?> />
			<label for="<?php echo $this->get_field_id('show_product_count_brackets'); ?>"><?php _e( 'Show in brackets' ); ?></label>)
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_parent_category') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_parent_category') ); ?>"<?php checked( $show_parent_category ); ?> />
			<label for="<?php echo $this->get_field_id('show_parent_category'); ?>"><?php _e( 'Show parent category' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_same_level') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_same_level') ); ?>"<?php checked( $show_same_level ); ?> />
			<label for="<?php echo $this->get_field_id('show_same_level'); ?>"><?php _e( 'Always show categories of the same level' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_category_thumbnail') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_category_thumbnail') ); ?>"<?php checked( $show_category_thumbnail ); ?> />
			<label for="<?php echo $this->get_field_id('show_category_thumbnail'); ?>"><?php _e( 'Show categories thumbnails' ); ?></label>
		</p>
		<p>
			<?php _e('Thumbnail size:') ?>
		</p>
		<p>
			<select class="widefat" id="<?php echo $this->get_field_id('thumbnail_size'); ?>" name="<?php echo $this->get_field_name('thumbnail_size'); ?>">
				<?php
					global $_wp_additional_image_sizes;

					foreach (get_intermediate_image_sizes() as $key => $thumb_size) 
					{
						$size = '';

						if (isset($_wp_additional_image_sizes[$thumb_size])) 
						{
							$width = intval($_wp_additional_image_sizes[$thumb_size]['width']);
							$height = intval($_wp_additional_image_sizes[$thumb_size]['height']);
							if($width && $height) $size = ' - '.$width.' x '.$height;
						} 
						else 
						{
							$width = get_option($thumb_size.'_size_w');
							$height = get_option($thumb_size.'_size_h');
							if($width && $height) $size = ' - '.$width.' x '.$height;
						}

						echo '<option value="'.$thumb_size.'" '.selected($thumbnail_size, $thumb_size).'>'.$thumb_size.' '.$size.'</option>';						
					}
					// keywords for sizes (thumbnail, medium, large or full) 
				?>
				<option value="full" <?php selected($thumbnail_size, 'full'); ?>>full</option>
			</select>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_category_title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_category_title') ); ?>"<?php checked( $show_category_title ); ?> />
			<label for="<?php echo $this->get_field_id('show_category_title'); ?>"><?php _e( 'Show categories titles under thumbnails' ); ?></label>
		</p>
		<?php
	}

	function get_categories($input, $args) {

		global $wp_query, $post, $woocommerce;

		extract( $input );
        extract( $args );                       

		$isproduct = false;
		$groundlevel = false;

		if(isset($catslist) && !$show_active)
		{
			if(!preg_match('/[0-9]+/', $catslist)) $catslist = get_term_by( 'slug', $catslist, 'product_cat')->term_id;

			$args = array(
				'title_li'           => '',
				'hierarchical'       => 1,
				'show_option_none'   => '',
				'echo'               => 0,
				'depth'				 => 1,
				'hide_empty'         => 0,
				'parent'             => $catslist,
				'child_of'           => $catslist,
				'taxonomy'           => 'product_cat'
			);

			if($hide_empty_cats) $args['hide_empty'] = 1;
		}
		elseif($show_active)
		{
			$isproduct = false;

			$args = array(
				'title_li'           => '',
				'hierarchical'       => 1,
				'show_option_none'   => '',
				'echo'               => 0,
				'depth'				 => 1,
				'hide_empty'         => 0,
				'taxonomy'           => 'product_cat'
			);

			if($hide_empty_cats) $args['hide_empty'] = 1;

			$current_tax = get_query_var('product_cat'); // slug

			if(!$current_tax || $current_tax == '')
			{
				if(get_class(get_queried_object()) == 'WP_Post') $isproduct = true;
				else return false;	
			}
			else
			{
				if($show_same_level)
				{
					$args['parent'] = get_queried_object()->term_id;
					$categories = get_categories( $args );

					if(empty($categories)) 
					{
						$groundlevel = true;
						
						if(get_queried_object()->parent != 0)
						{
							$args['parent'] = get_queried_object()->parent;
							$categories = get_categories( $args );
						}
						else
						{
							$args['parent'] = get_queried_object()->term_id;
							$categories = get_categories( $args );
						}
					}
					else $groundlevel = false;		
				}
				else $args['parent'] = get_queried_object()->term_id;
			}	
		}

		// Here we get all categories
		if($isproduct) $categories = get_the_terms( get_the_ID(), 'product_cat' );
		else $categories = get_categories( $args );

		if(!empty($categories))
		{
            echo $before_widget;
            
            if ($title) {
                echo $before_title . $title . $after_title;                            
            }                        
                    
			if($show_active)
			{
				if($groundlevel)
				{
					$link = get_term_link( (int)get_queried_object()->parent, 'product_cat' );
					$parent = get_term( (int)get_queried_object()->parent, 'product_cat' );
				}
				else
				{
					if(property_exists(get_queried_object(), 'term_id'))
					{
						$link = get_term_link( (int)get_queried_object()->term_id, 'product_cat' );
						$parent = get_term( (int)get_queried_object()->term_id, 'product_cat' );
					}
				}			
			}
			else
			{
				$link = get_term_link( (int)$catslist, 'product_cat' );
				$parent = get_term( (int)$catslist, 'product_cat' );
			}
			
			$level = 0;

			echo '<ul class="product-categories subcategories level'.$level.'">';
			$level++;

				if($show_parent_category && !empty($parent) )
				{
					if(get_queried_object() && property_exists($wp_query->queried_object, 'slug') && $wp_query->queried_object->slug == $parent->slug) $class = ' class="parent current"';
					else $class = ' class="parent"';

					echo '<li'.$class.'>';

					if($show_category_thumbnail)
					{
						$this->get_thumbnail($parent);

						if($show_category_title) echo '<a href="'.$link.'">'.$parent->name.'</a>';
						if($show_product_count && $show_product_count_brackets) echo ' ('.$parent->count.')';
						elseif($show_product_count) echo ' <span class="count">'.$parent->count.'</span>';

					}
					else if(!$isproduct)
					{
						echo '<a href="'.$link.'">'.$parent->name.'</a>';
						if($show_product_count && $show_product_count_brackets) echo ' ('.$parent->count.')';
						elseif($show_product_count) echo ' <span class="count">'.$parent->count.'</span>';
					} 

					echo '</li>';
				}

			foreach($categories as $cat)
			{
				if(get_queried_object() && property_exists($wp_query->queried_object, 'slug') && $wp_query->queried_object->slug == $cat->slug) $class = ' class="current"';
				else $class = '';

				$link = get_term_link( $cat->slug, $cat->taxonomy );
				echo '<li'.$class.'>';

				if($show_category_thumbnail)
				{
					echo '<a href="'.$link.'">';

					$this->get_thumbnail($cat);				   		

					echo '</a>';
				}
				
				if($show_category_title) echo '<a href="'.$link.'">'.$cat->name.'</a>';

				if(!$show_category_title && !$show_category_thumbnail) echo '<a href="'.$link.'">'.$cat->name.'</a>';

				if($show_product_count && $show_product_count_brackets) echo ' ('.$cat->count.')';
				elseif($show_product_count) echo ' <span class="count">'.$cat->count.'</span>';

				if(!$hide_children) $this->walk($cat->term_id, $show_category_thumbnail, $show_category_title, $level);
				
				echo '</li>';
			}

			echo '</ul>';
                        
            echo $after_widget;                
		}
	}

	function get_thumbnail($cat)
	{
		if(property_exists($cat, 'woocommerce_term_id')) $thumbnail_id = get_metadata( 'woocommerce_term', $cat->woocommerce_term_id, 'thumbnail_id', true );
		else $thumbnail_id = get_metadata( 'woocommerce_term', $cat->term_id, 'thumbnail_id', true );;
	
	   	if ($thumbnail_id) 
	   	{
	   		if($thumbnail_size)
	   		{
	   			$image = wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size );
	   			$image = $image[0];
	   		}
	   		else
	   		{
	   			$image = wp_get_attachment_image_src( $thumbnail_id, 'medium'  );
	   			$image = $image[0];
	   		}
	   		echo '<img src="'.$image.'">';

	   	}
	   	else echo '<img src="'.plugins_url().'/woocommerce/assets/images/placeholder.png">';	
	}

	function walk($cat , $show_category_thumbnail, $show_category_title, $level)
    {	
    	$args = array(
				'hierarchical'       => 1,
				'show_option_none'   => '',
				'hide_empty'         => 0,
				'parent'			 => $cat,
				'taxonomy'           => 'product_cat'
			);
    	$next = get_categories($args);

    	// if( $next && property_exists(get_queried_object(), 'slug'))
    	if( $next )
    	{
    		echo '<ul class="children level'.$level.'">';
    		$level++;

    		foreach ($next as $n)
    		{
    			if(get_queried_object()->slug == $n->slug) $class = ' class="current"';
				else $class = '';

    			$link = get_term_link( $n->slug, $n->taxonomy );
				echo '<li'.$class.'>';

				if($show_category_thumbnail)
				{
					echo '<a href="'.$link.'">';

					$this->get_thumbnail($n);				   		

					echo '</a>';	   		
				}

				if($show_category_title) echo '<a href="'.$link.'">'.$n->name.'</a>';
				
				if(!$show_category_title && !$show_category_thumbnail) echo '<a href="'.$link.'">'.$n->name.'</a>';

				if($show_product_count && $show_product_count_brackets) echo ' ('.$n->count.')';
				elseif($show_product_count) echo ' <span class="count">'.$n->count.'</span>';
				
				echo '</li>';

				$this->walk($n->term_id, $show_category_thumbnail, $show_category_title, $level);
				
    		}
    		
    		echo '</ul>';
    	}
    }
}

function woocommerce_subcategories_widget_register() {
    register_widget('woocommerce_subcategories_widget');
}
add_action('widgets_init', 'woocommerce_subcategories_widget_register');
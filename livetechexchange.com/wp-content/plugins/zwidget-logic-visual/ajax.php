<?php
add_action('wp_ajax_widget-logic-options', 			'widget_logic_visual_options');


add_action('wp_ajax_widget-logic-save',				'widget_logic_visual_save');
add_action('wp_ajax_widget-logic-update',			'widget_logic_visual_update');
add_action('wp_ajax_widget-logic-delete-option',	'widget_logic_visual_delete');

add_action('wp_ajax_widget-logic-add-tags',			'widget_logic_visual_conditional_tags');

add_action('wp_ajax_widget-logic-add-options',		'widget_logic_visual_add_options');
add_action('wp_ajax_widget-logic-edit-option',		'widget_logic_visual_edit_options');
add_action('wp_ajax_widget-logic-more-options', 	'widget_logic_visual_more_options');
add_action('wp_ajax_widget-logic-update-conditional-tags'	,'widget_logic_visual_update_conditional_tags');
add_action('wp_ajax_widget-logic-update-visibility'			,'widget_logic_visual_update_visibility');

// ========================================================	//
// ==					SAVE WIDGET LOGIC				==	//
// ========================================================	//
function widget_logic_visual_save()
{
	$widget_id		= $_POST['widgetID'];
	$widget_options	= $widget_id.'-visibility';
	
	if(isset($_POST['nwlv']['ids']) && !empty($_POST['nwlv']['ids'])) :
		$_POST['nwlv']['ids']	= explode(',', preg_replace('/\s*/m','',$_POST['nwlv']['ids']));
	endif;

	$nwlv_save[]		= $_POST['nwlv'];
	
	$visibility		= get_option($widget_options);
	$visibility		= ( is_array($visibility) && sizeof($visibility) > 0 ) ? array_merge( (array) $visibility , $nwlv_save) : $nwlv_save;
	
	update_option($widget_options,$visibility);
	widget_logic_visual_list_visibility_options($widget_id);
	
	exit();	
}

// ========================================================	//
// ==					UPDATE WIDGET LOGIC				==	//
// ========================================================	//
function widget_logic_visual_update()
{
	$widget_id		= $_POST['widgetID'];
	$widget_options	= $widget_id.'-visibility';
	$visOption		= $_POST['visOption'];

	if(isset($_POST['nwlv']['ids']) && !empty($_POST['nwlv']['ids'])) :
		$_POST['nwlv']['ids']	= explode(',', preg_replace('/\s*/m','',$_POST['nwlv']['ids']));
	endif;
	
	$visibility		= get_option($widget_options);
	$visibility[$visOption]	= ( is_array($visibility) && sizeof($visibility) > 0 && isset($visibility[$visOption])) ? $_POST['nwlv'] : $visibility[$visOption];
	
	update_option($widget_options,$visibility);

	widget_logic_visual_list_visibility_options($widget_id);
	
	exit();	
}

// ========================================================	//
// ==					DELETE WIDGET LOGIC				==	//
// ========================================================	//

function widget_logic_visual_delete()
{
	$widget_id		= $_POST['widgetID'];
	$visi_id		= $_POST['visOption'];
	
	$widget_options	= $widget_id.'-visibility';
	$visibility		= get_option($widget_options);
	
	unset($visibility[$visi_id]);
	update_option($widget_options,$visibility);
	
	widget_logic_visual_list_visibility_options($widget_id);
	exit();	
}

// ========================================================	//
// ==				  UPDATE CONDITIONAL TAGS			==	//
// =======================================================	//

function widget_logic_visual_update_conditional_tags()
{	
	$nwlv		= $_POST['nwlv'];
	$widgetID	= $_POST['widgetID'];
	
	update_option($widgetID.'-conditional-tags',$nwlv['cod-tag']);
	update_option($widgetID.'-conditional-tags-activate',$nwlv['activate']);
	
	widget_logic_visual_list_visibility_options($widgetID);
	
	exit();
}

// ========================================================	//
// ==					UPDATE VISIBILITY				==	//
// =======================================================	//

function widget_logic_visual_update_visibility()
{
	$widgetID	= $_POST['widgetID'];
		
	widget_logic_visual_list_visibility_on_widget($widgetID);
		
	exit();	
}

// ========================================================	//
// ==				   WIDGET LOGIC OPTIONS				==	//
// ========================================================	//

function widget_logic_visual_options()
{
	$id_disp	= $_POST['widgetID'];

	?>
    <div id="nwlv-dialog" class="wrap">  
    
    	<h2><img src="<?php echo WLVPLUGINURL; ?>/images/logo-title.png" alt="" /></h2>
        <br />
        <div id="nwlv-buttons">
	        <a href="#" id='nwlv-add-option' class='button-primary'>Add New Limitation ( Visual )</a>&nbsp; or &nbsp;
    	    <a href="#" id='nwlv-add-tag' class='button'>Using Conditional Tag Code ( Advanced )</a>
		</div>
        
        <div id="nwlv-add-option-holder"></div>
		<div id="nwlv-list-visibility"><?php widget_logic_visual_list_visibility_options($id_disp); ?></div>
        
        <script type="text/javascript" language="javascript1.2">
		jQuery(document).ready(function(){
			jQuery('#nwlv-add-option').click(function(){
				var value	= jQuery(this).val();
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				var data 	= {
								action		: 'widget-logic-add-options',
								widgetID	: "<?php echo $id_disp; ?>"
					   	  	  };
							  
				jQuery.post(ajaxurl, data, function(response) {
					if(response == 'disabled') {
						alert("Please remove/delete conditional tag code limitations before adding visual limitations - you can have one or the other per widget, not both");	
					} else {
						jQuery('#nwlv-add-option-holder').html(response);
						jQuery('#nwlv-buttons').hide();
					}
				});
				
				return false;
			});
			
			jQuery('#nwlv-add-tag').click(function(){
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				var data 	= {
								action		: 'widget-logic-add-tags',
								widgetID	: "<?php echo $id_disp; ?>"
					   	  	  };
							  
				jQuery.post(ajaxurl, data, function(response) {
					if(response == 'disabled') {
						alert("Please remove/delete visual limitations before adding conditional code - you can have one of the other per widget, not both");
					} else {
						jQuery('#nwlv-add-option-holder').html(response);
						jQuery('#nwlv-buttons').hide();
					}
				});
				return false;
			});
		});
		</script>
	</div>
    <?php
	exit();
}

// ========================================================	//
// ==			   WIDGET LOGIC CONDITIONAL TAGS		==	//
// ========================================================	//

function widget_logic_visual_conditional_tags()
{
	$widgetID	= $_POST['widgetID'];
	$visual_cond		= get_option($widgetID.'-visibility');
	$cond_tags			= get_option($widgetID.'-conditional-tags');
	$cond_tags_activate	= get_option($widgetID.'-conditional-tags-activate');
	$checked			= (!is_null($cond_tags_activate) && !empty($cond_tags_activate)) ? "checked='checked'" : "";
	
	if(!empty($visual_cond)) :
		echo "disabled";
		exit();
	endif;
	
	?>
    <div class="nwlv-add-options">
    
    	<h3>Add Conditional Tags</h3>
    
    	<form id="nwlv-conditional-tags" class="nwlv-form" method="post" action="">	
	        <div class="nwlv-input">
    	    	<label>Conditional Tags</label>
                <textarea id="nwlv-cod-tag" name="nwlv[cod-tag]" rows="3" cols="50"><?php echo $cond_tags; ?></textarea>
                <div class="clearfix"></div>
	        </div>
            
            <div class="nwlv-input">
            	<label>Activate the code?</label>
                <input type="checkbox" id="nwlv-activate" name="nwlv[activate]" <?php echo $checked; ?> value="1" />
                <div class="clearfix"></div>
            </div>
            
            <div class="nwlv-input">
            	<label>Sample</label>
                <div style="float:left">
                	<ul>
                    	<li><strong>is_home()</strong> or <strong>is_front_page()</strong></li>
                        <li><strong>is_single()</strong>,<strong>is_page()</strong> or <strong>is_attachment()</strong></li>
                        <li><strong>is_category()</strong>,<strong>is_archive()</strong> or <strong>is_tag()</strong></li>
                        <li> or you can find more conditional tags <a href="http://codex.wordpress.org/Conditional_Tags" title="more conditional tags">here</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="clearfix"></div>
        
			<input type="hidden" name="widgetID" value="<?php echo $widgetID; ?>" />
            <input type="hidden" name="action" value="widget-logic-update-conditional-tags" />
			<input class="button-primary" type="submit" name="submit" value="Update" />
			<input id='nwlv-cancel-button' class="button" type="button" name="submit" value="Cancel" />
        </form>
        
        <script type="text/javascript" language="javascript1.2">
			jQuery(document).ready(function(){
				
				jQuery('#nwlv-conditional-tags').submit(function(){
					var data	= jQuery(this).serialize();
					var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
					
					
					jQuery.post(ajaxurl, data, function(response) {
						jQuery('#nwlv-buttons').show();
						jQuery('#nwlv-list-visibility').html(response);
						jQuery('#nwlv-add-option-holder').html('');
						
						var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
						var data 	= {
										action		: 'widget-logic-update-visibility',
										widgetID	: "<?php echo $widgetID; ?>"
									  };
									  
						jQuery.post(ajaxurl, data, function(response) {
							jQuery('#visibility-<?php echo $widgetID; ?>').html(response);
						});
					});
					
					return false;
				});
				
				jQuery('#nwlv-cancel-button').click(function(){
					jQuery('#nwlv-buttons').show();
					jQuery('#nwlv-add-option-holder').html('');
					return false;
				});
			});
		</script>
        
	</div>
    <?php
	exit();
}

// ========================================================	//
// ==			    WIDGET LOGIC LIST OPTIONS			==	//
// ========================================================	//

function widget_logic_visual_list_visibility_options($widgetID)
{
	$visibilities	= get_option($widgetID.'-visibility');	
	$cond_tag		= get_option($widgetID.'-conditional-tags');
	$cond_act		= get_option($widgetID.'-conditional-tags-activate');
	
	if(!is_null($cond_act) && !empty($cond_act)) :
	
	?>
	<div class="nwlv-list-options">
    	Current widget is using conditional tags()
        <code><?php echo $cond_tag; ?></code>
    </div>
    <?php
	
	elseif(is_array($visibilities) && sizeof($visibilities) > 0) :
		
		foreach($visibilities as $key => $option) :
		
			$not			= ( isset($option['not']) && ($option['not'] == 'not') ) ? "not" : "";
			$list_content	= widget_logic_visual_list_contents($option);
		?>
		<div class="nwlv-list-options">
			<a href="#" class="nwlv-edit-option button" rel="<?php echo $key; ?>">edit</a>
			<a href="#" class="nwlv-delete-option button" rel="<?php echo $key; ?>">delete</a> &nbsp;&nbsp;&nbsp;
			this widget will <?php echo "<span style='font-weight:bold;color:red;'>".$not."</span>"; ?> show in <strong><?php echo $option['show']; ?></strong> page
			<span style="font-weight:bold;color:green;"><?php echo $list_content; ?></span>
		</div>
		<?php
		endforeach;
		
		?>
		<script type="text/javascript" language="javascript1.2">
		jQuery(document).ready(function(){
			
			jQuery('.nwlv-edit-option').click(function(){
				
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				var data 	= {
								action		: 'widget-logic-edit-option',
								widgetID	: "<?php echo $widgetID; ?>",
								visOption	: jQuery(this).attr('rel')
							  };
								  
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#nwlv-add-option-holder').html(response);
				});
				
				return false;
			});
			
			jQuery('.nwlv-delete-option').click(function(){
	
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				var data 	= {
								action		: 'widget-logic-delete-option',
								widgetID	: "<?php echo $widgetID; ?>",
								visOption	: jQuery(this).attr('rel')
							  };
								  
				jQuery.post(ajaxurl, data, function(response) {
					
					jQuery('#nwlv-list-visibility').html(response);
					
					var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
					var data 	= {
									action		: 'widget-logic-update-visibility',
									widgetID	: "<?php echo $widgetID; ?>"
								  };
								  
					jQuery.post(ajaxurl, data, function(response) {
						jQuery('#visibility-<?php echo $widgetID; ?>').html(response);
					});
				});
				
				return false;
			});
		});
		</script>
		<?php
		
	else :
	
		?>
        <div>
        	<p>Choose an option to get started...</p>
            
            <p>"Add New Limitation" is point and click style widget placement</p>
            
            <p>
            	"Using Conditional Tag Code" is for advanced users that want to use wordpress template conditional tag code.<br />
                You can choose one or the other ( per widget )
            </p>
        </div>
        <?php
	
	endif;
}

// ========================================================	//
// ==			    WIDGET LOGIC LIST CONTENTS			==	//
// ========================================================	//

function widget_logic_visual_list_contents($data)
{
	$showin	= "";
	
	switch($data['show']) :
	
		case "all"		:
		case "home"		:
		case "search"	:
		case "page-404"	: break;
		
		case "author"	: $ids	= (isset($data['select']) && is_array($data['select']) && sizeof($data['select']) > 0 ) ? $data['select'] : NULL;
						  
						  if(!is_null($ids) && is_array($ids) && sizeof($ids) > 0) :
						  
						  	foreach($ids as $user_id) :
								$user		= get_userdata($user_id);
								$showin[]	= $user->user_nicename;
							endforeach;
							
							$showin	= "[ ".implode(', ',$showin)." ]";
						  
						  endif;
						  
						  break;
		
		case "category"	: $ids	= (isset($data['select']) && is_array($data['select']) && sizeof($data['select']) > 0 ) ? $data['select'] : NULL;
						  
						  if(!is_null($ids) && is_array($ids) && sizeof($ids) > 0) :
						  
						  	foreach($ids as $cat_id) :
								$showin[]	= get_cat_name($cat_id);
							endforeach;
							
							$showin	= "[ ".implode(', ',$showin)." ]";
						  
						  endif;

						  break;
		
		case "tag"		: $ids	= (isset($data['select']) && is_array($data['select']) && sizeof($data['select']) > 0 ) ? $data['select'] : NULL;
						  
						  if(!is_null($ids) && is_array($ids) && sizeof($ids) > 0) :
						  
						  	foreach($ids as $tag_slug) :
								$the_tag	= get_term_by('slug',$tag_slug,'post_tag');
								$showin[]	= $the_tag->name;
							endforeach;
							
							$showin	= "[ ".implode(', ',$showin)." ]";
						  
						  endif;

						  break;
		
		default			: $ids	= (isset($data['select']) && is_array($data['select']) && sizeof($data['select']) > 0 ) ? $data['select'] : NULL;
					 	  if(!is_null($ids) && is_array($ids) && sizeof($ids) > 0) :
						  
					  		wp_reset_query();
							
							if(sizeof($ids) > 1) :
								$args	= array(
									'post__in'	=> $ids,
									'post_type'	=> $data['show'],
								);
							elseif(sizeof($ids) == 1) :
								$args	= array(
									'p'	=> $ids[0],
									'post_type'	=> $data['show'],
								);
							endif;
							
							query_posts($args);
							
							if(have_posts()) :
							while(have_posts()) :
								the_post();
								$showin[]	= get_the_title();
							endwhile;
							endif;
							
							$showin	= "[ ".implode(', ',$showin)." ]";
					  	  endif;
					  
					  	  break;
	
	endswitch;
	
	return $showin;	
}

// ========================================================	//
// ==				 WIDGET LOGIC ADD OPTIONS			==	//
// ========================================================	//

function widget_logic_visual_add_options()
{
	$id_disp	= $_POST['widgetID'];
	$cond_tag	= get_option($id_disp.'-conditional-tags');
	$cond_act	= get_option($id_disp.'-conditional-tags-activate');
	
	if(!empty($cond_act)) :
		echo 'disabled';
	else :
		widget_logic_visual_more_extra_control($id_disp);
	endif;

	exit();	
}

// ========================================================	//
// ==				 WIDGET LOGIC EDIT OPTIONS			==	//
// ========================================================	//

function widget_logic_visual_edit_options()
{
	$widgetID		= $_POST['widgetID'];
	$widget_options	= $widgetID.'-visibility';
	$visibility		= get_option($widget_options);
	
	widget_logic_visual_more_extra_control($widgetID,$visibility[$_POST['visOption']],true);
	exit();	
}

// ========================================================	//
// ==			    WIDGET LOGIC EXTRA CONTROL			==	//
// ========================================================	//

function widget_logic_visual_more_extra_control($widgetID,$value = NULL,$edit = false)
{
	$options	= array(
		'show-in'	=> array(
			//'all'			=> 'All',
			'home'			=> 'Homepage',
			'search'		=> 'Search',
			'page-404'		=> '404 Page',
			'author'		=> 'Author',
			'post'			=> 'Post',
			'page'			=> 'Page',
			'category'		=> 'Category',
			'tag'			=> 'Tag',
			'attachment'	=> 'Attachment',
		)
	);
	
	// to get post type options
	$args	= array(
  		'public'   => true,
  		'_builtin' => false
	); 
	
	$post_types	= get_post_types($args);
	// end here
	
	$options['show-in']	= array_merge($options['show-in'],$post_types);
	$onlyshowin	=  get_option($widgetID.'-only_show_in');
	
	$i = 0;
	?>
    <div class="nwlv-add-options">
    
    	<?php if($edit): ?>
    	<form id="nwlv-edit-form" class="nwlv-form" method="post" action="">
        <?php else : ?>
    	<form id="nwlv-add-form" class="nwlv-form" method="post" action="">
        <?php endif; ?>
        
        	<?php if($edit): ?>
        	<h3>Edit Limitation</h3>
            <?php else : ?>
        	<h3>Add Limitation</h3>
            <?php endif; ?>
    
    		<div class="nwlv-input">
                <label for='nwlv-show-in'>Template Section</label>
    
                <select name='nwlv[show]' id='nwlv-show-in'>
                <?php foreach($options['show-in'] as $key => $show) : ?>
                    <?php $selected	= ( $key == $value['show'] ) ? "selected='selected'" : ""; ?>
                    <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $show; ?></option>
                <?php endforeach; ?>
                </select>
                <div class="clearfix"></div>
            </div>
        
	        <div id="nwlv-post-id">
			<?php 
			if($edit) : 
				widget_logic_visual_more_options(false,$widgetID,$value['show'],$value);  
			endif;
			?>
			</div>
    
    		<div class="nwlv-input">
				<?php $checked	= (isset($value['not']) && $value['not'] == 'not') ? "checked='checked'" : NULL ; ?>
                <label for="nwlv-not">Except</label>
                <input id='nwlv-not' type="checkbox" name="nwlv[not]" value="not" <?php echo $checked; ?> />
			</div>
        
        	<br /><br />
        	
            <input type="hidden" name="widgetID" value="<?php echo $widgetID; ?>" />
            <input type="hidden" name="action" value="widget-logic-save" />
            <?php if($edit) : ?>
            <input type="hidden" name="action" value="widget-logic-update" />
			<input type="hidden" name="visOption" value="<?php echo $_POST['visOption']; ?>" />
        	<input class="button-primary" type="submit" name="submit" value="Update" />
            <?php else: ?>
        	<input class="button-primary" type="submit" name="submit" value="Add" />
            <?php endif; ?>
			<input id='nwlv-cancel-button' class="button" type="button" name="submit" value="Cancel" />
        
		</form>
        
    </div>
    

    <script type="text/javascript" language="javascript1.2">
		jQuery(document).ready(function(){
			jQuery('#nwlv-show-in').change(function(){
				var value	= jQuery(this).val();
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				var data 	= {
								action	: 'widget-logic-more-options',
								show	: value,
								ajax	: true,
								id_disp	: "<?php echo $widgetID; ?>"
					   	  	  };
							  
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#nwlv-post-id').html(response);
				});
				
			});
			
			jQuery('#nwlv-cancel-button').click(function(){
				jQuery('#nwlv-buttons').show();
				jQuery('#nwlv-add-option-holder').html('');
				return false;
			});
			
			jQuery('#nwlv-edit-form').submit(function(){
				var data	= jQuery(this).serialize();
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#nwlv-buttons').show();
					jQuery('#nwlv-list-visibility').html(response);
					jQuery('#nwlv-add-option-holder').html('');
					
					var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
					var data 	= {
									action		: 'widget-logic-update-visibility',
									widgetID	: "<?php echo $widgetID; ?>"
								  };
								  
					jQuery.post(ajaxurl, data, function(response) {
						jQuery('#visibility-<?php echo $widgetID; ?>').html(response);
					});
				});
				
				return false;
			});
			
			jQuery('#nwlv-add-form').submit(function(){
				var data	= jQuery(this).serialize();
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#nwlv-buttons').show();
					jQuery('#nwlv-list-visibility').html(response);
					jQuery('#nwlv-add-option-holder').html('');
					
					var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
					var data 	= {
									action		: 'widget-logic-update-visibility',
									widgetID	: "<?php echo $widgetID; ?>"
								  };
								  
					jQuery.post(ajaxurl, data, function(response) {
						jQuery('#visibility-<?php echo $widgetID; ?>').html(response);
					});
				});
				
				return false;
			});
		});
	</script>
    <?php
}

// ========================================================	//
// ==			    WIDGET LOGIC MORE OPTIONS			==	//
// ========================================================	//

function widget_logic_visual_more_options($ajax = true,$widget = NULL,$show = NULL,$value = NULL)
{
	$widget	= ( is_null($widget) ) 		? $_POST['id_disp'] : $widget;
	$show	= ( is_null($show) ) 		? $_POST['show'] : $show;
	$value	= ( is_null($value) )		? NULL : $value;
	$ajax	= ( isset($_POST['ajax'])) 	? $_POST['ajax'] : $ajax;
	
	switch($show) :
	
		case 'all'		: 
		case 'home'		: 
		case 'search'	: 
		case 'page-404'	: break;
		
		case 'author' 	: widget_logic_visual_the_options($widget,'author',$value);
						  break;
		
		case 'category' : widget_logic_visual_the_options($widget,'category',$value);
						  break;
						  
		case 'tag'	 	: widget_logic_visual_the_options($widget,'tag',$value);
						  break;
						  
		default			: widget_logic_visual_the_options($widget,'post',$value,$show);
					  	  break;
	
	endswitch;
	
	if($ajax) :
		exit();
	endif;
}

// ========================================================	//
// ==			   WIDGET LOGIC THE OPTIONS			==	//
// ========================================================	//

function widget_logic_visual_the_options($widgetID,$type,$value,$posttype = NULL)
{	
	switch($type) :
	
		case "author"	: $options	= get_users(); 		break;
		case "category"	: $options	= get_categories(); break;
		case "tag"		: $options	= get_tags();		break;
		case "post"		: $args	= array(
							'numberposts'	=> -1,
							'post_type'		=> $posttype
						  );
	
						  $options	= get_posts($args);
						  break;
	
	endswitch;
	
	$checked	= ( !is_array($value['select']) && sizeof($value['select']) == 0) ? "checked='checked'" : "";
	
	?>
	<div class="nwlv-input">
    	<label for="nwlv-select">Show in</label>
        <input type="checkbox" name="nwlv[all]" id="nwlv-all" value="all" <?php echo $checked; ?> /> &nbsp; All <?php echo ucwords($type); ?>(s) <br />
        
        <div id="nwlv-select-holder">
            <select name='nwlv[select][]' id='nwlv-select' multiple="multiple" size="8" >
            <?php 
				foreach($options as $option) : 
					switch($type) :
						case "author"	: $id		= $option->ID;
										  $title	= $option->user_nicename;
										  break;
										  
						case "category"	: $id		= $option->term_id;
										  $title	= $option->name;
										  break;
										  
						case "tag"		: $id		= $option->slug;
										  $title	= $option->name;
										  break;
										  
						case "post"		: $id		= $option->ID;
										  $title	= $option->post_title;
										  break;
					endswitch;
				
				$selected = ( is_array($value['select']) && in_array($id,$value['select'])) ? "selected='selected'" : ""; 
			?>
                <option value="<?php echo $id; ?>" <?php echo $selected; ?>><?php echo $title; ?></option>
            <?php endforeach; ?>
            </select>
            <div class="clearfix"></div>
            <em style="font-size:10px;">Hold on <i>Ctrl</i> and click for multiple options. To show in everypost, clear the options</em>
        </div>
        
        <div class="clearfix"></div>
	</div>

    <script type="text/javascript" language="javascript1.2">
	jQuery(document).ready(function(){
		
		var checkedYes	= jQuery('#nwlv-all').is(":checked");
		var selectedYes	= jQuery('#nwlv-select').find('option').is(':selected');
		
		if(checkedYes)
		{ 
			jQuery('#nwlv-select').find('option:selected').removeAttr('selected'); 
			jQuery('#nwlv-select-holder').hide();
		}
		else
		{ 	jQuery('#nwlv-select-holder').css('display','block'); }
		
		jQuery('#nwlv-all').click(function(){
			var checkedYes	= jQuery(this).is(":checked");
			
			if(checkedYes)
			{ 
				jQuery('#nwlv-select').find('option:selected').removeAttr('selected'); 
				jQuery('#nwlv-select-holder').hide();
			}
			else
			{ jQuery('#nwlv-select-holder').css('display','block'); }
		});
		
		jQuery('#nwlv-select').click(function(){
			jQuery('#nwlv-all').removeAttr('checked');
		});
	});
	</script>


    <?php
}
?>
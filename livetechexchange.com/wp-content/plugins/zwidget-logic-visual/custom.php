<?php
function widget_logic_visual_list_visibility_on_widget($widgetID)
{
	$cond_tag		= get_option($widgetID.'-conditional-tags');
	$cond_act		= get_option($widgetID.'-conditional-tags-activate');
	$visibilities	= get_option($widgetID.'-visibility');	
	
	if(!is_null($cond_act) && !empty($cond_act)) :
	
		?>
		<div class="nwlv-list-options">
        	this widget is using conditional tags <br />
			<code><?php echo $cond_tag; ?></code>
        </div>
		<?php
	
	elseif(is_array($visibilities) && sizeof($visibilities) > 0) :
		foreach($visibilities as $key => $option) :
		
			$not			= ( isset($option['not']) && ($option['not'] == 'not') ) ? "not" : "";
			$list_content	= widget_logic_visual_list_contents($option);
		?>
		<div class="nwlv-list-options">
			<?php echo "<span style='font-weight:bold;color:red;'>".$not."</span>"; ?> show in <strong><?php echo $option['show']; ?></strong> page
			<span style="font-weight:bold;color:green;"><?php echo $list_content; ?></span>
		</div>
		<?php
		
		endforeach;
	else :
	
		?>This widget has no limitation<?php
		
	endif;
}

function widget_logic_visual_check_visibility($widgetID)
{
	$cond_tag	= get_option($widgetID.'-conditional-tags');
	$cond_act	= get_option($widgetID.'-conditional-tags-activate');
	$visibility	= get_option($widgetID.'-visibility');
	
	$delete		= false;
	$eval		= array();
	
	if(!empty($cond_act) && !is_null($cond_act)) :
	
		$the_eval	= "return ".$cond_tag.";";
		
		$valid	= eval($the_eval);
			
		if($valid) :
			return false;
		else :
			return true;
		endif;
	
	else :

		if(!empty($visibility) && sizeof($visibility) > 0) :
		
			$the_visi	= array();
			
			foreach($visibility as $visi) :
			
				if(!isset($visi['not'])) :
					$the_visi[$visi['show']]['show']	= $visi['select'];
					$eval[$visi['show']]	= widget_logic_visual_selection_function($visi['show'],$eval,$visi['select'],false);
				else :
					$the_visi[$visi['show']]['not']	= $visi['select'];
					$eval[$visi['show']]	= widget_logic_visual_selection_function($visi['show'],$eval,$visi['select'],true);
				endif;
	
			endforeach;
			
			$the_eval	= "return ".implode(" || ",$eval).";";
			
			$valid	= eval($the_eval);
			
			if($valid) :
				return false;
			else :
				return true;
			endif;
	
		endif;
		
	endif;

	return false;
}

// checking for post selection
function widget_logic_visual_selection_post($post_type,$the_visi)
{
	global $post;
	$postid	= $post->ID;
	
	if(array_key_exists($post_type,$the_visi)) :
			
		$show	= ( array_key_exists('show',$the_visi[$post_type]) ) 	? $the_visi[$post_type]['show'] : "empty";
		$not	= ( array_key_exists('not',$the_visi[$post_type]) ) 	? $the_visi[$post_type]['not'] : "empty";
		
			
		//checking for all first
		if($show <> "empty" && empty($show)) :
			$delete	= false;
		elseif($not <> "empty" && empty($not)) :
			$delete	= true;
		endif;
		
		if(is_array($show) && in_array($postid,$show)) :
			$delete	= false;
		endif;
		
		if(is_array($not) && in_array($postid,$not)) :
			$delete	= true;
		endif;
				
		return $delete;
				
	endif;
			
	//return true;	
	
}

// checking for taxonomy  selection
function widget_logic_visual_selection_taxonomy($taxonomy,$the_visi)
{
	
	if(array_key_exists($taxonomy,$the_visi)) :
			
		$show	= ( array_key_exists('show',$the_visi[$taxonomy]) ) ? $the_visi[$taxonomy]['show'] 	: "empty";
		$not	= ( array_key_exists('not',$the_visi[$taxonomy]) ) 	? $the_visi[$taxonomy]['not'] 	: "empty";
		
		$show	= ( is_tag() ) ? widget_logic_visual_convert_tag_id($show)	: $show;
		$not	= ( is_tag() ) ? widget_logic_visual_convert_tag_id($not)	: $not;
			
		//checking for all first
		if($show <> "empty" && empty($show)) :
			$delete	= false;
		elseif($not <> "empty" && empty($not)) :
			$delete	= true;
		endif;
		
		switch($taxonomy) :
		
			case "category"	: if(is_category($show)) 	$delete = false;
							  if(is_category($not))		$delete	= true;
							  
							  break;
							  
			case "tag"		: if(is_array($show) && is_tag($show)) 	$delete = false;
							  if(is_array($not) && is_tag($not))	$delete	= true;
							  
							  break;
							  
			default			: if(is_array($show) && is_tax($taxonomy,$show)) :
				  			    $delete	= false;
							  endif;
		
							  if(is_array($not) && is_tax($taxonomy,$show)) :
							    $delete	= true;
							  endif;
							  break;
		
		endswitch;
				
		return $delete;
				
	endif;
			
	//return true;	
}

// convert tag id into tag slug
function widget_logic_visual_convert_tag_id($tags)
{
	if(is_array($tags) && sizeof($tags) > 0) :
		
		foreach($tags as $key => $tag) :
			$the_tag	= get_term_by('id',$tag,'post_tag');
			$tags[$key]	= $the_tag->slug;
		endforeach;
		
		return $tags;	
		
	endif;
	
	//return true;
}

// convert selection into logical function
function widget_logic_visual_selection_function($type,$current_eval,$value,$negation = false)
{
	$eval	= "";
	switch($type) :
		
		case "home"			: $eval	= "(is_home() || is_front_page())";	break;
		case "search"		: $eval = "is_search()";					break;
		case "page-404"		: $eval	= "is_404()";						break;
		
		case "author"		: $eval	= "is_author(";						
 							  if(is_array($value) && sizeof($value) > 0) :
								  $eval	.= "array(".implode(",",$value).")";
							  endif;
							  $eval .= ")";
							  break;
							  
		case "category"		: $eval	= "is_category(";
 							  if(is_array($value) && sizeof($value) > 0) :
								  $eval	.= "array(".implode(",",$value).")";
							  endif;
							  $eval .= ")";
							  break;
							  
		case "tag"			: $eval	= "is_tag(";
 							  if(is_array($value) && sizeof($value) > 0) :
								  $eval	.= "array(".implode(",",$value).")";
							  endif;
							  $eval .= ")";
							  break;
							  
		case "page"			: $eval	= "is_page(";
 							  if(is_array($value) && sizeof($value) > 0) :
								  $eval	.= "array(".implode(",",$value).")";
							  endif;
							  $eval .= ")";
							  break;
							  
		case "post"			: $eval	= "is_single(";
 							  if(is_array($value) && sizeof($value) > 0) :
								  $eval	.= "array(".implode(",",$value).")";
							  endif;
							  $eval .= ")";
							  break;
		
		case "attachment"	: $eval	= "(is_attachment(";
 							  if(is_array($value) && sizeof($value) > 0) :
								  $eval	.= "array(".implode(",",$value).")";
							  endif;
							  $eval .= ") ";
							  $eval	.= "&& 'attachment' == get_post_type())";
							  break;
							   
		default				: $eval	= "(is_single(";
 							  if(is_array($value) && sizeof($value) > 0) :
								  $eval	.= "array(".implode(",",$value).")";
							  endif;
							  $eval .= ")";
							  $eval	.= "&& '".$type."' == get_post_type())";

							  break;
		
	endswitch;
	
	$eval	= ($negation) 					? "!".$eval : $eval;
	$eval	= isset($current_eval[$type]) 	? $current_eval[$type].' && '.$eval : $eval;

	return $eval;
}
?>
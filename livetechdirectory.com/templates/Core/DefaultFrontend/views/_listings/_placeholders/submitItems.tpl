<div class="submiItems">
    {foreach from=$SUBMIT_ITEMS item=item name=submit_items}
        {if $item.IS_DETAIL eq '1'}
        <div class="phpld-grid phpld-equalize submitItem">
            <div class="phpld-label float-left">{l}{$item.NAME}{/l}:</div>
            <div class="smallDesc float-left">

                {if $item.TYPE eq 'FILE'}
                    <a href="{$smarty.const.DOC_ROOT}/uploads/{$LINK[$item.FIELD_NAME]}" target="_blank">{$LINK[$item.FIELD_NAME]}</a>
                {elseif $item.TYPE == 'BOOL'}
                    {if $LINK[$item.FIELD_NAME]|escape|trim == 1}{l}Yes{/l}{else}{l}No{/l}{/if}
                {elseif $item.TYPE == 'DROPDOWN'}
                    {$LINK[$item.FIELD_NAME]}
                {elseif $item.TYPE == 'TAGS'}
                    {foreach from=$LINK->getTags() item="tag"}
                        <span class="label">{$tag.TITLE}</span>
                    {/foreach}

                {elseif $item.TYPE == 'MULTICHECKBOX'}
                    {assign var="items" value=","|explode:$LINK[$item.FIELD_NAME]}
                    <ul class="multicheckbox">
                        {foreach from=$items item="item"}
                            <li>{$item}</li>
                        {/foreach}
                    </ul>
		      {elseif $item.TYPE eq 'VIDEO'} 
 <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/flowplayer-3.2.6.min.js"></script>
        <!--    <object type="application/x-shockwave-flash" data="player_flv_maxi.swf" width="352" height="288">
                 <param name="movie" value="player_flv_maxi.swf" />
                 <param name="FlashVars" value="flv=uploads/{$LINK[$item.FIELD_NAME]}&showfullscreen=1&autoplay=1&showstop=1&showvolume=1&showtime=1" />
            </object> -->

	    	<!-- this A tag is where your Flowplayer will be placed. it can be anywhere -->
		<a href="{$smarty.const.SITE_URL}uploads/{$LINK[$item.FIELD_NAME]}"
			 style="display:block;width:520px;height:330px"  
			 id="player"> </a> 
	
		<!-- this will install flowplayer inside previous A- tag. -->
		<script>
			flowplayer("player", "{$smarty.const.SITE_URL}flowplayer-3.2.7.swf",{literal} {
    clip:  {
        autoPlay: false,
        autoBuffering: true
    }
	});{/literal}	</script>
         
           
                {elseif $item.TYPE == 'URL_IMAGE'}
                    <img src="{$smarty.const.DOC_ROOT}/uploads/{$LINK[$item.FIELD_NAME]}" />
     
	  	{else}
          {$LINK[$item.FIELD_NAME]}
                {/if}

            </div>
        </div>
        {/if}
    {/foreach}
 {if !empty($group_image_details)}
  <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/jcarousel.css" />
 <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.jcarousel.min.js"></script>
 <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="{$smarty.const.DOC_ROOT}/javascripts/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

<script type="text/javascript">
{literal}
jQuery(document).ready(function() {
jQuery('#mycarousel').jcarousel({
        visible: 4
    });
jQuery("a.image_list").fancybox();
});
{/literal}
</script>
       
	  
          	<ul id="mycarousel" class="jcarousel-skin-tango">
              		{foreach from=$group_image_details item=group_image name=group_images}
              			<li>
				    <a href="{$smarty.const.DOC_ROOT}/uploads/{$group_image.IMAGE}" class="image_list" rel="image_list" >
				    <img src="{$smarty.const.DOC_ROOT}/uploads/thumb/{$group_image.IMAGE}" border="0" style="border: 1px dotted grey;width:100px;"  />
				    </a>
				 </li>
              		{/foreach}
              </ul>
    
	{/if}
</div>
{if $linkColumns == 2}
    {assign var="gridClass" value='phpld-g50 woo'}
{elseif  $linkColumns == 3}
    {assign var="gridClass" value='phpld-g33 woo'}
{elseif  $linkColumns == 4}
    {assign var="gridClass" value='phpld-g25 woo'}
{elseif  $linkColumns == 5}
    {assign var="gridClass" value='phpld-g20 woo'}
{else}
    {assign var="gridClass" value='phpld-gbox '}
{/if}


{if $linkColumns > 1}
    <script type="text/javascript">
        {literal}    
    $(window).load(function() { 
            $('img').each(function(){
                $(this).attr('height', jQuery(this).outerHeight());
            });  
    });    

    var handler{/literal}{$widgetID}{literal} = null;   
    $(document).ready(function() {
            
        if($('#woogrid{/literal}{$widgetID}{literal} div.woo').length > 1){  
        //if(handler) handler.wookmarkClear();    
        handler{/literal}{$widgetID}{literal} = $('#woogrid{/literal}{$widgetID}{literal} div.woo');

            var options = {
                autoResize: true,  
                container: jQuery('#woogrid{/literal}{$widgetID}{literal}'), // Optional, used for some extra CSS styling
                offset: 0 // Optional, the distance between grid items       
            };
        setInterval( function() { handler{/literal}{$widgetID}{literal}.wookmark(options); } , 1000)
            
      }
      //setTimeout(setWook(), 1000);
    });
        {/literal}
    </script> 
{/if}
{assign var="i" value=1}

<div class="phpld-grid listing-style-{$linkStyle}" {if $linkColumns > 1} id="woogrid{$widgetID}" {/if} style="position: relative;">
    {foreach from=$links item=LINK name=name}
        <div class="{$gridClass}{if $LINK.FEATURED} featured{/if}">
            {$LINK->listing($linkStyle)}
        </div>
    {/foreach}
</div>

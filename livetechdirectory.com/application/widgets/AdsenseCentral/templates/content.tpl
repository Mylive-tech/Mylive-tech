{if $set.EMBED neq ''}
{$set.EMBED}
{else}
{if $set.PUBLISHER_ID eq ''}
<p style="color: green">The {$TYPE} widget needs the publisher id set up in order to work.</p>
{else}
<div style="width: {$WIDTH}px; height: {$HEIGHT}px; margin: 0px auto;">
{literal}
<script type="text/javascript">
<!--
google_ad_client = "{/literal}{$set.PUBLISHER_ID}{literal}";
google_ad_width = {/literal}{$WIDTH}{literal};
google_ad_height = {/literal}{$HEIGHT}{literal};
google_ad_type = "text";

google_color_border = "{/literal}{$set.BORDER_COLOR}{literal}";
google_color_bg = "{/literal}{$set.BACKGROUND_COLOR}{literal}";
google_color_link = "{/literal}{$set.TITLE_COLOR}{literal}";
google_color_text = "{/literal}{$set.TEXT_COLOR}{literal}";
google_color_url = "{/literal}{$set.URL_COLOR}{literal}";
//-->

</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
{/literal}
</div>
{/if}
{/if}
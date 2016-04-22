{if $show_title eq 1}
{$TITLE}
<br/>
{/if}
<ul class="boxPopCats">
{if !empty($set.PARTNER1_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER1_URL}">{$set.PARTNER1_TITLE}</a></li>{/if}
{if !empty($set.PARTNER2_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER2_URL}">{$set.PARTNER2_TITLE}</a></li>{/if}
{if !empty($set.PARTNER3_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER3_URL}">{$set.PARTNER3_TITLE}</a></li>{/if}
{if !empty($set.PARTNER4_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER4_URL}">{$set.PARTNER4_TITLE}</a></li>{/if}
{if !empty($set.PARTNER5_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER5_URL}">{$set.PARTNER5_TITLE}</a></li>{/if}
{if !empty($set.PARTNER6_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER6_URL}">{$set.PARTNER6_TITLE}</a></li>{/if}
{if !empty($set.PARTNER7_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER7_URL}">{$set.PARTNER7_TITLE}</a></li>{/if}
{if !empty($set.PARTNER8_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER8_URL}">{$set.PARTNER8_TITLE}</a></li>{/if}
{if !empty($set.PARTNER9_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER9_URL}">{$set.PARTNER9_TITLE}</a></li>{/if}
{if !empty($set.PARTNER10_URL)}<li> <a {if $set.NEW_WINDOW eq "Yes"}target="_blank"{/if} href="{$set.PARTNER10_URL}">{$set.PARTNER10_TITLE}</a></li>{/if}
</ul>
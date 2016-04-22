<div class="listing-details listing-details-default">
    <div class="link-title">
		<a class="link-title-url" href="{$LINK.URL}">{$LINK.TITLE}</a>
	</div>
	<div class="description-detail">
        {$LINK.DESCRIPTION}
    </div>	
    {if $LINK.URL}<span class="link">{$LINK.URL|escape|trim}</span>{/if}
	    <div class="link-category">
		{l}Category{/l}: {$LISTING_CATEGORIES_DETAILS}
	</div>
    <div class="phpld-clearfix"></div>    
</div>

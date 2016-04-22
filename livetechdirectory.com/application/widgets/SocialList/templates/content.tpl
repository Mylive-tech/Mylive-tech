<div class="socialList" id="socialList{$id}">
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {ldelim}
		var networks = [
				{if !empty($linkedin)}{ldelim}name:'linkedin',id:'{$linkedin}'{rdelim},{/if}
				{if !empty($facebook)}{ldelim}name:'facebook',id:'{$facebook}'{rdelim},{/if}
				{if !empty($tumblr)}{ldelim}name:'tumblr',id:'{$tumblr}'{rdelim},{/if}
				{if !empty($pinterest)}{ldelim}name:'pinterest',id:'{$pinterest}'{rdelim},{/if}
				{if !empty($flickr_id) and !empty($flickr_key)}{ldelim}name:'flickr',id:'{$flickr_id}',apiKey:'{$flickr_key}'{rdelim}, {/if}
				{if !empty($youtube)}{ldelim}name:'youtube',id:'{$youtube}'{rdelim}, {/if}
				{if !empty($twitter)}{ldelim}name:'twitter',id:'{$twitter}'{rdelim}, {/if}
				{if !empty($googleplus)}{ldelim}name:'googleplus',id:'{$googleplus}/posts'{rdelim}, {/if}
				{if !empty($rss)}{ldelim}name:'rss',id:'{$rss}'{rdelim}, {/if}
				{if !empty($craigslist_id) and !empty($craigslist_area)}{ldelim}name:'craigslist',id:'{$craigslist_id}',areaName:'{$craigslist_area}{rdelim}, {/if}
				{ldelim}{rdelim}
			];
		networks.pop();

		jQuery('#socialList{$id}').socialist({ldelim}
			networks: networks
		{rdelim});
	{rdelim});
</script>
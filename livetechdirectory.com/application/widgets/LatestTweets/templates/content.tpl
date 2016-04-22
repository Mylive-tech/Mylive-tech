{literal}
<style>
	#twitter_div {
		padding: 5px 15px;
	}
	ul#twitter_update_list {
		list-style-type: none;
		padding: 0px;
	}
</style>
{/literal}

{if $USERNAME eq ''}
<p style="color: green; margin: 0px;">The LatestTweets widget needs the twitter username set up in order to work.</p>
{else}
<div id="twitter_div">
<ul id="twitter_update_list"></ul>
<a href="http://twitter.com/{$USERNAME}" id="twitter-link" style="display:block;text-align:right;">follow me on Twitter</a>
</div>
{literal}
<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/{/literal}{$USERNAME}{literal}.json?callback=twitterCallback2&amp;count={/literal}{$NO_OF_TWEETS}{literal}"></script>
{/literal}
{/if}
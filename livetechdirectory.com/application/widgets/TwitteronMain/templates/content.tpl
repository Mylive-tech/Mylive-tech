<center>{if $USERNAME eq ''}
<p style="color: green; margin: 0px;">The TwitteronMain widget needs the twitter username set up in order to work.</p>
{else}{if $TITLE ne ''}
{$TITLE}
<br />
{/if}{literal}
 
<script src="http://widgets.twimg.com/j/2/widget.js"></script> 
<script> 
new TWTR.Widget({
version: 2,
type: 'profile',
rpp: 30,
interval: 6000,
width: {/literal}{$WIDTH}{literal},
height: 279,
theme: {
shell: {
background: '{/literal}{$THEME_BACKGROUND}{literal}',
color: '{/literal}{$THEME_COLOR}{literal}'
},
tweets: {
background: '{/literal}{$TWEET_BACKGROUND}{literal}',
color: '{/literal}{$TWEET_COLOR}{literal}',
links: '{/literal}{$TWEET_LINKS}{literal}'
}
},
features: {
scrollbar: true,
loop: false,
live: false,
hashtags: true,
timestamp: true,
avatars: false,
behavior: 'all'
}
}).render().setUser('{/literal}{$USERNAME}{literal}').start();
</script> 
{/literal}
</center> 
</br> 
<center> 
<a href="http://twitter.com/{$USERNAME}" class="twitter-follow-button">Follow @{$USERNAME}</a> 
<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script> 
</center> 

{/if}
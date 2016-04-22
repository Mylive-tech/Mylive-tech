<div class="video-container">
    {$LISTING_VIDEO_HTML}
    {*
{if $LINK.VIDEO_TYPE eq 'YOUTUBE'}
    <object style="height: 390px; width: 640px">
        <param name="movie" value="http://www.youtube.com/v/{$LINK.VIDEO_ID}?version=3"></param>
        <param name="allowFullScreen" value="true"></param>
        <param name="allowScriptAccess" value="always"></param>
        <embed src="http://www.youtube.com/v/{$LINK.VIDEO_ID}?version=3" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="640" height="390"></embed>
    </object>
{elseif $LINK.VIDEO_TYPE eq 'HULU'}
    <object style="height: 390px; width: 640px">
        <param name="movie" value="{$LINK.EMBED_URL}"></param>
        <param name="allowFullScreen" value="true"></param>
        <embed src="{$LINK.EMBED_URL}" type="application/x-shockwave-flash"  width="640" height="390" allowFullScreen="true"></embed>
    </object>
{elseif $LINK.VIDEO_TYPE eq 'VIMEO'}
    <iframe src="http://player.vimeo.com/video/{$LINK.VIDEO_ID}?title=0&amp;byline=0&amp;portrait=0" width="640" height="390" frameborder="0" webkitAllowFullScreen allowFullScreen></iframe>
{elseif $LINK.VIDEO_TYPE eq 'DM'}
    <object style="height: 390px; width: 640px">
        <param name="movie" value="http://www.dailymotion.com/swf/video/{$LINK.VIDEO_ID}?background=493D27&foreground=E8D9AC&highlight=FFFFF0"></param>
        <param name="allowFullScreen" value="true"></param>
        <param name="allowScriptAccess" value="always"></param>
        <embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/video/{$LINK.VIDEO_ID}?background=493D27&foreground=E8D9AC&highlight=FFFFF0" width="640" height="390" allowfullscreen="true" allowscriptaccess="always"></embed>
    </object>
{/if}
*}
</div>

{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
{if $action eq 'build'}
   <div class="block">
   <!-- Action Links -->
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/conf_sitemap.php" title="{l}Restart sitemap building{/l}" class="button"><span class="new-sitemap">{l}Restart sitemap building{/l}</span></a></li>
   </ul>
   <!-- /Action Links -->
   </div>

   {if isset($error) and $error gt 0}
   <div class="block">
      <div class="error">
      <h2>{l}Error{/l}</h2>
      <p>{l}An error occured while creating sitemap{/l}</p>
      {if !empty($errorMsg)}
         <p>{$errorMsg|escape}</p>
      {/if}
      {if !empty($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p>{$sql_error|escape}</p>
      {/if}
      </div>
   </div>
   {/if}

   {if $googleSitemapCreated}
   <div class="block">
      <div class="success">
         <h2>{l}Google Sitemap created successfully{/l}</h2>
         <p><a href="{$googleWwwSitemapFile|escape}" title="{l}View created sitemap in a new window{/l}." target="_blank">{$googleWwwSitemapFile|escape}</a></p>
         {if isset($googleCompressedSitemapCreated) and $googleCompressedSitemapCreated}
            <p><a href="{$googleWwwCompressedSitemapFile|escape}" title="{l}Link to GZIP compressed sitemap{/l}." target="_blank">{$googleWwwCompressedSitemapFile|escape}</a></p>
         {/if}
      </div>

      {if isset($googleCompressedSitemapCreated) and !$googleCompressedSitemapCreated}
         <div class="error">
            <p>{l}An error occured while creating GZIP compressed Google Sitemap{/l}</p>
         </div>
      {/if}

      {if isset($pingResult) and !empty($pingMessage)}
         {if $pingResult == 1}
            <div class="success">{$pingMessage}</div>
         {else}
            <div class="error">{$pingMessage}</div>
         {/if}
      {/if}
   </div>
   {/if}

   {if isset($yahooSitemapCreated) and $yahooSitemapCreated eq 1}
   <div class="success block">
      <h2>{l}Yahoo! Sitemap created successfully{/l}</h2>
      <p><a href="{$yahooWwwSitemapFile|escape}" title="{l}View created sitemap in a new window{/l}." target="_blank">{$yahooWwwSitemapFile|escape}</a></p>

      {if isset($yahooCompressedSitemapCreated) and $yahooCompressedSitemapCreated}
         <p><a href="{$yahooWwwCompressedSitemapFile|escape}" title="{l}Link to GZIP compressed sitemap{/l}." target="_blank">{$yahooWwwCompressedSitemapFile|escape}</a></p>
      {/if}

      <p>{l}To submit your Yahoo! Sitemap, go to the {/l}<a href="https://siteexplorer.search.yahoo.com/submit" title="Yahoo! Submit" target="_blank">Yahoo! Submit</a>{l} page, create an account if you don't have one, and submit your text file URL location{/l}.
      </p>
   </div>
   {/if}

   {if isset($yahooCompressedSitemapCreated) and !$yahooCompressedSitemapCreated}
      <div class="error">
         <p>{l}An error occured while creating GZIP compressed Yahoo! Sitemap{/l}</p>
      </div>
   {/if}

{else}
   <div class="block">
      <div class="info-box">
         <h2>{l}Sitemaps{/l}</h2>
         <p><span class="important">{l}Google Sitemaps{/l}</span>{l} and {/l}<span class="important">{l}Yahoo Sitemaps{/l}</span>{l} allow webmasters to feed pages they want indexed directly to the search engines. Submitting your sitemaps is a great way to improve the chances that more pages from your site will be indexed by the search engines. Both sitemap programs offer better crawl coverage to help visitors and search engines find more of your pages. You get a smarter crawl because you can provide Google and Yahoo! with specific information about all your web pages, such as when a page was last modified or how frequently it changes. With both programs you no longer have to wait on the search engines to find your pages. Now you can tell them exactly what to visit{/l}.</p>

         <h2>{l}Start using Google sitemaps{/l}:</h2>
         <p>{l}Create a {/l}<a href="https://www.google.com/accounts/NewAccount" title="Google Account gives you access to many Google services." target="_blank">Google Account</a>{l}, generate here a sitemap file without the {/l}<span class="important">ping Google</span>{l} option, then go to the {/l}<a href="http://www.google.com/webmasters/sitemaps" title="Google Sitemap Submit Page" target="_blank">Google Sitemap Submit Page</a>.{l} Enter your sitemap URL and wait for the first download displayed on the stats page. If the status is not 'OK', correct the errors and resubmit your sitemap until it's approved. Bookmark the stats page and check back every once in a while (and after script changes!) to track Googlebot's usage of your sitemap{/l}.</p>

         <p class="notice">{l}Notice: After you register for a Google account, and submit your sitemap file, you can select the {/l}<span class="important">ping Google</span>{l} option and an automatic notification will be sent to Google's crawler, so you don't have to submit the page manually{/l}.</p>
         <p class="notice">{l}Google recommends that you resubmit a Sitemap no more than once per hour{/l}.</p>


         <h2>{l}More information about Google sitemaps{/l}:</h2>
         <ul>
            <li><a href="https://www.google.com/webmasters/sitemaps/docs/en/about.html" title="{l}About Google Sitemaps{/l}" target="_blank">{l}About Google Sitemaps{/l}</a></li>
            <li><a href="https://www.google.com/webmasters/sitemaps/docs/en/faq.html" title="{l}Google Sitemaps FAQ{/l}" target="_blank">{l}Google Sitemaps FAQ{/l}</a></li>
            <li><a href="https://www.google.com/webmasters/sitemaps/docs/en/protocol.html" title="{l}Sitemap Protocol Contents{/l}" target="_blank">{l}Sitemap Protocol Contents{/l}</a></li>
            <li><a href="http://www.google.com/webmasters/sitemaps/docs/en/submit.html" title="{l}Adding a Sitemap or Site to your Google Sitemaps Account{/l}" target="_blank">{l}Adding a Sitemap or Site to your Google Sitemaps Account{/l}</a></li>
         </ul>


         <h2>{l}Start using Yahoo! sitemaps:{/l}</h2>
         <p>{l}Unlike the {/l}<span class="important">Google Sitemaps</span>{l} protocol, {/l}<span class="important">Yahoo! Sitemaps</span>{l} are just a simple text file containing a list of URLs, each URL at the start of a new line. The filename of the URL list file must be {/l}<code class="important">urllist.txt</code></p>
         <p>{l}To submit your Yahoo! Sitemap, go to the {/l}<a href="https://siteexplorer.search.yahoo.com/submit" title="Yahoo! Submit" target="_blank">Yahoo! Submit</a>{l} page, create an account if you don't have one, and submit your text file URL location.{/l}</p>
      </div>
   </div>

<form method="post" action="{$smarty.const.DOC_ROOT}/conf_sitemap.php" name="fieldsForm">
<input type="hidden" id="timestamp" name="timestamp" value="{$timestamp}" class="hidden" />
<div class="block">
   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}Google Sitemap Settings{/l}</th>
      </tr>
   </thead>

   <tbody>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="googleSitemapFile">{l}Full path to sitemap file{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="googleSitemapFile" name="googleSitemapFile" value="{$googleSitemapFile}{if $googleCompressFile eq '1'}.gz{/if}" class="text" readonly="readonly" />
            <p class="notice">{l}No need to change{/l}</p>
            {if $googleFileValidation !== true and is_string($googleFileValidation)}
               <p class="error"><code>{$googleSitemapFileName}</code> - {$googleFileValidation|escape}</p>
            {/if}
            {if $googleCompressedFileValidation !== true and is_string($googleCompressedFileValidation)}
               <p class="error"><code>{$googleCompressedSitemapFileName}</code> - {$googleCompressedFileValidation|escape}</p>
            {/if}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="googleCompressFile">{l}Compress sitemap{/l}:</label></td>
         <td class="smallDesc">
            {if $gzipSupport eq 1}
               <input type="checkbox" id="googleCompressFile" name="googleCompressFile" value="1"{if $googleCompressFile eq '1'} checked="checked"{/if} />
               <p class="notice">{l}GZIP Compression: necessary if uncompressed sitemap is larger than 10 MB{/l}.</p>
            {else}
               <p class="notice">{l}No GZIP support available{/l}.</p>
            {/if}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="pingGoogle">{l}Ping Google{/l}:</label></td>
         <td class="smallDesc">
            <input type="checkbox" id="pingGoogle" name="pingGoogle" value="1"{if $pingGoogle eq '1'} checked="checked"{/if} />
            <p class="notice">{l}Skip manual submission and send Google a notification about the new created sitemap.{/l}.</p>
            <p class="notice">{l}The Googlebot will automatically crawl the new sitemap.{/l}</p>
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="lastmod">{l}Last modification date of URL{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$lastmodList selected=$lastmod name="lastmod" id="lastmod"}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="timeformat">{l}Timeformat for sitemap file{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$timeformatList selected=$timeformat name="timeformat" id="timeformat"}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="priority">{l}Priority{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$priorityList selected=$priority name="priority" id="priority"}
            <p class="notice">{l}Relative priority of an URL related to whole website{/l}.</p>
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="changefreq">{l}Change frequency{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$changefreqList selected=$changefreq name="changefreq" id="changefreq"}
            <p class="notice">{l}Specify the update (change) frequency of the URL's{/l}.</p>
         </td>
      </tr>
   </tbody>
   </table>
</div>

<div class="block">
   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}Yahoo! Sitemap Settings{/l}</th>
      </tr>
   </thead>

   <tbody>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="yahooSitemapFile">{l}Full path to sitemap file{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="yahooSitemapFile" name="yahooSitemapFile" value="{$yahooSitemapFile}{if $yahooCompressFile eq '1'}.gz{/if}" class="text" readonly="readonly" />
            <p class="notice">{l}No need to change{/l}</p>
            {if $yahooFileValidation !== true and is_string($yahooFileValidation)}
               <p class="error"><code>{$yahooSitemapFileName}</code> - {$yahooFileValidation|escape}</p>
            {/if}
            {if $yahooCompressedFileValidation !== true and is_string($yahooCompressedFileValidation)}
               <p class="error"><code>{$yahooCompressedSitemapFileName}</code> - {$yahooCompressedFileValidation|escape}</p>
            {/if}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="yahooCompressFile">{l}Compress sitemap{/l}:</label></td>
         <td class="smallDesc">
            {if $gzipSupport eq 1}
               <input type="checkbox" id="yahooCompressFile" name="yahooCompressFile" value="1"{if $yahooCompressFile eq '1'} checked="checked"{/if} />
               <p class="notice">{l}GZIP Compression: necessary if uncompressed sitemap is larger than 10 MB{/l}.</p>
            {else}
               <p class="notice">{l}No GZIP support available{/l}.</p>
            {/if}
         </td>
      </tr>
   </tbody>
</table>
</div>

<div class="block">
   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}General Sitemap Settings{/l}</th>
      </tr>
   </thead>

   <tbody>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="range">{l}Number of URL's to process per cycle{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="range" name="range" value="{$range}" maxlength="10" class="text range" />
         </td>
      </tr>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="paging">{l}Add paging URL's{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$yes_no selected=$paging name="paging" id="paging"}
            <p class="notice">{l}This is the only limitation that could timeout the script from executing if you have thousands of link pages in a category.{/l}</p>
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-sitemap-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td>{if $nosubmit eq '1'}
                <p class="notice">{l}Please grant writing permissions at least to one of the sitemap files{/l}.</p>
             {else}
                <input type="submit" id="send-sitemap-submit" name="generate" value="{l}Generate{/l}" alt="{l}Generate form{/l}" title="{l}Generate sitemap{/l}" class="button" />
             {/if}
             <input type="submit" id="test-sitemap-submit" name="test" value="{l}Check settings{/l}" alt="{l}Check settings{/l}" title="{l}Check settings{/l}" class="button" />
         </td>
      </tr>
   </tfoot>
</table>
</div>
<input type="hidden" name="formSubmitted" value="1" />
</form>
{/if}
{/strip}
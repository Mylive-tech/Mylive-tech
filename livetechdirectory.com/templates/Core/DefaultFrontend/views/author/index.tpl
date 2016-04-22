<h1>{$NAME|escape|trim}</h1>
<div class="authorPage">
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">{l}Name{/l}:</div>
            <div class="phpld-label float-left">
                {$NAME|escape|trim}
            </div>
   </div>
   {if $INFO}
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">{l}Info{/l}:</div>
            <div class="phpld-label float-left">
                {$INFO|escape|trim}
            </div>
   </div>
   {/if}
   
   {if $WEBSITE}
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">{l}Website{/l}:</div>
            <div class="phpld-label float-left">
                {$WEBSITE|escape|trim}
            </div>
   </div>
   {/if}
   
   {if $WEBSITE_NAME}
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">{l}Website Name{/l}:</div>
            <div class="phpld-label float-left">
                {$WEBSITE_NAME|escape|trim}
            </div>
   </div>
   {/if}
   {if $RELATED}
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">{l}Members Listings{/l}:</div>
            <div class="phpld-label float-left">
                {$RELATED}
            </div>
   </div>
   {/if}
   
</div>


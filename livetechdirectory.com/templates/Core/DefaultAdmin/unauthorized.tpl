{strip}
<div class="error">
   <h2>{l}Unauthorized.{/l}</h2>
   <p>{l}Sorry, you're not allowed to access this page.{/l}</p>

   {if isset($unauthorizedReason) and !empty($unauthorizedReason)}
      <h2>{l}Reason{/l}</h2>
      <p>{$unauthorizedReason|escape|trim}</p>
   {/if}
</div>
{/strip}

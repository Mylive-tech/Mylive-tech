{if !is_null($namespaces)}
    <div class="phpld-column phpld-messages">
    {foreach from=$namespaces item="messages" key="namespace"}
        {if $messages|@count > 0}
            {foreach from=$messages item="m"}
                <p class="box {$namespace}">{$m}</p>
            {/foreach}
        {/if}
    {/foreach}
    </div>
{/if}
<address>
    <div class="address">{$LINK.ADDRESS}</div>
    <div class="addressCityStateZip">{$LINK.CITY} {$LINK.STATE} {$LINK.ZIP} {$LINK.COUNTRY}</div>
    <div class="contacts">
    {if $LINK.PHONE_NUMBER}
        <div class="phone"><label>Phone: </label>{$LINK.PHONE_NUMBER}</div>
    {/if}
    {if $LINK.FAX_NUMBER}
        <div class="fax"><label>Fax: </label>{$LINK.FAX_NUMBER}</div>
    {/if}
    </div>
</address>
<div class="phpld-columnar">
   
    <div>
        {if $smarty.get.LINK_TYPE == 'undefined'}
            <div class="box error">
                {l}You must choose a link type to proceed{/l}
            </div>
        {/if}
        {foreach from=$link_types item=link_type name=link_types key=link_type_id}
            <div class="phpld-columnar phpld-equalize">
                <div class="phpld-fbox-check float-left">
                    <input type="radio" name="LINK_TYPE" value="{$link_type_id}" {if $link_type_id == $linktypeid}checked{/if}/>
                </div>
                <div class="float-left phpld-fbox-text">
                    {l}{$link_type.NAME}{/l}&nbsp;-&nbsp;
                    {if $link_type.PRICE > 0}
                        {$smarty.const.HTML_CURRENCY_CODE}{$link_type.PRICE} / {$payment_um[$link_type.PAY_UM]}
                    {else}
                        free
                    {/if}
                    <p class="small">{l}{$link_type.DESCRIPTION}{/l}</p>
                </div>
            </div>
        {/foreach}
        <div class="phpld-columnar">
            <center>
                <div class="phpld-fbox-button">
                    <input type="button" class='button' name="choicemade" onclick="closeCategSelectModal(self);" value="{l}Go To Step Three{/l}" />
                </div>
            </center>
        </div>
    </div>
</div>

{literal}
        <script type="text/javascript">
            function closeCategSelectModal(el) {
                    //el.parent.tb_remove();
                    //destroyCatTree();
                    //jQuery("#selectCategOk").hide();
                    //jQuery("#toggleCategTree").hide();
                    var cur_categ ={/literal}{$smarty.request.c}{literal};
                    var cur_linktype = jQuery('input[name=LINK_TYPE]:checked').val();
                    document.location.href = "submit?c=" + cur_categ+ "&LINK_TYPE="+cur_linktype;
            }
        </script>
    {/literal}
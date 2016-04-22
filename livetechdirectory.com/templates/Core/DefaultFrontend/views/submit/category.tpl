<div class="phpld-columnar">
    <h3 style="width:100%;">{l}Step One Choose a Category{/l}:</h3>
    <div id="hiddenModalContent">
        {* Load category selection *}
        {include file="views/_shared/category_select.tpl" selected=$categoryID selected_parent=$parentID}
    </div>
    <br />
    <center>
        <div class="phpld-fbox-button">
            <input type="button" class="button" id="ok" value="{l}Go To Step Two{/l}" onclick="closeCategSelectModal(self);" style="padding:1px 10px;" />
        </div>
    </center>

    {literal}
        <script type="text/javascript">
            function closeCategSelectModal(el) {
                    //el.parent.tb_remove();
                    //destroyCatTree();
                    //jQuery("#selectCategOk").hide();
                    //jQuery("#toggleCategTree").hide();
                    var cur_categ = jQuery("#CATEGORY_ID").val();
                    document.location.href = "submit?c=" + cur_categ;
            }
        </script>
    {/literal}
</div>
{* Error and confirmation messages *}
{include file="messages.tpl"}
<style type="text/css" media="screen">
    @import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/datatable_jui.css";
    @import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/jquery-ui-1.7.2.custom.css";
</style>
{literal}
<script type="text/javascript">

var oTable;

function multiple_action(action) {
    hash = dt_get_selected();
        jQuery.ajax({
url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php",
    type: "post",
    data: ({
        submitAction: action,
        category_id: function() { return jQuery("#CATEGORY_ID").val(); },
        multiselect_links: hash
    }),
    cache: false,
success: function(response){
        if (response == '1')
    oTable.fnStandingRedraw();
else
        alert('{/literal}{escapejs}{l}An errror occured while saving.{/l}{/escapejs}{literal}');
}
});
}


function dt_get_selected() {
    var hash = '';
    var trs = oTable.fnGetNodes();
    for (var i=0 ; i<trs.length ; i++) {
        if ( jQuery(trs[i]).hasClass('row_selected'))
            hash += trs[i].getAttribute("id") + ',';
    }
    hash = hash.slice(0, - 1);
    return hash;
}


        jQuery(document).ready(function() {

    jQuery("#multiple_controls input").click(function() {
        ctrl_name = jQuery(this).attr('name');
        isAutosubmitDisabled = jQuery(this).attr('autosubmit-disabled');
        if (ctrl_name != 'check_all' && ctrl_name != 'uncheck_all' && !isAutosubmitDisabled)
            multiple_action(ctrl_name);
    });

    jQuery("#check_all").click(function() {
        jQuery("#example tbody tr").each(function(n,element){
            var chbox = '#categ_' + jQuery(element).attr('id');
            jQuery(element).addClass('row_selected');
            jQuery(chbox).attr('checked', true);
        });
    });

    jQuery("#uncheck_all").click(function() {
        jQuery("#example tbody tr").each(function(n,element){
            var chbox = '#categ_' + jQuery(element).attr('id');
            jQuery(element).removeClass('row_selected');
            jQuery(chbox).attr('checked', false);
        });
    });

        jQuery("#action_del").live("click", function() {
        return link_rm_confirm('{/literal}{escapejs}{l}Are you sure you want to remove this tag?{/l}\n{l}Note: tags can not be restored after removal!{/l}{/escapejs}{literal}');
});


    jQuery("#status_active").live("click", function () {
        var id = jQuery(this).parent().parent().parent().attr('id');
        single_action('active', id);
    });

    jQuery("#status_pending").live("click", function () {
        var id = jQuery(this).parent().parent().parent().attr('id');
        single_action('pending', id);
    });

    jQuery("#status_inactive").live("click", function () {
        var id = jQuery(this).parent().parent().parent().attr('id');
        single_action('inactive', id);
    });


function single_action(action, id) {
        jQuery.ajax({
url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php",
    type: "post",
    data: ({
        submitAction: action,
        category_id: function() { return jQuery("#CATEGORY_ID").val(); },
        multiselect_links: id
    }),
    cache: false,
success: function(response){
        if (response == '1')
    oTable.fnStandingRedraw();
else
        alert('{/literal}{escapejs}{l}An errror occured while saving.{/l}{/escapejs}{literal}');
}
});
}

    jQuery('#example tbody tr').live("click", function () {
        var chbox = '#categ_' + jQuery(this).attr('id');
        if (jQuery(this).hasClass('row_selected')) {
            jQuery(this).removeClass('row_selected');
            jQuery(chbox).attr('checked', false);
        } else {
            jQuery(this).addClass('row_selected');
            jQuery(chbox).attr('checked', true);
        }
    });

        oTable = jQuery('#example').dataTable( {
    "bProcessing": true,
    "bServerSide": true,
    "bJQueryUI": true,
    "bSort": true,
    "iDisplayLength": 20,
    "sPaginationType": "full_numbers",
"oSearch": { "sSearch": "{/literal}{$search}{literal}" },
    "aaSorting": [[ 5, "desc" ]],
"sAjaxSource": "{/literal}dir_tags_ajax.php?status={$status}{literal}",

"fnDrawCallback": function() {

        jQuery('#example tbody tr td:nth-child(2) span').editable('{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php', {
    "height": "14px",
    submitdata : {
        submitAction: "title"
    },
    callback: function(value, settings) {
        oTable.fnStandingRedraw();
    }
});

        jQuery('#example tbody tr td:nth-child(6)').editable('{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php', {
    "height": "14px",
    submitdata : {
        submitAction: "pr"
    },
    callback: function(value, settings) {
        oTable.fnStandingRedraw();
    }
});

        jQuery('#example tbody tr td:nth-child(7)').editable('{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php', {
    "height": "14px",
    submitdata : {
        submitAction: "hits"
    },
    callback: function(value, settings) {
        oTable.fnStandingRedraw();
    }
});

},

"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
    var id = aData[0];
    jQuery(nRow).attr("id",id);
    jQuery(nRow).find("td").attr("id",id);

    jQuery('td:eq(0)', nRow).html('<input type="checkbox" name="categ_' + aData[0] + '" id="categ_' + aData[0] + '"> ' + aData[0]);

        jQuery('td:eq(1)', nRow).html('<span id="' + aData[0] + '">' + aData[1] + '</span>{/literal}'{literal});

    jQuery('td:eq(1) #more-info-' + aData[0], nRow).click(function() {
        ajaxLinkDetailsRequest(aData[0]);
    });


    var stats = '';
{/literal}

    jQuery('td:eq(3)', nRow).html('<div style="width: 200px;"><span style="float: left;">'+aData[3] + '</span></div>');
{literal}

    var action_edit = '';
    var action_del = '';
{/literal}
    action_edit = '<a href="{$smarty.const.DOC_ROOT}/dir_tags_edit.php?action=E:' + aData[0] + '" title="{escapejs}{l}Edit Link{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" border="0"/></a>';
    action_del = '<a id="action_del" href="{$smarty.const.DOC_ROOT}/dir_tags_edit.php?action=D:' + aData[0] + '"  title="{escapejs}{l}Remove Link{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" border="0"/></a>';
{literal}

    jQuery('td:eq(4)', nRow).html(action_edit + '&nbsp;' + action_del);


    return nRow;
},

    "aoColumns": [
        null,
        null,
        null,
        null,
        { "bSortable": false }
    ]


});

    oTable.fnSetColumnVis(10, false);

});





</script>
{/literal}

<div class="block">
    <!-- Action Links -->
    <ul class="page-action-list">
        <li><a href="{$smarty.const.DOC_ROOT}/dir_tags_edit.php?action=N" title="{l}Create new tag{/l}" class="button"><span class="new-link">{l}New Tag{/l}</span></a></li>
    </ul>
    <!-- /Action Links -->
</div>
<div class="block">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%; " id="example" name="example" class="display">
        <thead>
        <tr>
        {foreach from=$columns key=col item=name}
            <th>{$name|escape|trim}</th>
        {/foreach}
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="9" class="dataTables_empty">Loading data from server</td>
        </tr>
        </tbody>
    </table>
</div>
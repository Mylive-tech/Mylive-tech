{* Error and confirmation messages *}
{include file="messages.tpl"}

{literal}
    <script type="text/javascript">
    function status_hide(id) {
            var display = document.getElementById('list-status-'+id).style.display;
            if (display == 'none') {
                    document.getElementById('list-status-'+id).style.display = 'block';
            } else {
                    document.getElementById('list-status-'+id).style.display = 'none';
            }
            return false;
    }
    function getPosition(e, id) {
        e = e || window.event;
        var cursor = {x:0, y:0};
        if (e.pageX || e.pageY) {
            cursor.x = e.pageX;
            cursor.y = e.pageY;
        } 
        else {
            var de = document.documentElement; 
            var b = document.body;
            cursor.x = e.clientX + 
                (de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
            cursor.y = e.clientY + 
                (de.scrollTop || b.scrollTop) - (de.clientTop || 0);
        }

        document.getElementById(id).style.top = eval(cursor.y+1)+'px';
            document.getElementById(id).style.left = eval(cursor.x)+'px';
    }

    function select_all(key,all) {

            if (document.getElementsByClassName == undefined) {
            document.getElementsByClassName = function(className)
            {
                    var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
                    var allElements = document.getElementsByTagName("*");
                    var results = [];

                    var element;
                    for (var i = 0; (element = allElements[i]) != null; i++) {
                            var elementClass = element.className;
                            if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
                                    results.push(element);
                    }

                    return results;
            }
            }	

            var elem = document.getElementsByClassName(key);
            if (all == 1) {
                    for (var i in elem) {
                            elem[i].checked=true;
                    }
            } else {
                    for (var i in elem) {
                            elem[i].checked=false;
                    }
            }
    }
    jQuery(document).ready(function(){
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                jQuery(this).width(jQuery(this).width());
            });
            return ui;
        };
        jQuery('table.list tbody').sortable({
            cursor: 'move',
            axis:   'y',
            opacity: 0.6,
            stop: function(e, ui) {
                helper: fixHelper,
                jQuery(this).sortable("refresh");
                sorted = jQuery(this).sortable('toArray');
                var order = new Array;
                for(var i = 0; i < sorted.length; i++ ){
                    var ids = sorted[i].split('_');
                    if (typeof ids[1] != undefined) {
                        order.push(ids[1]);
                    }
                    
                }    
                /*jQuery().each(sorted, function(k,v){
                    var ids = v.attr('id').split('_');
                    if (typeof ids[1] != undefined) {
                        order.push(ids[1]);
                    }
                });*/

                jQuery.get({/literal}'{$smarty.const.DOC_ROOT}/dir_widgets_per_zone_edit.php?action=O'{literal}, {'ids' : order});
            }
            
            
            
            /*helper: fixHelper,
            axis: "y",
            opacity: 0.6,
            stop: function(event, ui) {
                order = new Array();
                jQuery(event.target).each(function(){
                    var ids = jQuery(this).attr('id').split('_');
                    if (typeof ids[1] != undefined) {
                        order.push(ids[1]);
                    }
                });
                jQuery.get({/literal}'{$smarty.const.DOC_ROOT}/dir_widgets_per_zone_edit.php?action=O'{literal}, {'ids' : order});
            }*/
        });
    });

    </script>
{/literal}

{strip}

    <div class="block">
        <h2 style="font-size: 12px;">Now viewing: {$zone}. View all zones <a href="{$smarty.const.DOC_ROOT}/dir_widget_zones.php">here</a>.</h2>
    </div>

    {if $wid_message neq ''}
        <div class="success block">
            {$wid_message}
        </div>
    {/if}
    {if $wid_error neq ''}
        <div class="block">
            <div class="error">
                {$wid_error}
            </div>
        </div>
    {/if}

    {if $op_status eq 1}
        <div class="success block">
            {l}Operation successful.{/l}
        </div>
    {elseif $op_status eq -1}
        <div class="block">
            <div class="error">
                <h2>{l}Error{/l}</h2>
                <p>{l}Some errors occured during the operation.{/l}</p>
            </div>
        </div>
    {/if}

    <div class="block">
        <form action="{$smarty.const.DOC_ROOT}/dir_widgets_edit.php" method="post">
            <input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" />
            <table class="list">
                <thead>
                    <tr>
                        <th class="listHeader">&nbsp;</th>
                        {foreach from=$columns key=col item=name}
                            {if $col ne 'TITLE_URL'}
                                <th class="listHeader">{$name|escape|trim}</th>
                            {/if}
                        {/foreach}
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$list item=row key=NAME name=widgets}
                        <tr class="{cycle values="odd,even"}" id="row_{$row.ID}">
                            <td class="first-child" style="width: 25px;">
                                <label>
                                    {if $row.NAME neq 'MainContent'}
                                        <input type="checkbox" name="multi-{$row.NAME}" class="{$zone}" id="multi-{$row.NAME}" title="{l}Check box to select this item.{/l}" />
                                    {/if}
                                </label>
                            </td> 
                            {foreach from=$columns key=col item=name}
                                {assign var="val" value=$row.$col}
                                {if $col eq 'ACTIVE'}
                                    <td style="width: 140px;" {if $row.ZONE neq '' && $row.ZONE eq $zone} class="status-2"{else}class="status-0"{/if}>
                                        {if $row.ACTIVE == 1}
                                            <span class="link-status pop">{l}On{/l}</span>

                                            <h3 id="chgStat-{$row.NAME}" class="chgStatTitle" onclick="{literal}return status_hide('{/literal}{$row.ID}{literal}');{/literal}">{l}Change status{/l}</h3>
                                            <ul id="list-status-{$row.ID}" style="display: none;">
                                                <li><a class="new-status-0" href="{$smarty.const.DOC_ROOT}/dir_widgets_per_zone_edit.php?action=D:{$row.ID}" title="{l}Turn Off Widget{/l}: {$row.NAME|escape|trim}">{l}Off{/l}</a></li>
                                            </ul>

                                        {else}
                                            <span class="link-status pop">{l}Off{/l}</span>

                                            <h3 id="chgStat-{$row.NAME}" class="chgStatTitle" onclick="{literal}return status_hide('{/literal}{$row.NAME}{literal}');{/literal}">{l}Change status{/l}</h3>
                                            <ul id="list-status-{$row.NAME}" style="display: none;">
                                                <li><a class="new-status-2" href="{$smarty.const.DOC_ROOT}/dir_widgets_per_zone_edit.php?action=A:{$row.ID}" title="{l}Turn On Widget{/l}: {$row.NAME|escape|trim}">{l}On{/l}</a></li>
                                            </ul>

                                        {/if}
                                    </td>
                                {elseif $col eq 'ACTION'}
                                    {if $row.ZONE neq '' && $row.ZONE eq $zone}
                                        {if $row.NAME neq 'MainContent'}
                                            <td class="noborder" style="width: 50px; white-space: nowrap">
                                                <a style="float:left; margin-right:8px; margin-left:8px" href="{$smarty.const.DOC_ROOT}/dir_widgets_edit.php?action=E:{$row.ID}" title="{l}Edit settings for{/l}: {$row.NAME|escape|trim}" class="action edit"><span>{l}EDIT SETTINGS{/l}</span></a>
                                                <a style="float:left; margin-right:8px; margin-left:8px" href="{$smarty.const.DOC_ROOT}/dir_widgets_per_zone_edit.php?action=R:{$row.ID}" title="{l}Remore widget from current zone{/l}: {$row.NAME|escape|trim}" class="action delete" onclick="return confirm('Are you sure?');"><span>{l}REMOVE WIDGET{/l}</span></a>
                                            </td>
                                        {else}
                                            <td class="noborder"></td>
                                        {/if}

                                    {else}
                                        <td class="noborder" colspan="3" style="width: 60px;"></td>
                                    {/if}

                                {elseif $col eq 'ORDER_ID' && $row.ZONE eq '' && $row.$col == 1000}
                                    <td>
                                        {l}None yet{/l}
                                    </td>
                                {elseif $col eq 'NAME'}
                                    <td>
                                        <a href="" style="border: none; text-decoration: underline;" onmouseover="{literal}document.getElementById('wid_details_{/literal}{$row.$col}{literal}').style.display='block'; getPosition(event, 'wid_details_{/literal}{$row.$col}{literal}');{/literal}" onmouseout="{literal}document.getElementById('wid_details_{/literal}{$row.$col}{literal}').style.display='none';{/literal}" onclick="return false;">{$row.$col|escape|trim}</a>
                                        <span id="wid_details_{$row.$col}" class="wid_details" style="display: none;">
                                            {if $row.DESCRIPTION|escape|trim neq ''}
                                                {$row.DESCRIPTION|escape|trim}
                                            {else}
                                                No description.
                                            {/if}
                                        </span>
                                    </td>
                                {elseif $col eq 'TITLE'}
                                    <td>
                                        <a href="" style="border: none; text-decoration: underline;" onclick="return false;">{$row.$col|escape|trim}</a>
                                    </td>
                                {else}
                                    <td>
                                        {$row.$col|escape|trim}
                                    </td>
                                {/if}

                            {/foreach}
                        </tr>
                    {foreachelse}
                        <tr>
                            <td colspan="{$col_count}" class="norec">{l}No records found.{/l}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            <table class="list">
                <tr>
                    <td colspan="2" style="font-weight: bold; text-align: center;">{l}Manage multiple selections{/l}</td>
                </tr>
                <tr>
                    <td>
                        <fieldset class="link_action">
                            <legend>{l}Select{/l}</legend>
                            <input type="button" name="all" id="allButton" value="{l}All{/l}" title="{l}Select All{/l}" class="button" onclick="{literal}select_all('{/literal}{$zone}{literal}', 1);{/literal}" />
                            <input type="button" name="none" id="noneButton" value="{l}None{/l}" title="{l}Select None{/l}" class="button" onclick="{literal}select_all('{/literal}{$zone}{literal}', 0);{/literal}" />	
                        </fieldset>
                        <fieldset class="link_action">
                            <legend>{l}Change Status{/l}</legend>
                            <input type="submit" name="hide" id="hideButton" value="{l}Hide{/l}" title="{l}Hide selected widgets{/l}" class="button"/>
                            <input type="submit" name="show" id="hideButton" value="{l}Show{/l}" title="{l}Show selected widgets{/l}" class="button"/>
                            <input type="hidden" name="action" value="multi"/>
                            <input type="hidden" name="type" value="{$zone}"/>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </form>
    </div>
{/strip}

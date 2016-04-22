<script type="text/javascript" src="{$smarty.const.FRONT_DOC_ROOT}javascripts/nav-editor.js"></script>
<div class="pages availPagesCol">
    <h3>Available pages</h3>
    <ul class="menu menu-to-edit" id="avaiable-pages">
        <li class="menu-item menu-item-page">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                <span class="item-title">Custom URL </span>
                <span class="item-controls">
                    <button class="btn" onclick="jQuery('#addCustomUrlForm').toggle()">+</button>
                </span>
                <form method="post" id="addCustomUrlForm" action="" class="form-stacked" style="display: none;">
                    <input type="hidden" name="action" value="addPage">
                    <fieldset>
                        <label>Label: </label>
                        <input type="text" name="LABEL" value=""  class="text"/>
                        <label>URL: </label>
                        <input type="text" name="URL" value=""  class="text"/>
                        <div class="actions" style="margin-right:10px;">
                            <input type="submit" name="submit" id="submit" value="Add" class="btn">
                        </div>
                    </fieldset>
                </form>
                </dt>
            </dl>
        </li>

        {foreach from=$pages item=page}
        <li class="menu-item menu-item-page">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                    <span class="item-title">{$page.LABEL}</span>
                        <span class="item-controls">
                            <form method="post" method="post">
                                <input type="hidden" name="action" value="addPage">
                                <input type="hidden" name="LABEL" value="{$page.LABEL}" />
                                <input type="hidden" name="URL" value="{$page.URL}" />
                                <button type="submit" value="submit" class="btn">+</button>
                            </form>
                        </span>
                </dt>
            </dl>
        </li>
        {/foreach}
    </ul>
</div>
<div class="menu menuPagesCol">
    <h3>Menu</h3>
    <ul class="menu menu-to-edit" id="menu-to-edit">
        {$menuPages}
    </ul>
    <div class="actions">
        <button onclick="mkMenuEditor.saveMenu('{$idMenu}', event)" class="btn">Save</button>
    </div>
</div>
<li id="menu-item-{$page.ID}" class="sortable menu-item menu-item-depth-{$page.LEVEL} menu-item-page menu-item-edit-inactive">
    <dl class="menu-item-bar">
        <dt class="menu-item-handle">
            <span class="item-title">{$page.LABEL}</span>
            <span class="item-controls">
                <!--<span class="item-type">Страница</span>-->
                <a class="item-edit" id="edit-{$page.ID}" title="Edit menu item" href="javascript:void(null)" onclick="jQuery('#settings-{$page.ID}').toggle('fast')">Edit</a>
                <a class="item-remove" id="menu-item-{$page.ID}" title="Remove menu item" href="javascript:void(null)" onclick="if (confirm('Are you sure?'))mkMenuEditor.removeElement('{$page.ID}', event)">Remove</a>
            </span>
        </dt>
    </dl>
    <ul class="menu-item-transport"></ul>
    <div class="settings" id="settings-{$page.ID}">
        <form method="post">
            <input type="hidden" name="ID" value="{$page.ID}">
            <input type="hidden" name="action" value="saveMenuItem">
            <label>Label: </label>
            <input type="text" name="LABEL" value="{$page.LABEL}" class="text"/>
            <label>URL: </label>
            <input type="text" name="URL" value="{$page.URL}"  class="text"/>
            <div class="actions" style="margin-right:10px;">
                <input type="submit" name="submit" id="submit" value="Save" class="btn">
            </div>

            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[{$page.ID}]" value="{$page.ID}">
            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[{$page.ID}]" value="{$page.PARENT}">
        </form>
    </div>
</li>
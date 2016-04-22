{* Error and confirmation messages *}
{include file="messages.tpl"}
{strip}

<div class="howto">

<hr></hr>
<h2>PhpLD - HOW TO USE WIDGETS</h2>
<hr></hr>


<h3>INSTALLATION</h3>

<p>PhpLD Widgets functionalities are set up at the time of PhpLD installation/update time.</p>

<h3>ADDING A NEW WIDGET</h3>

<p>Although PhpLD comes with a bunch of widgets already available you might want to add new widgets to your install. Here's how to do that:</p>

<p>1. Upload the zip of the wanted widget to the <b>widgets</b> folder.</p>

<p>2. Unzip it and a folder with the widget's name should be created, having more or less the following structure:</p>
<ul>
	<li>/templates/</li>
	<li> /WidgetName.php</li>
	<li>/config.xml</li>
</ul>

<p>3. Go to the site's admin area and click on <b>Widgets >> Avaliable Widgets</b> in the left menu.</p>

<p>4. Look for the widget you've just uploaded and unzipped, it should have its <b>Installed</b> status set to <b>NO</b>.</p>

<p>5. Click the widget's <b>Install</b> link.<br/>
The widget is now properly installed, you just need to assign it to a front end Zone where it will later be visible. A <b>Zone Panel</b> will show up with available zones to pick from.
<br/>
Some extra info: At this time, widgets are available by type (Vertical, Central). A widget of a certain type can be displayed only in Zones of that type. That is, Vertical widgets can only be displayed in Vertical Zones, such as: LEFT_COLUMN, RIGHT_COLUMN.
</p>

<p>6. After installation click on <b>Zones</b> (under Widgets, in the left menu).<br/>
The PhpLD front end area is divided in the so called Zones, each with its Zone Type. Pick the zone, or one of the zones you've installed the widget on, then click <b>Wiew Widgets</b>.
</p>

<p>
7. This list shows all the widgets available for activation in the selected zone. At this time, your widget should have it's status set to <b>Visible</b>. <br/>This means that at this point, if the widget doesn't need extra configuring (most of them probably don't), it should already be available on the site's front end, in the selected zone(s). If the Widget needs some extra settings to be selected, a message will show up on the front end to let you know.
</p>

<h3>EDITING A WIDGET'S SETTINGS</h3>
<p>Some widgets do not work unless some basic settings are picked up after installation and activation. In other cases, although a widget is perfectly functional as it is, you might want to alter some of its particularities if allowed to do so. This is where widget settings come in.In the admin area, click on Widgets >> Avaliable Widgets in the left menu. Pick the widget you want to change the settings to and click <b>Edit Settings</b> (This is available only for installed widgets).</p>

<h3>UNINSTALLING A WIDGET</h3>
<p>In the admin area, click on <b>Widgets >> Avaliable Widgets</b> in the left menu. Pick the wanted widget and click <b>Uninstall</b>. This will completely remove the widget from all the zones it was previously set up in. The widget's custom settings, if any, will also be lost.
</p>
</div>
{/strip}

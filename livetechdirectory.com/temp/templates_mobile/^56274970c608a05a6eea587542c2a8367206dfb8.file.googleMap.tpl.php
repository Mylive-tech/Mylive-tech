<?php /* Smarty version Smarty-3.1.12, created on 2015-11-26 17:56:31
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/googleMap.tpl" */ ?>
<?php /*%%SmartyHeaderCode:43485909565747cfced365-27253759%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '56274970c608a05a6eea587542c2a8367206dfb8' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/googleMap.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '43485909565747cfced365-27253759',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_565747cfd23b85_73073923',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_565747cfd23b85_73073923')) {function content_565747cfd23b85_73073923($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.replace.php';
?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['ADDRESS']){?><div id="map" class="jmap"></div>
<script type="text/javascript">
	
	if (typeof isGmapsLoadStarted == 'undefined') {
		var isGmapsLoadStarted=true;
		var addressElements = [];

		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = "http://www.google.com/jsapi?sensor=false&callback=loadMap";
		document.body.appendChild(script);

		if (typeof loadMap == 'undefined') {
			var loadMap = function() {
				google.load(
					'maps',
					'3.7',
					{
                        'other_params' : 'sensor=false',
						'callback' : mapsLoaded
					}
				);
			};
		}

		var mapsLoaded = function() {
			for(var i=0; i<addressElements.length; i++) {
				var mapInfo = addressElements[i];
				mapInfo.geocoder = new google.maps.Geocoder();
				mapInfo.infowindow = new google.maps.InfoWindow();
				var coords = new google.maps.LatLng(41, -98); //North America, centered somewhere in South Dakota
				mapInfo.map = new google.maps.Map(document.getElementById(mapInfo.id), {
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					center: coords,
					zoom: 4
				});

				var inputValue=mapInfo.value;
				if(inputValue!="")
					submitAddress(mapInfo,inputValue);
			}
		};

		var submitAddress = function(mInfo, address) {
			mInfo.geocoder.geocode({address: address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						var latlng=new google.maps.LatLng(
							results[0].geometry.location.lat(),
							results[0].geometry.location.lng()
						);
						if (mInfo.gmapsMarker!==undefined) {
							mInfo.gmapsMarker.setPosition(latlng);
						} else {
							mInfo.gmapsMarker = new google.maps.Marker({
								position: latlng,
								map: mInfo.map});
						}
						mInfo.map.panTo(latlng);
						if(mInfo.content!="") {
							mInfo.infowindow.setContent(mInfo.content);
							mInfo.infowindow.open(mInfo.map, mInfo.gmapsMarker);
							mInfo.map.setZoom(16);
							mInfo.map.panBy(10,-80);
						}
					}
				}
			});
		};

		jQuery(document).ready(function($) {
			for(var i=0; i<addressElements.length;i++) {
				$("#"+addressElements[i].id)
						.data("mapInfo", addressElements[i]);
			}
		});
	}
	
</script>


<script type="text/javascript">
	addressElements.push({
		id: "map",
		value: '<?php echo smarty_modifier_replace(trim($_smarty_tpl->tpl_vars['LINK']->value['ADDRESS']),"'","\\'");?>
,<?php echo $_smarty_tpl->tpl_vars['LINK']->value['CITY'];?>
,<?php echo $_smarty_tpl->tpl_vars['LINK']->value['STATE'];?>
',
		content: '<?php if ($_smarty_tpl->tpl_vars['LINK']->value['IMAGE']){?><img display="block" src="<?php echo @DOC_ROOT;?>
/uploads/thumb/<?php echo $_smarty_tpl->tpl_vars['LINK']->value['IMAGE'];?>
"/><br /><?php }?><b><?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['LINK']->value['TITLE'],"'","\\'");?>
 <br><?php echo smarty_modifier_replace(trim($_smarty_tpl->tpl_vars['LINK']->value['ADDRESS']),"'","\\'");?>
<?php if ($_smarty_tpl->tpl_vars['LINK']->value['CITY']!=''){?><br> <?php echo $_smarty_tpl->tpl_vars['LINK']->value['CITY'];?>
<?php }?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['STATE']!=''){?><br> <?php echo $_smarty_tpl->tpl_vars['LINK']->value['STATE'];?>
<?php }?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['ZIP']!=''){?><br> <?php echo $_smarty_tpl->tpl_vars['LINK']->value['ZIP'];?>
<?php }?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['PHONE_NUMBER']!=''){?><br> <?php echo $_smarty_tpl->tpl_vars['LINK']->value['PHONE_NUMBER'];?>
<?php }?></b>'
	});
</script>
<?php }?><?php }} ?>
{if $LINK.ADDRESS}<div id="map" class="jmap"></div>
<script type="text/javascript">
	{literal}
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
	{/literal}
</script>


<script type="text/javascript">
	addressElements.push({ldelim}
		id: "map",
		value: '{$LINK.ADDRESS|trim|replace:"'":"\\'"},{$LINK.CITY},{$LINK.STATE}',
		content: '{if $LINK.IMAGE}<img display="block" src="{$smarty.const.DOC_ROOT}/uploads/thumb/{$LINK.IMAGE}"/><br />{/if}<b>{$LINK.TITLE|replace:"'":"\\'"} <br>{$LINK.ADDRESS|trim|replace:"'":"\\'"}{if $LINK.CITY != ""}<br> {$LINK.CITY}{/if}{if $LINK.STATE != ""}<br> {$LINK.STATE}{/if}{if $LINK.ZIP != ""}<br> {$LINK.ZIP}{/if}{if $LINK.PHONE_NUMBER != ""}<br> {$LINK.PHONE_NUMBER}{/if}</b>'
	{rdelim});
</script>
{/if}
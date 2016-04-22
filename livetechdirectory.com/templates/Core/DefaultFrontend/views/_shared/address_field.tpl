{*Template currently not in use*}

<input type="text" name="{$NAME|escape|trim}" id="{$NAME|escape|trim}" value="{$VALUE|escape|trim}" size="40"/>

{if $smarty.const.GMAP_ENABLE}
<div id="{$NAME|escape|trim}_MAP" style="height: 200px; display: block; clear: both;"></div>
<script type="text/javascript">
{literal}
	if (typeof isGmapsLoadStarted == 'undefined') {
		var isGmapsLoadStarted=true;
		var addressElements = [];

		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = "http://www.google.com/jsapi?sensor=false&callback=loadMap";
		console.log("adding script");
		document.body.appendChild(script);

		if (typeof loadMap == 'undefined') {
			console.log("defining loadMap");
			var loadMap = function() {
				console.log("calling loadMap");
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
				mapInfo.map = new google.maps.Map(document.getElementById(mapInfo.id+"_MAP"), {
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					center: coords,
					zoom: 4
				});

				//click on map
				google.maps.event.addListener(mapInfo.map, 'click', function(e) {
					var mInfo = jQuery(this.b).data("mapInfo");
					mInfo.geocoder.geocode(
					{'latLng': e.latLng},
							function(results, status) {
								if (status == google.maps.GeocoderStatus.OK) {
									if (results[0]) {
										if (mInfo.gmapsMarker!==undefined) {
											mInfo.gmapsMarker.setPosition(e.latLng);
										} else {
											mInfo.gmapsMarker = new google.maps.Marker({
												position: e.latLng,
												map: mInfo.map});
										}
										if(confirm("Do you want to replace the existing address with \""+results[0].formatted_address+"\"?"))
										{
											var $address=jQuery("#"+mInfo.id);
											//first disable the onchange event
											$address.data("callbackDeactivator")(mInfo.id);
											//set the value
											$address.val(results[0].formatted_address);
											//activate the onchange event
											$address.data("callbackActivator")(mInfo.id);
										}
										mInfo.infowindow.setContent(results[0].formatted_address);
										mInfo.infowindow.open(mInfo.map, mInfo.gmapsMarker);
									}
								}
							});
				});
				//END click on map

				var inputValue=$("#"+mapInfo.id).val();
				if(inputValue!="")
					submitAddress(mapInfo,inputValue);
			}
		};

		var changeCallback = function() {
			var mInfo = $(this).data("mapInfo");
			submitAddress(mInfo, $(this).val());
		};

		var submitAddress = function(mInfo, address) {
			mInfo.geocoder.geocode({address: address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						var latlng=new google.maps.LatLng(
								results[0].geometry.location['$a'],
								results[0].geometry.location['ab']
						);
						if (mInfo.gmapsMarker!==undefined) {
							mInfo.gmapsMarker.setPosition(latlng);
						} else {
							mInfo.gmapsMarker = new google.maps.Marker({
								position: latlng,
								map: mInfo.map});
						}
						mInfo.map.panTo(latlng);
						mInfo.infowindow.setContent(results[0].formatted_address);
						mInfo.infowindow.open(mInfo.map, mInfo.gmapsMarker);
					}
				}
			});
		};

		var activateCallback = function(id) {
			jQuery("#"+id).bind("change", changeCallback);
		};

		var deactivateCallback = function(id) {
			jQuery("#"+id).unbind("change", changeCallback);
		};

		jQuery(document).ready(function($) {
			for(var i=0; i<addressElements.length;i++) {
				$("#"+addressElements[i].id)
						.data("callbackActivator", activateCallback)
						.data("callbackDeactivator", deactivateCallback)
						.data("mapInfo", addressElements[i]);
				$("#"+addressElements[i].id+"_MAP")
						.data("mapInfo", addressElements[i]);
				activateCallback(addressElements[i].id);
			}
		});
	}
{/literal}
</script>


<script type="text/javascript">
	addressElements.push({ldelim}
		id: "{$NAME|escape|trim}",
		value: "{$VALUE|escape|trim}"
	{rdelim});
</script>

{/if}
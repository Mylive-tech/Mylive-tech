var browserSupportFlag =  new Boolean();

var map;

var infowindow = new google.maps.InfoWindow();

var geocoder = new google.maps.Geocoder();

var bounds = new google.maps.LatLngBounds();

var descriptions = new Array;

var adUnit;

var map_markers = new Array;

function initListingsMap(options) {

    var mapOptions = {
        'zoom': default_zoom,
        'map': 'map_canvas',
        'center': new google.maps.LatLng(default_lat, default_lon),
        'mapTypeId': google.maps.MapTypeId.ROADMAP,
        'scaleControl': true,
        'markers': new Array()
    }


    mapOptions = jQuery.extend(mapOptions, options);


    map = new google.maps.Map(document.getElementById(mapOptions.map), mapOptions);

    var adUnitDiv = document.createElement('div');
    var adUnitOptions = {
        publisherId: 'ca-google-maps_apidocs',
        map: map,
        visible: true
    };
    var adUnit = new google.maps.adsense.AdUnit(adUnitDiv, adUnitOptions);
    markers = mapOptions.markers;


    markers_num = markers.length;

    for (var i in markers) {
        if (markers[i]['location'] != '') {
            addMarker(markers[i], markers[i]['location'], false);
        }
    }
   
    
    var markerCluster = new MarkerClusterer(map, map_markers);
}

function addMarker(item, address, open_infowindow) {
    if (!item.lat && !item.lon) {
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var marker = new google.maps.Marker({
                    'position': results[0].geometry.location,
                    'map': map,
                    'icon': item.icon
                });
                var infowindow = new google.maps.InfoWindow({
                    content: item.message
                });
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,marker);
                });
                //alert(results[0].geometry.viewport);
                //bounds.extend(results[0].geometry.viewport);
                bounds.extend(new google.maps.LatLng(results[0].geometry.location));
                map_markers.push(marker);
            }
        });
    } else {
        var marker = new google.maps.Marker({
            'position': new google.maps.LatLng(item.lat, item.lon),
            'map': map,
            'icon': item.icon
        });
        var infowindow = new google.maps.InfoWindow({
            content: item.message
        });
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map,marker);
        });
        //alert(results[0].geometry.viewport);
        bounds.extend(new google.maps.LatLng(item.lat, item.lon));
         map_markers.push(marker);
    }

    if (open_infowindow == true) {

        map.setCenter(bounds.getCenter());
        map.setZoom(16);

        //infowindow.open(map,marker);
        google.maps.event.trigger(marker, 'click');
        setTimeout(function() {
            var newBounds = map.getBounds();
            var northEast = newBounds.getNorthEast();

            bounds.extend(new google.maps.LatLng(northEast.lat(), item.lon));

            map.setCenter(bounds.getCenter());
            map.setZoom(16);
        }, 300);
    }
    
   
    
    
}

function zoomToViewports(markers_length) {
    if (markers_length == 1) {
        map.setCenter(bounds.getCenter());
        map.setZoom(16);
    } else {
        map.fitBounds(bounds);
    }
}

function get_markers_length(markers) {
    var markers_num = 0;
    for (var level in markers) {
        for (var i = 0; i < markers[level].length; i++) {
            if (markers[level][i])
                markers_num++;
        }
    }

    return markers_num;
}

function parseLatLng(value) {
    value.replace('/\s//g');
    var coords = value.split(',');
    var lat = parseFloat(coords[0]);
    var lng = parseFloat(coords[1]);
    if (isNaN(lat) || isNaN(lng)) {
        return null;
    } else {
        return new google.maps.LatLng(lat, lng);
    }
}


var GMaps = new Class({
	initialize: function(){
		$("map-preview_").setStyle('width', $('jform_params_map_width').value + 'px');
		$("map-preview_").setStyle('height', $('jform_params_map_height').value + 'px');
		this.origin = new google.maps.LatLng($('jform_params_center_lat').value, $('jform_params_center_lng').value);
		this.options = { zoom: parseInt($('jform_params_zoom').value), center: this.origin, mapTypeId: google.maps.MapTypeId.ROADMAP };
		this.map = new google.maps.Map($("map-preview_"), this.options);
		this.infoWindow = new google.maps.InfoWindow({ content: 'Hello, World!' });
		google.maps.event.addListener(this.map, "click", this.mapsDropMarker);
		if($('jform_marker_lat').value && $('jform_marker_lng').value){
			var coords = new google.maps.LatLng($('jform_marker_lat').value, $('jform_marker_lng').value);
			this.marker = new google.maps.Marker({ position: coords, map: this.map, maxWidth: 200, draggable: true, title: $('jform_marker_alias').value });
			google.maps.event.addListener(this.marker, "drag", this.mapsDragMarker);
			google.maps.event.addListener(this.marker, "click", this.showInfoWindow);
		}else{
			this.marker = null;
		}
	},

	mapsDropMarker: function(mapEvent){
		try{
			myMaps.infoWindow.close();
			$('jform_marker_lat').value = mapEvent.latLng.lat();
			$('jform_marker_lng').value = mapEvent.latLng.lng();
			var coords = new google.maps.LatLng(mapEvent.latLng.lat(), mapEvent.latLng.lng());
			if(myMaps.marker){
				myMaps.marker.setMap(null);
			}
			myMaps.marker = new google.maps.Marker({ position: coords, map: myMaps.map, draggable: true });
			google.maps.event.addListener(myMaps.marker, "drag", myMaps.mapsDragMarker);
			google.maps.event.addListener(myMaps.marker, "click", myMaps.showInfoWindow);
		}catch(e){
			myMaps.debug(e);
		}
	},

	mapsDragMarker: function(mapEvent){
		$('jform_marker_lat').value = mapEvent.latLng.lat();
		$('jform_marker_lng').value = mapEvent.latLng.lng();
	},
	
	showInfoWindow: function(mapEvent){
		var content = "<h1>" + $('jform_marker_name').value + "</h1>\n";
		content += $('jform_marker_description').value + "\n";
		myMaps.infoWindow.setContent(content);
		myMaps.infoWindow.setOptions({ maxWidth: 300 });
		myMaps.infoWindow.open(myMaps.map, myMaps.marker);		
	},
	
	debug: function(someError){
		try{
			window.console.log(someError);
		}catch(e){
			alert(someError);
		};
	}
});
window.addEvent('load', function(){ myMaps = new GMaps(); });

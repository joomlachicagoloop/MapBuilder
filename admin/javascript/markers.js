var GMaps = new Class({
	initialize: function(){
		$("map-preview_").setStyle('width', $('optionsmap_width').getValue() + 'px');
		$("map-preview_").setStyle('height', $('optionsmap_height').getValue() + 'px');
		this.origin = new google.maps.LatLng($('optionscenter_lat').getValue(), $('optionscenter_lng').getValue());
		this.options = { zoom: parseInt($('optionszoom').getValue()), center: this.origin, mapTypeId: google.maps.MapTypeId.ROADMAP };
		this.map = new google.maps.Map($("map-preview_"), this.options);
		this.infoWindow = new google.maps.InfoWindow({ content: 'Hello, World!' });
		google.maps.event.addListener(this.map, "click", this.mapsDropMarker);
		if($('optionsmarker_lat').getValue() && $('optionsmarker_lng').getValue()){
			var coords = new google.maps.LatLng($('optionsmarker_lat').getValue(), $('optionsmarker_lng').getValue());
			this.marker = new google.maps.Marker({ position: coords, map: this.map, maxWidth: 200, draggable: true, title: $('marker-alias_').getValue() });
			google.maps.event.addListener(this.marker, "drag", this.mapsDragMarker);
			google.maps.event.addListener(this.marker, "click", this.showInfoWindow);
		}else{
			this.marker = null;
		}
	},

	mapsDropMarker: function(mapEvent){
		try{
			myMaps.infoWindow.close();
			$('optionsmarker_lat').value = mapEvent.latLng.lat();
			$('optionsmarker_lng').value = mapEvent.latLng.lng();
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
		$('optionsmarker_lat').value = mapEvent.latLng.lat();
		$('optionsmarker_lng').value = mapEvent.latLng.lng();
	},
	
	showInfoWindow: function(mapEvent){
		var content = "<h1>" + $('marker-name_').getValue() + "</h1>\n";
		content += "<p>" + $('marker-description_').getValue() + "</p>\n";
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
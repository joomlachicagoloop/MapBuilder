var GMaps = new Class({
	initialize: function(){
		this.origin = new google.maps.LatLng($('paramscenter_lat').getValue(), $('paramscenter_lng').getValue());
		this.options = { zoom: parseInt($('paramszoom').getValue()), center: this.origin, mapTypeId: google.maps.MapTypeId.ROADMAP };
		this.map = new google.maps.Map($("map-preview_"), this.options);
		//google.maps.event.addListener(this.map, "click", this.mapsDropMarker.bindWithEvent(this));
		this.marker = new google.maps.Marker({ position: this.origin, map: this.map, draggable: true });
		google.maps.event.addListener(this.marker, "drag", this.mapsDragMarker);
		google.maps.event.addListener(this.map, "zoom_changed", this.mapsChangeZoom);
	},

	mapsDragMarker: function(mapEvent){
		window.console.log(mapEvent);
		$('paramscenter_lat').value = mapEvent.latLng.lat();
		$('paramscenter_lng').value = mapEvent.latLng.lng();
	},
	
	mapsChangeZoom: function(){
		try{
			$('paramszoom').value = myMaps.map.getZoom();
		}catch(e){
			myMaps.debug(e.message);
		}
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
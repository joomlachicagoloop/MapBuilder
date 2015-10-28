/**
 * Mapbuilder Maps.js Javascript file
 * Version I don't know
 * @type {Class}
 */



var GMaps = new Class({
	initialize: function(){
		this.lat = $('jform_params_center_lat').value;
		this.lng = $('jform_params_center_lng').value;
		this.zoom = $('jform_params_zoom').value;
		this.origin = new google.maps.LatLng(this.lat, this.lng);
		this.options = { zoom: parseInt(this.zoom), center: this.origin, mapTypeId: google.maps.MapTypeId.ROADMAP };
		this.map = new google.maps.Map($("map-preview_"), this.options);
		//google.maps.event.addListener(this.map, "click", this.mapsDropMarker.bindWithEvent(this));
		this.marker = new google.maps.Marker({ position: this.origin, map: this.map, draggable: true });
		google.maps.event.addListener(this.marker, "drag", this.mapsDragMarker);
		google.maps.event.addListener(this.map, "zoom_changed", this.mapsChangeZoom);
	},

	mapsDragMarker: function(mapEvent){
		$('jform_params_center_lat').value = mapEvent.latLng.lat();
		$('jform_params_center_lng').value = mapEvent.latLng.lng();
	},
	
	mapsChangeZoom: function(){
		try{
			$('jform_params_zoom').value = myMaps.map.getZoom();
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

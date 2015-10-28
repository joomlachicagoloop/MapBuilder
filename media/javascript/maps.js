/**
 * MapBuilder
 * This javascript file uses the Prototype framework.
 */
 
var Maps = new Class({
	initialize: function(element){
		this.element = $(element);
		this.lat = this.element.get('data-lat');
		this.lng = this.element.get('data-lng');
		this.zoom = parseInt( this.element.get('data-zoom') );
		this.uri = this.element.get('data-uri');
		this.mapId = parseInt( this.element.get('data-id') );
		this.markers = new Array();
		this.load = this.loadData.bind(this);
		this.parse = this.parseData.bind(this);
		this.build = this.buildMap.bind(this);
		this.error = this.parseError.bind(this);
		if(typeof google != 'undefined'){
			google.load("maps", "3.1", { other_params: "sensor=false", callback: this.mapsInitialized.bind(this) });
		}
	},
	
	mapsInitialized: function(){
		this.infoWindow = new google.maps.InfoWindow({ content: 'Hello, World!' });
		this.build();
	},
	
	loadData: function(someId){
		new Request.JSON({ url: this.uri + "/index.php?option=com_mapbuilder&format=json&layout=ajax&id=" + someId, method: 'GET', onSuccess: this.parse, onFailure: this.error }).send();
	},
	
	parseData: function(someAjax){
	    this.markers = someAjax;
		var someWindow = this.infoWindow;
		this.markers.each(function(someRecord){
			var coords = new google.maps.LatLng(someRecord.marker_lat, someRecord.marker_lng);
			var someMarker = new google.maps.Marker({ position: coords, map: this.map, title: someRecord.marker_alias });
			google.maps.event.addListener(someMarker, "click", function(){
				var content = "<h3>" + someRecord.marker_name + "</h3>";
				content += "<p>" + someRecord.marker_description + "</p>";
				someWindow.setContent(content);
				someWindow.setOptions({ maxWidth: 400 });
				someWindow.open(someMarker.getMap(), someMarker);
			});
		}, this);
	},
	
	parseError: function(someAjax){
	    if(window.console){
	        window.console.log('failure');
	        window.console.log(someAjax);
	    }
	},
	
	buildMap: function(){
		var someOrigin = new google.maps.LatLng(this.lat, this.lng);
		var mapOptions = { zoom: this.zoom, center: someOrigin, mapTypeId: google.maps.MapTypeId.ROADMAP };
		this.map = new google.maps.Map(this.element, mapOptions);
		this.load(this.mapId);
	}
});

window.addEvent('load', function(){ 
	$$('div.mapbuilder').each( function(i){
		new Maps(i);
	})
});

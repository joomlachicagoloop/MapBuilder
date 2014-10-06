var Maps = new Class({
	initialize: function(){
		this.maps = new Array();
		this.markers = new Array();
		this.load = this.loadData.bind(this);
		this.parse = this.parseData.bind(this);
		this.build = this.buildMap.bind(this);
		this.error = this.parseError.bind(this);
		this.find = this.findMap.bind(this);
		if(typeof google != 'undefined'){
			google.load("maps", "3.1", { other_params: "sensor=false", callback: this.mapsInitialized.bind(this) });
		}
	},
	
	mapsInitialized: function(){
		this.infoWindow = new google.maps.InfoWindow({ content: 'Hello, World!' });
		$$('div.mapbuilder').each(this.build);
	},
	
	loadData: function(someId){
		new Request.JSON({ url: "/index.php?option=com_mapbuilder&format=json&layout=ajax&id=" + someId, method: 'GET', onSuccess: this.parse, onFailure: this.error }).send();
	},
	
	parseData: function(someAjax){
	    this.markers = someAjax;
		var someWindow = this.infoWindow;
		this.markers.each(function(someRecord){
			var someMap = this.find(someRecord.map_id);
			var coords = new google.maps.LatLng(someRecord.marker_lat, someRecord.marker_lng);
			var someMarker = new google.maps.Marker({ position: coords, map: someMap, title: someRecord.marker_alias });
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
	    if(windown.console){
	        window.console.log('failure');
	        window.console.log(someAjax);
	    }
	},
	
	buildMap: function(container){
	    var someLat = container.get('data-lat');
	    var someLng = container.get('data-lng');
	    var someZoom = parseInt(container.get('data-zoom'));
	    var someId = parseInt(container.get('data-id'));
		var someOrigin = new google.maps.LatLng(someLat, someLng);
		var mapOptions = { zoom: someZoom, center: someOrigin, mapTypeId: google.maps.MapTypeId.ROADMAP };
		var someMap = new google.maps.Map(container, mapOptions);
		this.maps.push(someMap);
		this.load(someId);
	},
	
	findMap: function(someId){
		var mapList = this.maps.filter(function(someMap){
			var someContainer = someMap.getDiv();
			if(someContainer.get('data-id') == someId){
				return true;
			}else{
				return false;
			}
		}, this);
		return mapList[0];
	}
});

window.addEvent('load', function(){ myMaps = new Maps(); });

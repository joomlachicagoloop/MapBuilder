var Maps = new Class({
	initialize: function(){
		this.maps = new Array();
		this.markers = new Array();
		this.load = this.loadData.bind(this);
		this.parse = this.parseData.bind(this);
		this.build = this.buildMap.bind(this);
		this.error = this.parseError.bind(this);
		this.find = this.findMap.bind(this);
		this.latX = /lat(-?\d+\.\d+)/;
		this.lngX = /lng(-?\d+\.\d+)/;
		this.zoomX = /zoom(\d{1,2})/;
		this.idX = /id(\d+)/;
		if(typeof google != 'undefined'){
			google.load("maps", "3.1", { other_params: "sensor=false", callback: this.mapsInitialized.bind(this) });
		}
	},
	
	mapsInitialized: function(){
		this.infoWindow = new google.maps.InfoWindow({ content: 'Hello, World!' });
		$$('div.google-map_').each(this.build);
	},
	
	loadData: function(someId){
		new Request.JSON({ url: "/index.php?option=com_maps&format=json&layout=ajax&id=" + someId, method: 'GET', onSuccess: this.parse, onFailure: this.error }).send();
	},
	
	parseData: function(someAjax){
	    this.markers = someAjax;
		var someWindow = this.infoWindow;
		this.markers.each(function(someRecord){
			var someMap = this.find(someRecord.maps_id);
			var coords = new google.maps.LatLng(someRecord.marker_lat, someRecord.marker_lng);
			var someMarker = new google.maps.Marker({ position: coords, map: someMap, title: someRecord.marker_alias });
			google.maps.event.addListener(someMarker, "click", function(){
				var content = "<h3>" + someRecord.marker_name + "</h3>";
				content += "<p>" + someRecord.marker_description + "</p>";
				someWindow.setContent(content);
				someWindow.setOptions({ maxWidth: 300 });
				someWindow.open(someMarker.getMap(), someMarker);
			});
		}, this);
	},
	
	parseError: function(someAjax){
	    window.console.log('failure');
	    window.console.log(someAjax);
	},
	
	buildMap: function(container){
		if(container.className.match(this.latX)){
			var someLat = RegExp.$1;
		}else{
			return false;
		}
		if(container.className.match(this.lngX)){
			var someLng = RegExp.$1;
		}else{
			return false;
		}
		if(container.className.match(this.zoomX)){
			var someZoom = parseInt(RegExp.$1);
		}else{
			return false;
		}
		if(container.className.match(this.idX)){
			var someId = parseInt(RegExp.$1);
		}
		var someOrigin = new google.maps.LatLng(someLat, someLng);
		var mapOptions = { zoom: someZoom, center: someOrigin, mapTypeId: google.maps.MapTypeId.ROADMAP };
		var someMap = new google.maps.Map(container, mapOptions);
		this.maps.push(someMap);
		this.load(someId);
	},
	
	findMap: function(someId){
		var mapList = this.maps.filter(function(someMap){
			var someContainer = someMap.getDiv();
			if(someContainer.className.match(this.idX)){
				return RegExp.$1 == someId;
			}else{
				return false;
			}
		}, this);
		return mapList[0];
	}
});

window.addEvent('load', function(){ myMaps = new Maps(); });

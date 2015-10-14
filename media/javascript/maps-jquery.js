(function() {
	var $, MapBuilderUI;
	$ = jQuery;
	MapBuilderUI = (function(element) {
		function MapBuilderUI(element) {
			// CONSTRUCTOR METHOD
			this.element = $(element);
            this.lat = this.element.data('lat');
            this.lng = this.element.data('lng');
            this.uri = this.element.data('uri');
            this.zoom = parseInt(this.element.data('zoom'));
            this.mapId = parseInt(this.element.data('id'));
			this.markers = new Array();
			this.load = $.proxy(this.loadData, this);
			this.parse = $.proxy(this.parseData, this);
			this.build = $.proxy(this.buildMap, this);
			this.error = $.proxy(this.parseError, this);
			this.autoload = $.proxy(this.mapsInitialized, this);
			if(typeof google != 'undefined'){
			    google.load("maps", "3.1", { other_params: "sensor=false", callback: this.autoload });
		    }
		}
		
		MapBuilderUI.prototype.mapsInitialized = function() {
            this.infoWindow = new google.maps.InfoWindow({ content: 'Hello, World!' });
            this.build();
		}

        MapBuilderUI.prototype.loadData = function(someId){
            $.ajax( this.uri + "/index.php?option=com_mapbuilder&format=json&layout=ajax&id=" + someId, { dataType: "json", success: this.parse, error: this.error } );
        }
        
        MapBuilderUI.prototype.parseData = function(someAjax){
            this.markers = someAjax;
            var someWindow = this.infoWindow;
            var self = this;
            $.each(self.markers, function(someIndex, someRecord){
                var coords = new google.maps.LatLng(someRecord.marker_lat, someRecord.marker_lng);
                var someMarker = new google.maps.Marker({ position: coords, map: self.map, title: someRecord.marker_alias });
                google.maps.event.addListener(someMarker, "click", function(){
                    var content = "<h3>" + someRecord.marker_name + "</h3>";
                    content += "<p>" + someRecord.marker_description + "</p>";
                    someWindow.setContent(content);
                    someWindow.setOptions({ maxWidth: 400 });
                    someWindow.open(someMarker.getMap(), someMarker);
                });
            });
        }
        
        MapBuilderUI.prototype.parseError = function(someAjax){
            if(windown.console){
                window.console.log('failure');
                window.console.log(someAjax);
            }
        }
        
        MapBuilderUI.prototype.buildMap = function(){
            var someOrigin = new google.maps.LatLng(this.lat, this.lng);
            var mapOptions = { zoom: this.zoom, center: someOrigin, mapTypeId: google.maps.MapTypeId.ROADMAP };
            this.map = new google.maps.Map(this.element.get(0), mapOptions);
            this.load(this.mapId);
        }
        
		return MapBuilderUI;
	})();
	
	$(function() {
		var maps = [];
		$('div.mapbuilder').each( function(){
			maps.push( new MapBuilderUI(this) );
		});
		return maps;
	});
}).call(this);

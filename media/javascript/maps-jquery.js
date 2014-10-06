(function() {
	var $, MapBuilderUI;
	$ = jQuery;
	MapBuilderUI = (function() {
		function MapBuilderUI() {
			// CONSTRUCTOR METHOD
			this.maps = new Array();
			this.markers = new Array();
			this.load = $.proxy(this.loadData, this);
			this.parse = $.proxy(this.parseData, this);
			this.build = $.proxy(this.buildMap, this);
			this.error = $.proxy(this.parseError, this);
			this.find = $.proxy(this.findMap, this);
			this.autoload = $.proxy(this.mapsInitialized, this);
			if(typeof google != 'undefined'){
			    google.load("maps", "3.1", { other_params: "sensor=false", callback: this.autoload });
		    }
		}
		
		MapBuilderUI.prototype.mapsInitialized = function() {
            this.infoWindow = new google.maps.InfoWindow({ content: 'Hello, World!' });
            $('div.mapbuilder').each(this.build);
		}

        MapBuilderUI.prototype.loadData = function(someId){
            $.ajax("/index.php?option=com_mapbuilder&format=json&layout=ajax&id=" + someId, { dataType: "json", success: this.parse, error: this.error } );
        }
        
        MapBuilderUI.prototype.parseData = function(someAjax){
            this.markers = someAjax;
            var someWindow = this.infoWindow;
            var self = this;
            $.each(self.markers, function(someIndex, someRecord){
                var someMap = self.find(someRecord.map_id);
                var coords = new google.maps.LatLng(someRecord.marker_lat, someRecord.marker_lng);
                var someMarker = new google.maps.Marker({ position: coords, map: someMap, title: someRecord.marker_alias });
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
        
        MapBuilderUI.prototype.buildMap = function(index, container){
            var el = $(container);
            var someLat = el.data('lat');
            var someLng = el.data('lng');
            var someZoom = parseInt(el.data('zoom'));
            var someId = parseInt(el.data('id'));
            var someOrigin = new google.maps.LatLng(someLat, someLng);
            var mapOptions = { zoom: someZoom, center: someOrigin, mapTypeId: google.maps.MapTypeId.ROADMAP };
            var someMap = new google.maps.Map(container, mapOptions);
            this.maps.push(someMap);
            this.load(someId);
        }
        
        MapBuilderUI.prototype.findMap = function(someId){
            var mapList = $.map(this.maps, function(someMap){
                var someContainer = someMap.getDiv();
                if(someContainer.get('data-id') == someId){
                    return someMap;
                }else{
                    return false;
                }
            }, this);
            return mapList[0];
        }
        
		return MapBuilderUI;
	})();
	
	$(function() {
		return new MapBuilderUI();
	});
}).call(this);

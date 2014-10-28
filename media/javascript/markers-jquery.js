(function() {
	var $, MapBuilderUI;
	$ = jQuery;
	MapBuilderUI = (function(element) {
		function MapBuilderUI(element) {
			// CONSTRUCTOR METHOD
			this.map = null;
			this.markers = new Array();
			this.element = $(element);
			this.load = $.proxy(this.loadData, this);
			this.parse = $.proxy(this.parseData, this);
			this.build = $.proxy(this.buildMap, this);
			this.error = $.proxy(this.parseError, this);
			this.find = $.proxy(this.findMap, this);
			this.locate = $.proxy(this.geoLocate, this);
			this.code = $.proxy(this.geoCode, this);
			this.search = $.proxy(this.searchForLocation, this);
			this.track = $.proxy(this.trackLocation, this);
			this.autoload = $.proxy(this.mapsInitialized, this);
			this.submit = $.proxy(this.submitFormViaAjax, this);
			this.success = $.proxy(this.handleAjaxSuccess, this);
			this.error = $.proxy(this.handleAjaxError, this);
			this.counter = 0;
			if(typeof google != 'undefined'){
			    google.load("maps", "3.1", { other_params: "sensor=false", callback: this.autoload });
		    }
			$('#geolocation').on("click", this.search);
			$('#mapbuilder-tracking').on("click", this.track);
		    // ADD FORM VALIDATION FILTERS
			document.formvalidator.setHandler('uint', function(value){
				re_uint = /^\d+$/;
				return re_uint.test(value);
			});
			document.formvalidator.setHandler('string', function(value){
				re_string = /^([\w\d\s-_\.,&'#\u00E0-\u00FC]+)?$/;
				return re_string.test(value);
			});
			document.formvalidator.setHandler('cmd', function(value){
				re_cmd = /^([\w-_]+)$/;
				return re_cmd.test(value);
			});
			document.formvalidator.setHandler('float', function(value){
				re_cmd = /^-?(\d+)\.(\d+)$/;
				return re_cmd.test(value);
			});
		}
		
		MapBuilderUI.prototype.mapsInitialized = function() {
			this.geocoder = new google.maps.Geocoder();
            this.infoWindow = new google.maps.InfoWindow({ content: 'Hello, World!' });
            this.build(this.element);
            if(navigator.geolocation){
            	navigator.geolocation.getCurrentPosition(this.locate);
            }else{
            	this.geocoder.geocode( { 'address': $('#location_search').val(), 'country': 'us' }, this.code );
            }
		}

        MapBuilderUI.prototype.loadData = function(someId){
            $.ajax("/index.php?option=com_mapbuilder&format=json&layout=ajax&id=" + someId, { dataType: "json", success: this.parse, error: this.error } );
        }
        
        MapBuilderUI.prototype.parseData = function(someAjax){
            this.markers = someAjax;
            var someWindow = this.infoWindow;
            var self = this;
            var someMap = self.map;
            $.each(self.markers, function(someIndex, someRecord){
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
        
        MapBuilderUI.prototype.buildMap = function(container){
            var el = this.element.get(0);
            var someLat = container.data('lat');
            var someLng = container.data('lng');
            var someZoom = parseInt(container.data('zoom'));
            var someId = parseInt(container.data('id'));
            var someOrigin = new google.maps.LatLng(someLat, someLng);
            var mapOptions = { zoom: someZoom, center: someOrigin, mapTypeId: google.maps.MapTypeId.ROADMAP };
            this.map = new google.maps.Map(el, mapOptions);
            this.load(someId);
        }
        
        MapBuilderUI.prototype.findMap = function(someId){
            var mapList = $.map(this.maps, function(someMap){
                var someContainer = someMap.getDiv();
                if($(someContainer).data('id') == someId){
                    return someMap;
                }else{
                    return false;
                }
            }, this);
            return mapList[0];
        }
        
        MapBuilderUI.prototype.searchForLocation = function(evt){
			evt.preventDefault();
			this.geocoder.geocode( { 'address': $('#location_search').val(), 'country': 'us' }, this.code );
        }
        
        MapBuilderUI.prototype.trackLocation = function(evt){
        	evt.preventDefault();
        	var form = $('#mapbuilder-submit-form').get(0);
        	var button = $(evt.delegateTarget);
        	if( button.hasClass( 'active' ) ){
        	    if( this.timeout ) clearTimeout(this.timeout);
        	    if( this.watchid ) navigator.geolocation.clearWatch( this.watchid );
        		button.removeClass( 'active' );
        		button.removeClass( 'btn-danger' );
        		button.addClass( 'btn-success' );
        		button.html('Start Tracking');
        		$( '#mapbuilder-spinner' ).addClass( 'hidden' );
        	}else{
				if(!document.formvalidator.isValid(form)){
					return false;
				}
				if(!navigator.geolocation){
				    $('#system-message-container').html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Sorry</h4>This device does not support geolocation, please search for your current location city & state, zip code, or any nearby landmark.</div>');
				    return false;
				}
				this.watchid = navigator.geolocation.watchPosition( function(pos){
					$('#jform_marker_lng').val(pos.coords.longitude);
					$('#jform_marker_lat').val(pos.coords.latitude);
				});
				this.submit();
        		button.removeClass('btn-success');
        		button.addClass('btn-danger');
        		button.addClass('active');
        		button.html('Stop Tracking');
        		$( '#mapbuilder-spinner' ).removeClass( 'hidden' );
        	}
        }
        
        MapBuilderUI.prototype.submitFormViaAjax = function(){
            var formData = $('#mapbuilder-submit-form').serialize();
            formData += '&tmpl=component';
            $.ajax("/index.php", { type: "POST", data: formData, success: this.success, error: this.error });
        }
        
        MapBuilderUI.prototype.handleAjaxSuccess = function(msg){
            this.counter++;
            $('#system-message-container').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Success!</h4>You have added '+this.counter+' markers. A new marker will be added every 60 seconds.</div>');
            this.timeout = setTimeout(this.submit, 60000);
        }
        
        MapBuilderUI.prototype.handleAjaxError = function(xhr, msg, exception){
        
        }
        
        MapBuilderUI.prototype.geoLocate = function(location){
			this.center_lat	= location.coords.latitude;
			this.center_lng	= location.coords.longitude;
			this.accuracy	= location.coords.accuracy;
			this.coords		= new google.maps.LatLng(this.center_lat, this.center_lng);
			if(! this.origin){
				this.origin = new google.maps.Marker({ animation: google.maps.Animation.DROP, position: this.coords, map: this.map, title: 'drag to set location', icon: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', zIndex: google.maps.Marker.MAX_ZINDEX + 1, draggable: true });
				google.maps.event.addListener(this.origin, "drag", function(evt){
					$('#jform_marker_lng').val(evt.latLng.lng());
					$('#jform_marker_lat').val(evt.latLng.lat());
				});
			}else{
				this.origin.setPosition(this.coords);
			}
			this.map.panTo(this.coords);
			this.updateForm(this.center_lat, this.center_lng);
        }

		MapBuilderUI.prototype.geoCode = function(results, status){
			if(status == google.maps.GeocoderStatus.OK){
				if(status != google.maps.GeocoderStatus.ZERO_RESULTS){
					this.center_lat = results[0].geometry.location.lat();
					this.center_lng = results[0].geometry.location.lng();
					this.coords = results[0].geometry.location;
					if(! this.origin){
						this.origin = new google.maps.Marker({ animation: google.maps.Animation.DROP, position: this.coords, map: this.map, title: 'drag to set location', icon: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', zIndex: google.maps.Marker.MAX_ZINDEX + 1, draggable: true });
						google.maps.event.addListener(this.origin, "drag", function(evt){
							$('#jform_marker_lng').val(evt.latLng.lng());
							$('#jform_marker_lat').val(evt.latLng.lat());
						});
					}else{
						this.origin.setPosition(this.coords);
					}
					this.map.panTo(this.coords);
					this.updateForm(this.center_lat, this.center_lng);
				}
			}
		}
		
		MapBuilderUI.prototype.updateForm = function(lat, lng){
			$('#jform_marker_lng').val(lng);
			$('#jform_marker_lat').val(lat);
		}
		
		return MapBuilderUI;
	})();
	
	$(function() {
		return $('.mapbuilder').map(function(){
			new MapBuilderUI(this);
		});
	});
}).call(this);

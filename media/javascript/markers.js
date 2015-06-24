var Markers = new Class({
	initialize: function( element )
	{
		// CONSTRUCTOR METHOD
		this.map = null;
		this.markers = new Array();
		this.element = $(element);
		this.load = this.loadData.bind(this);
		this.parse = this.parseData.bind(this);
		this.build = this.buildMap.bind(this);
		this.error = this.parseError.bind(this);
		this.find = this.findMap.bind(this);
		this.locate = this.geoLocate.bind(this);
		this.code = this.geoCode.bind(this);
		this.search = this.searchForLocation.bind(this);
		this.track = this.trackLocation.bind(this);
		this.autoload = this.mapsInitialized.bind(this);
		this.submit = this.submitFormViaAjax.bind(this);
		this.success = this.handleAjaxSuccess.bind(this);
		this.error = this.handleAjaxError.bind(this);
		this.counter = 0;
		if(typeof google != 'undefined'){
			google.load("maps", "3.1", { other_params: "sensor=false", callback: this.autoload });
		}
		$('geolocation').addEvent('click', this.search);
		$('mapbuilder-tracking').addEvent('click', this.track);
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
	},
	
	mapsInitialized: function(){
		this.geocoder = new google.maps.Geocoder();
		this.infoWindow = new google.maps.InfoWindow({ content: 'Hello, World!' });
		this.build(this.element);
		if(navigator.geolocation){
			navigator.geolocation.getCurrentPosition(this.locate);
		}else{
			this.geocoder.geocode( { 'address': $('location_search').get('value'), 'country': 'us' }, this.code );
		}
	},
	
	loadData: function(someId){
        new Request.JSON({
        	url: "/index.php?option=com_mapbuilder&format=json&layout=ajax&id=" + someId,
        	onSuccess: this.parse,
        	onFailure: this.error
        }).send();
	},
	
	parseData: function(someAjax){
		this.markers = someAjax;
		this.markers.each(function(someRecord){
			var coords = new google.maps.LatLng(someRecord.marker_lat, someRecord.marker_lng);
			var someMarker = new google.maps.Marker({ position: coords, map: this.map, title: someRecord.marker_alias });
			google.maps.event.addListener(someMarker, "click", function(){
				var content = "<h3>" + someRecord.marker_name + "</h3>";
				content += "<p>" + someRecord.marker_description + "</p>";
				this.infoWindow.setContent(content);
				this.infoWindow.setOptions({ maxWidth: 400 });
				this.infoWindow.open(someMarker.getMap(), someMarker);
			}, this);
		});
	},
	
	parseError: function(someAjax){
		if(windown.console){
			window.console.log('failure');
			window.console.log(someAjax);
		}
	},
	
	buildMap: function(container){
		var el = this.element;
		var someLat = container.get('data-lat');
		var someLng = container.get('data-lng');
		var someZoom = parseInt(container.get('data-zoom'));
		var someId = parseInt(container.get('data-id'));
		var someOrigin = new google.maps.LatLng(someLat, someLng);
		var mapOptions = { zoom: someZoom, center: someOrigin, mapTypeId: google.maps.MapTypeId.ROADMAP };
		this.map = new google.maps.Map(el, mapOptions);
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
	},
	
	searchForLocation: function(evt){
		evt.stop();
		this.geocoder.geocode( { 'address': $('location_search').get('value'), 'country': 'us' }, this.code );
	},
	
	trackLocation: function(evt){
		evt.stop();
		var form = $('mapbuilder-submit-form');
		var button = $(evt.target);
		if( button.hasClass( 'active' ) ){
			if( this.timeout ) clearTimeout(this.timeout);
			if( this.watchid ) navigator.geolocation.clearWatch( this.watchid );
			button.removeClass( 'active' );
			button.removeClass( 'btn-danger' );
			button.addClass( 'btn-success' );
			button.set('html', 'Start Tracking');
			$( 'mapbuilder-spinner' ).addClass( 'hidden' );
		}else{
			if(!document.formvalidator.isValid(form)){
				return false;
			}
			if(!navigator.geolocation){
				$('system-message-container').set('html', '<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Sorry</h4>This device does not support geolocation, please search for your current location city & state, zip code, or any nearby landmark.</div>');
				return false;
			}
			this.watchid = navigator.geolocation.watchPosition( function(pos){
				$('jform_marker_lng').set('value', pos.coords.longitude);
				$('jform_marker_lat').set('value', pos.coords.latitude);
			});
			this.submit();
			button.removeClass('btn-success');
			button.addClass('btn-danger');
			button.addClass('active');
			button.set('html', 'Stop Tracking');
			$( 'mapbuilder-spinner' ).removeClass( 'hidden' );
		}
	},
	
	submitFormViaAjax: function(){
		var formData = $('mapbuilder-submit-form').toQueryString();
		formData += '&tmpl=component';
		new Request({ url: "/index.php", data: formData, onSuccess: this.success, onFailure: this.error }).post();
	},
	
	handleAjaxSuccess: function( msg ){
        	var matches = msg.match(/<div id="system-message-container">[\s\S]+?<div class="alert (alert-\w+)">([\s\S]+?)<\/p>/g);
        	var response = matches[0];
        	var alert = RegExp.$1;
        	var heading = response.match(/<h4.*?>(.*)<\/h4>/g);
        	var message = response.match(/<p>(.*)<\/p>/g);
        	if(alert == 'alert-error'){
            	$('system-message-container').set('html', '<div class="alert '+alert+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+heading[0]+message[0]+'</div>');
        	    if( this.timeout ) clearTimeout(this.timeout);
        	    if( this.watchid ) navigator.geolocation.clearWatch( this.watchid );
        		var button = $('#mapbuilder-tracking');
        		button.removeClass( 'active' );
        		button.removeClass( 'btn-danger' );
        		button.addClass( 'btn-success' );
        		button.set('html', 'Start Tracking');
        		$( 'mapbuilder-spinner' ).addClass( 'hidden' );
        		return false;
        	}
            this.counter++;
            $('system-message-container').set('html', '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+heading[0]+'<p>You have added '+this.counter+' markers. A new marker will be added every 60 seconds.</p></div>');
            this.timeout = setTimeout(this.submit, 60000);
	},
	
	handleAjaxError: function(xhr){
		window.console.log(xhr);
		$('system-message-container').set('html', '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4 class="alert-heading">Error!</h4><p>'+msg+'</p></div>');
		if( this.timeout ) clearTimeout(this.timeout);
		if( this.watchid ) navigator.geolocation.clearWatch( this.watchid );
		var button = $('mapbuilder-tracking');
		button.removeClass( 'active' );
		button.removeClass( 'btn-danger' );
		button.addClass( 'btn-success' );
		button.set('html', 'Start Tracking');
		$( 'mapbuilder-spinner' ).addClass( 'hidden' );
	},
	
	geoLocate: function(location){
			this.center_lat	= location.coords.latitude;
			this.center_lng	= location.coords.longitude;
			this.accuracy	= location.coords.accuracy;
			this.coords		= new google.maps.LatLng(this.center_lat, this.center_lng);
			if(! this.origin){
				this.origin = new google.maps.Marker({ animation: google.maps.Animation.DROP, position: this.coords, map: this.map, title: 'drag to set location', icon: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', zIndex: google.maps.Marker.MAX_ZINDEX + 1, draggable: true });
				google.maps.event.addListener(this.origin, "drag", function(evt){
					$('jform_marker_lng').set('value', evt.latLng.lng());
					$('jform_marker_lat').set('value', evt.latLng.lat());
				});
			}else{
				this.origin.setPosition(this.coords);
			}
			this.map.panTo(this.coords);
			this.updateForm(this.center_lat, this.center_lng);
	},
	
	geoCode: function(results, status){
		if(status == google.maps.GeocoderStatus.OK){
			if(status != google.maps.GeocoderStatus.ZERO_RESULTS){
				this.center_lat = results[0].geometry.location.lat();
				this.center_lng = results[0].geometry.location.lng();
				this.coords = results[0].geometry.location;
				if(! this.origin){
					this.origin = new google.maps.Marker({ animation: google.maps.Animation.DROP, position: this.coords, map: this.map, title: 'drag to set location', icon: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', zIndex: google.maps.Marker.MAX_ZINDEX + 1, draggable: true });
					google.maps.event.addListener(this.origin, "drag", function(evt){
						$('jform_marker_lng').set('value', evt.latLng.lng());
						$('jform_marker_lat').set('value', evt.latLng.lat());
					});
				}else{
					this.origin.setPosition(this.coords);
				}
				this.map.panTo(this.coords);
				this.updateForm(this.center_lat, this.center_lng);
			}
		}
	},
	
	updateForm: function(lat, lng){
		$('jform_marker_lng').set('value', lng);
		$('jform_marker_lat').set('value', lat);
	}
});

window.addEvent('load', function(){
	myMaps = new Array();
	$$('.mapbuilder').each( function(i){
		new Markers(i);
	})
});

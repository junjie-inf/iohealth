var LocationPicker = {
	
	map: null,
	
	latitude: null,
			
	longitude: null,
	
	address: null,
	
	markerType: null,
	
	geodata: null,

	polyline: null,

	polygon: null,

	markers: null,

	isPolygon: null,

	geo: null,

	coordinates: [],

	tmp_geojson: {type: null, coordinates: null},
	
	init: function( $form, markerType ){
		var self = this;
		self.markerType = markerType || null;

		// Guardamos objetos input latitud y longitud
		self.latitude = $form.find('.location-picker-latitude');
		self.longitude = $form.find('.location-picker-longitude');
		self.address = $form.find('.location-picker-address');
		self.geo = $form.find('[name=geo]');
		
		// Creamos campos hidden en el form
		self.geodata = $('<input type="hidden" name="geodata" />');
		self.geodata.appendTo( $form );
		
		self.bindInput( self.latitude, function(){
			self.doGeocodingByCoords( self.latitude.val().trim(), self.longitude.val().trim() );
		});
		self.bindInput( self.longitude, function(){
			self.doGeocodingByCoords( self.latitude.val().trim(), self.longitude.val().trim() );
		});
		self.bindInput( self.address, function(){
			self.doGeocodingByAddress( self.address );
		});

		self.isPolygon = false;

		self.map = new GMaps({
			div: '#mapgeocoding',
			lat: self.latitude.val(),
			lng: self.longitude.val(),
			zoom: 18,

			click: function(e) {
				if ( !self.isPolygon )
				{
					self.latitude.val( e.latLng.lat() );
					self.longitude.val( e.latLng.lng() );
					//self.map.removeMarkers();

					self.setMarker( e.latLng.lat(), e.latLng.lng() );

					self.setAddress( e.latLng.lat(), e.latLng.lng() );

					//self.setAllMap( self.map.map );
				}
			}
		});

		self.markers = [];

		var polylineOptions = {
			geodesic: true,
			strokeColor: '#94999C',
			strokeOpacity: 1.0,
			strokeWeight: 2,
	  	};
	 	self.polyline = new google.maps.Polyline(polylineOptions);
	  	self.polyline.setMap(self.map.map);

	  	var polygonOptions = {
			geodesic: true,
			strokeColor: '#94999C',
			strokeOpacity: 1.0,
			strokeWeight: 2,
			fillColor: '#94999C',
			fillOpacity: 0.35
	  	};
	  	self.polygon = new google.maps.Polygon(polygonOptions);
	  	self.polygon.setMap(self.map.map);

	  	var clearControlDiv = document.createElement('div');
		var clearControl = self.clearControl(clearControlDiv,this.map.map);

	  	clearControlDiv.index = 1;
	  	self.map.map.controls[google.maps.ControlPosition.RIGHT_TOP].push(clearControlDiv);

		CoolReport.addGeolocateButton( self.map ); // Add geolocate button

		// Si es edit, cargo el geojson
		if ( typeof(geojson) != 'undefined' )
		{
			self.geo.val(JSON.stringify(geojson));
			if ( geojson.type == 'Point' )
			{
				self.setMarker(geojson.coordinates[1], geojson.coordinates[0]);
				self.setAddress(geojson.coordinates[1], geojson.coordinates[0]);
			}
			else if ( geojson.type == 'LineString' )
			{
				for (var i = 0; i < geojson.coordinates.length; i++)
				{
					self.setMarker(geojson.coordinates[i][1], geojson.coordinates[i][0]);
					self.setAddress(geojson.coordinates[i][1], geojson.coordinates[i][0]);
				}
			}		
			else
			{
				for (var i = 0; i < geojson.coordinates[0].length-1; i++)
				{
					self.setMarker(geojson.coordinates[0][i][1], geojson.coordinates[0][i][0]);
					self.setAddress(geojson.coordinates[0][i][1], geojson.coordinates[0][i][0]);
					self.isPolygon = true;
					self.setAllMap(self.map.map, false);
				}
			}

			// Si no es un Point, utilizo el bbox para ajustar el zoom/area mostrada
			if ( geojson.type != 'Point' )
			{
				self.map.map.fitBounds(
					new google.maps.LatLngBounds(
						new google.maps.LatLng(geojson.bbox[1], geojson.bbox[0]),
						new google.maps.LatLng(geojson.bbox[3], geojson.bbox[2])));
			}
		}
	},

	setAllMap: function  ( map, dragged )
	{
		var self = this;
		var path = [];

		for (var i = 0; i < self.markers.length; i++)
		{
			self.markers[i].setMap(map);

			if ( map != null )
			{	
				path.push(self.markers[i].getPosition());
			}

		}

		if (self.isPolygon )
		{
			// Añado el primer nodo al final para cerrar el poligono
			path.push(self.markers[0].getPosition());

			// Solo añade el primer punto si no se esta arrastrando
			if ( !dragged )
			{
				self.coordinates.push(self.coordinates[0]);
			}			

			self.polyline.setPath([]);
			self.polygon.setPath(path);

			self.tmp_geojson.type = 'Polygon';
			self.tmp_geojson.coordinates = [self.coordinates];
			self.geo.val( JSON.stringify(self.tmp_geojson) );
		}
		else
		{	
			self.polygon.setPath([]);
			self.polyline.setPath(path);	

			if ( self.markers.length <= 1 )
			{
				self.tmp_geojson.coordinates[0] = self.coordinates[0];
				self.tmp_geojson.coordinates[1] = self.coordinates[1];
			}
			else
			{
				self.tmp_geojson.coordinates = self.coordinates;
			}
			
			self.geo.val( JSON.stringify(self.tmp_geojson) );
		}
		
	},

	clearControl: function (controlDiv, map) 
	{
		var self = this; 
		// Set CSS styles for the DIV containing the control
		// Setting padding to 5 px will offset the control
		// from the edge of the map
		controlDiv.style.padding = '5px';

		// Set CSS for the control border
		var controlUI = document.createElement('div');
		controlUI.style.backgroundColor = 'white';
		controlUI.style.borderStyle = 'solid';
		controlUI.style.borderWidth = '2px';
		controlUI.style.cursor = 'pointer';
		controlUI.style.textAlign = 'center';
		controlUI.title = 'Click to clear the map';
		controlDiv.appendChild(controlUI);

		// Set CSS for the control interior
		var controlText = document.createElement('div');
		controlText.style.fontFamily = 'Arial,sans-serif';
		controlText.style.fontSize = '12px';
		controlText.style.paddingLeft = '4px';
		controlText.style.paddingRight = '4px';
		controlText.innerHTML = '<b>Clear map</b>';
		controlUI.appendChild(controlText);

		// Setup the click event listeners: clear
		google.maps.event.addDomListener(controlUI, 'click', function() {
			self.isPolygon = false;
			self.setAllMap(null, false);
			self.markers = [];
			self.tmp_geojson = {type: null, coordinates: null};
			self.coordinates = [];
		});

	},
	
	bindInput: function( $object, $callback ){
		$object
			.keypress(function(e){
				if((e.keyCode ? e.keyCode : e.which) == '13'){
					e.preventDefault();
					$callback();
				}
			})
/*			.blur(function(e){
				e.preventDefault();
				$callback();
			});*/
	},
			
	setMarker: function( $lat, $lng ){
		var self = this;
		var path = self.polyline.getPath();

		var marker = new google.maps.Marker({
			position: new google.maps.LatLng($lat, $lng),
			icon: $SITE_PATH + 'img/' + ( self.markerType === null ? $MARKER_TYPES.default : $MARKER_TYPES[self.markerType] ),
			draggable: true
		});

		self.markers.push(marker);

		path.push(marker.getPosition());

		if ( self.markers.length <= 1 )
		{	
			self.tmp_geojson.type = 'Point';
			self.tmp_geojson.coordinates = [$lng, $lat];
			self.coordinates.push([$lng, $lat]);
		}
		else if ( self.markers.length > 1 ) 
		{
			self.tmp_geojson.type = 'LineString';
			self.coordinates.push([$lng, $lat]);
			self.tmp_geojson.coordinates = self.coordinates;
		}

		google.maps.event.addListener(marker, 'dragend', function(e) {
			if ( self.markers.length <= 1 )
			{
				self.coordinates[0] = marker.position.lng();
				self.coordinates[1] = marker.position.lat();
			}
			else
			{
				self.coordinates.splice(self.markers.indexOf(marker), 1, [marker.position.lng(), marker.position.lat()]);

				if ( self.isPolygon && self.markers.indexOf(marker) === 0 )
				{
					self.coordinates.splice(self.coordinates.length-1, 1, [marker.position.lng(), marker.position.lat()]);
				}

			}
			self.setAddress( e.latLng.lat(), e.latLng.lng() );
			self.latitude.val( e.latLng.lat() );
			self.longitude.val( e.latLng.lng() );
			
			self.setAllMap(self.map.map, true);
		});

		google.maps.event.addListener(marker, 'click', function(e) {
			if ( e.latLng == self.markers[0].getPosition() )
			{
				self.isPolygon = true;
				//self.setMarker( e.latLng.lat(), e.latLng.lng() );
				self.setAllMap(self.map.map, false);
			}
		});
				
		marker.setMap(self.map.map);

		self.geo.val( JSON.stringify(self.tmp_geojson) );
	},
	
	doGeocodingByCoords: function ( $lat, $lng ){
		var self = this;
		
		if( $lat !== '' && $lng !== '' ){
			self.map.setCenter($lat, $lng);
			self.map.removeMarkers();
			self.setMarker( $lat, $lng );
		};
		
		self.setAddress( $lat, $lng ); // Actualizamos input de address
	},

	doGeocodingByAddress: function ( $input ){
		var self = this;
		
		var $address = $input.val().trim();
		if( $address !== '' ){
			GMaps.geocode({
				address: $address,
				callback: function(results, status){
					if(status=='OK'){
						var latlng = results[0].geometry.location;
						self.map.setCenter(latlng.lat(), latlng.lng());
						self.map.removeMarkers();
						self.setMarker(latlng.lat(), latlng.lng());
						self.latitude.val( latlng.lat() );
						self.longitude.val( latlng.lng() );
						self.setAddress( latlng.lat(), latlng.lng() );
					}
				}
			});
		};
	},

	setAddress: function ( $lat, $lng ){
		var self = this;

		var $latlng = new google.maps.LatLng($lat, $lng);
		GMaps.geocode({
			latLng: $latlng,
			callback: function(results, status){
				if( status == 'OK' ){
					if( results[0] ){
						self.address.val( results[0].formatted_address );
						self.geodata.val( $.toJSON(results[0]) );				
					}
				}
			}
		});
	}		
};
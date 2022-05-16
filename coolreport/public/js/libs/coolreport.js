var CoolReport = {
	
	gmap: null, // object GMap
			
	start: null, // datepickerrange start
	
	end: null, // datepickerrange end

	search: null, // search results
	
	infowindow: null,
	
	overlays: [],

	init: function(latitudes, longitudes) {
		var self = this;

		if( self.gmap === null ) {
			// Init marker Canvas
			self.markerCanvas = document.getElementById('markerCanvas');
			self.markerCanvasCtx = markerCanvas.getContext('2d');
			self.clusterImage = new Image();
			self.clusterImage.src = $SITE_PATH + 'img/cluster.png';

			// Calculate zoom
			var canvas = $('#map_canvas'),
				dimensions = { width: canvas.width(), height: canvas.height() },
				zoom = self.getBoundsZoomLevel( latitudes, longitudes, dimensions );

			self.gmap = new GMaps({
				div: '#map_canvas',
				lat: (latitudes[0] + latitudes[1]) / 2,
				lng: (longitudes[0] + longitudes[1]) / 2,
				zoom: zoom,
				click: function( ){
					if( typeof self.gmap.markers !== 'undefined' && self.gmap.markers.length > 0 )
					{
						self.gmap.hideInfoWindows();
					}
				},
				tilesloaded: function( ){
					self.updateMarkers(false);
				},
				mapTypeControlOptions: {
				  mapTypeIds : ["hybrid", "roadmap", "satellite", "terrain", "osm", "cloudmade"]
				},
			});
			
			self.gmap.addMapType("osm", {
				getTileUrl: function(coord, zoom) {
					return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
				},
				tileSize: new google.maps.Size(256, 256),
				name: "OpenStreetMap",
				maxZoom: 18
			});
			
			self.gmap.addMapType("cloudmade", {
				getTileUrl: function(coord, zoom) {
					return "http://b.tile.cloudmade.com/8ee2a50541944fb9bcedded5165f09d9/1/256/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
				},
				tileSize: new google.maps.Size(256, 256),
				name: "CloudMade",
				maxZoom: 18
			});
			
			self.addGeolocateButton( self.gmap ); // Add geolocate button
			/*
			self.gmap.addHeatmap({
				data: [
					new google.maps.LatLng(37.782551, -122.445368),
					new google.maps.LatLng(37.782745, -122.444586),
					new google.maps.LatLng(37.782842, -122.443688),
					new google.maps.LatLng(37.782919, -122.442815),
					new google.maps.LatLng(37.782992, -122.442112),
					new google.maps.LatLng(37.783100, -122.441461),
					new google.maps.LatLng(37.783206, -122.440829),
					new google.maps.LatLng(37.783273, -122.440324),
					new google.maps.LatLng(37.783316, -122.440023),
					new google.maps.LatLng(37.783357, -122.439794),
					new google.maps.LatLng(37.783371, -122.439687),
					new google.maps.LatLng(37.783368, -122.439666),
					new google.maps.LatLng(37.783383, -122.439594),
					new google.maps.LatLng(37.783508, -122.439525),
					new google.maps.LatLng(0.0, 0.0),
					new google.maps.LatLng(0.1, 0.1),
					new google.maps.LatLng(0.1, -0.1),
					new google.maps.LatLng(-0.1, 0.1),
				]
			});
			*/
			//self.gmap.setMapTypeId("osm");

			// Añadimos menú contextual en el mapa
			self.gmap.setContextMenu({
				control: 'map',
				options: [{
						title: 'Create report',
						name: 'add_marker',
						action: function(e) {
							window.location.href = $SITE_PATH + 'report/create?latitude=' + e.latLng.lat() + '&longitude=' + e.latLng.lng();
						}
					}]
			});
		}
	},
	
	setDate: function(start, end, callback) {
		var self = this;
		self.start = start;
		self.end = end;
		
		callback();
	},
	
	centerTo: function( latitude, longitude ) {
		var self = this;
		
		if( ! self.gmap ) return false;
		
		self.gmap.setCenter( latitude, longitude );
	},
	
	updateMarkers: function( clean ) {
		
		var self = this;
		
		if( ! self.gmap ) return false;

		if( typeof self.gmap.getBounds() !== "undefined" ){

			var templates = [-1];

			$.each($('#legend-content .checkbox :input'), function(i, template) {
				if ( $(template).prop('checked') )
				{
					templates.push($(template).val());
				}
  			});

			$.cookie("templates", JSON.stringify(templates));
			
			var bounds = self.gmap.getBounds(),
				southWest = bounds.getSouthWest(),
				northEast = bounds.getNorthEast();
			
			$.getJSON(
				$SITE_PATH+"report/search", 
				{
					minlatitude: southWest.lat(),
					maxlatitude: northEast.lat(),
					minlongitude: southWest.lng(),
					maxlongitude: northEast.lng(),
					mindate: self.start.format('X'),
					maxdate: self.end.format('X'),
					templates: templates
				}, 
				function(r)
				{
					if( r.status === "OK" ){
						//Borramos los markers antiguos
						self.removeMarkers();
						
						self.gridResolution = r.meta.grid;
						
						// Ponemos los markers
						$.each(r.data, function(i, report){
							self.setMarker(report);
						});
					}
				}
			);

		} else {
			console.log('Error loading markers');
		}
	},
	
	getMarkerById: function( id ) {
		
		var self = this,
			marker = null;
	
		if( typeof self.gmap.markers !== 'undefined' && self.gmap.markers.length > 0 ) {
			$.each( self.gmap.markers, function(k,v) {
				if( v.id === parseInt(id) )
				{
					marker = v;
					return false;
				}
				
				// Buscamos los markers que han sido ocultados 
				// debido a que sobrescribian posicion.
				$.each( v.appended, function(k2,v2) {
					if( v2 === parseInt(id) )
					{
						marker = v;
						return false;
					}
				});
			});
		}
		
		return marker;
	},
	
	removeMarkers: function() {
		var markerKeep = null;
		if (this.infowindow != null)
			markerKeep = this.infowindow.marker;
		
		for (var i = 0; i < this.overlays.length; i++)
		{
			if (this.overlays[i] != markerKeep)
				this.overlays[i].setMap(null);
		}
		this.overlays = [];
		
		if (markerKeep != null)
			this.overlays = [markerKeep];
	},
	
	getClusterMarkerIcon: function(num){
		//Limpiar y pintar imagen
		this.markerCanvasCtx.clearRect(0, 0, 50, 50);
		this.markerCanvasCtx.drawImage(this.clusterImage, 0, 0);
		
		
		this.markerCanvasCtx.font = 'bold 8pt Arial';
		this.markerCanvasCtx.fillStyle = 'white';
		this.markerCanvasCtx.strokeStyle = 'white';
		this.markerCanvasCtx.textAlign = 'center';

		this.markerCanvasCtx.fillText(num, 27, 31);
		return this.markerCanvas.toDataURL();
	},
	
	drawFromGeoJSON: function ( map, geojson )
	{
		if (geojson.type == 'Point')
		{
			var longitude = geojson.coordinates[0];
			var latitude = geojson.coordinates[1];

			// Añadimos marker
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(latitude,longitude),
				icon: $SITE_PATH + 'img/' + $MARKER_TYPES.report
			});
			marker.setMap(map);

			return marker;
		} 
		else if (geojson.type == 'LineString')
		{
			var line = [];
			for (var i = 0; i < geojson.coordinates.length; i++)
			{
				line.push(new google.maps.LatLng(geojson.coordinates[i][1], geojson.coordinates[i][0]));
			}
			var polyline = new google.maps.Polyline({
				path: line,
				geodesic: true,
				strokeColor: '#94999C',
				strokeOpacity: 1.0,
				strokeWeight: 2
			});
			polyline.setMap(map);

			return polyline;
		}
		else if (geojson.type == 'Polygon')
		{
			var line = [];
			for (var i = 0; i < geojson.coordinates[0].length; i++)
			{
				line.push(new google.maps.LatLng(geojson.coordinates[0][i][1], geojson.coordinates[0][i][0]));
			}
			
			var polyline = new google.maps.Polygon({
				path: line,
				geodesic: true,
				strokeColor: '#94999C',
				strokeOpacity: 1.0,
				strokeWeight: 2,
				fillColor: '#94999C',
				fillOpacity: 0.30
			});
			polyline.setMap(map);

			return polyline;
		}
		else if (geojson.type == 'MultiPolygon')
		{
			var polygon = [];
			for (var i = 0; i < geojson.coordinates.length; i++)
			{
				polygon = geojson.coordinates[i];
				
				var line = [];
				for (var j = 0; j < polygon[0].length; j++)
				{
					line.push(new google.maps.LatLng(polygon[0][j][1], polygon[0][j][0]));
				}
				
				var polyline = new google.maps.Polygon({
					path: line,
					geodesic: true,
					strokeColor: '#94999C',
					strokeOpacity: 1.0,
					strokeWeight: 2,
					fillColor: '#94999C',
					fillOpacity: 0.30
				});
				polyline.setMap(map);
			}

		}
	},
	
	zoomToGeoJSON: function ( map, geojson )
	{
		if (geojson.type == 'Point')
		{
			var minLat = geojson.coordinates[1] - 0.01;
			var maxLat = geojson.coordinates[1] + 0.01;
			var minLng = geojson.coordinates[0] - 0.01;
			var maxLng = geojson.coordinates[0] + 0.01;
		}
		else
		{
			if ( typeof geojson.bbox !== 'undefined' )
			{
				var minLat = geojson.bbox[1];
				var maxLat = geojson.bbox[3];
				var minLng = geojson.bbox[0];
				var maxLng = geojson.bbox[2];	
			}
			else
			{
				if (geojson.type == 'Polygon')
				{
					var mid = Math.round(geojson.coordinates[0].length/2);
					var minLat = geojson.coordinates[0][0][1];
					var maxLat = geojson.coordinates[0][mid][1];
					var minLng = geojson.coordinates[0][0][0];
					var maxLng = geojson.coordinates[0][mid][0];
				}
				else
				{
					var minLat = -30;
					var maxLat = 50;
					var minLng = -120;
					var maxLng = 120;
				}				
			}
		}
		map.fitBounds(
			new google.maps.LatLngBounds(
				new google.maps.LatLng(minLat, minLng),
				new google.maps.LatLng(maxLat, maxLng)));
	},
	
	updateInfoWindow : function ( start )
	{
		var self = this;
		
		//Cargar datos AJAX
		$.post($SITE_PATH + 'report/table', {
			mode:"geo",
			geo: self.infowindow.geo,
			draw: 1,
			columns: [{name: "address"}, {name: "title"}, {name: "created_at"}, {name: "comments"}],
			order: [{column: 2, dir: "desc"}],
			start: start,
			length: 5
		}).success(function(data) {
			var html = Handlebars.compile( $("#tpl-infowindow-list-reports").html() )({
				range: {
					start: data.first + 1,
					end: data.last + 1,
					total: data.recordsTotal,
					hasPrev: data.first > 1,
					hasNext: data.last < data.recordsTotal - 1
				},
				reports: data.data
			});
			self.infowindow.start = data.first;
			self.infowindow.setContent(html);
		});
	},
	
	openInfoWindow: function ( marker )
	{
		var self = this;
		
		var html = Handlebars.compile( $("#tpl-infowindow-list-reports").html() )({
			range: {
				start: 1,
				end: Math.min(5, marker.report.count),
				total: marker.report.count,
				hasPrev: false,
				hasNext: false
			},
			reports: []
		});
		
		if (self.infowindow)
			self.infowindow.setMap(null)
			
		self.infowindow = new google.maps.InfoWindow({
			content: html
		});
		self.infowindow.geo = marker.report.geo;
		self.infowindow.marker = marker;
		
		google.maps.event.addListener(self.infowindow, 'domready', function() {
			$('.insertPoint>div').hide().slideDown('fast')
			$('.btn-iw-page-next').click(function() {
				self.updateInfoWindow(self.infowindow.start + 5);
			});
			$('.btn-iw-page-prev').click(function() {
				self.updateInfoWindow(self.infowindow.start - 5);
			});
		});
		
		google.maps.event.addListener(self.infowindow, 'closeclick', function() {
			self.infowindow = null;
		});
		
		if (marker.report.geo.type == 'Point')
		{
			self.infowindow.open(self.gmap.map,marker);
		}
		else
		{
			//Calcula el centro de los puntos de la línea o polígono			
			if (marker.report.geo.type == 'Polygon')
				var coordinates = marker.report.geo.coordinates[0].slice(1);
			else
				var coordinates = marker.report.geo.coordinates;
			
			var lat = 0;
			var lng = 0;
			for (var i = 0; i < coordinates.length; i++)
			{
				lat += coordinates[i][1];
				lng += coordinates[i][0];
			}
			self.infowindow.setPosition(new google.maps.LatLng(
				lat/coordinates.length,
				lng/coordinates.length
			));
			self.infowindow.open(self.gmap.map);
		}
		
		self.updateInfoWindow(0);
	},
	
	setMarker: function( report ) {
		var self = this;
		
		if (report.type == 'cluster')
		{
			//Cluster de reports
			var lat = report.geo.coordinates[1];
			var lng = report.geo.coordinates[0];
			
			var count = report.count;
			
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(lat,lng),
				icon: this.getClusterMarkerIcon(count)
			});
			
			//Zoom para mostrar rectangulo de cuadricula
			google.maps.event.addListener(marker, 'click', function() {
				self.gmap.map.fitBounds(
					new google.maps.LatLngBounds(
						new google.maps.LatLng(lat-self.gridResolution/2, lng-self.gridResolution/2),
						new google.maps.LatLng(lat+self.gridResolution/2, lng+self.gridResolution/2)));
			});
			
			marker.setMap(self.gmap.map);
			self.overlays.push(marker);
		}
		else
		{
			//Punto con uno o más reports
			var marker = this.drawFromGeoJSON(self.gmap.map, report.geo);
			marker.report = report;
			
			if (report.geo.type == 'Point')
			{
				marker.icon = $SITE_PATH + 'map/marker?n=' + report.count;
				
				google.maps.event.addListener(marker, 'click', function() {
					//Evento para abrir infowindow con lista de reports
					self.openInfoWindow(marker);
				});
			}
			else
			{
				//Abrir en el centro del bbox si no es un punto
				google.maps.event.addListener(marker, 'click', function() {
					//Evento para abrir infowindow con lista de reports
					self.openInfoWindow(marker);
				});				
			}
			
			self.overlays.push(marker);
		}
		
		return false;
	},

	focusMarker: function(id) {
		var self = this;
		
		var marker = self.getMarkerById(id);
		
		if( marker !== null ) {
			// Cerramos todas las infowindow
			self.gmap.hideInfoWindows();
			// Centrar marker
			if( typeof marker.position.lat() !== 'undefined' && typeof marker.position.lng() !== 'undefined' ) { 
				self.gmap.setCenter( marker.position.lat(), marker.position.lng() );
			}
			// Zoomear
			self.gmap.setZoom(15);
			// Abrir infoWindow
			marker.infoWindow.open( self.gmap, marker );
		}
	},
			
	getBoundsZoomLevel: function(latitudes, longitudes, dimensions) {
		var WORLD_DIM = { height: 256, width: 256 };
		var ZOOM_MAX = 21;

		function latRad(lat) {
			var sin = Math.sin(lat * Math.PI / 180);
			var radX2 = Math.log((1 + sin) / (1 - sin)) / 2;
			return Math.max(Math.min(radX2, Math.PI), -Math.PI) / 2;
		}

		function zoom(mapPx, worldPx, fraction) {
			return Math.floor(Math.log(mapPx / worldPx / fraction) / Math.LN2);
		}

		var ne = new google.maps.LatLng(latitudes[1], longitudes[1]);//bounds.getNorthEast();
		var sw = new google.maps.LatLng(latitudes[0], longitudes[0]);//bounds.getSouthWest();

		var latFraction = (latRad(ne.lat()) - latRad(sw.lat())) / Math.PI;

		var lngDiff = ne.lng() - sw.lng();
		var lngFraction = ((lngDiff < 0) ? (lngDiff + 360) : lngDiff) / 360;

		var latZoom = zoom(dimensions.height, WORLD_DIM.height, latFraction) - 1;
		var lngZoom = zoom(dimensions.width, WORLD_DIM.width, lngFraction) - 1;

		return Math.min(latZoom, lngZoom, ZOOM_MAX);
	},
	
	/**
	 * Add the geolocate button to the map
	 * 
	 * @param {type} $map
	 */
	addGeolocateButton: function( $map )
	{
		$map.addControl({
			position: 'top_right',
			content: 'Geolocalizar',
			style: {
				color: '#565656',
				'fonr-family': 'Roboto, Arial, sans-serif',
				'font-size': '11px',
				background: '#fff',
				'background-clip': 'padding-box',
				margin: '5px',
				padding: '1px 6px',
				border: '1px solid rgba(0, 0, 0, 0.14902)',
				'-webkit-box-shadow': 'rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px',
				'box-shadow': 'rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px'
			},
			events: {
				click: function() {
					GMaps.geolocate({
						success: function(position) {
							$map.setCenter(position.coords.latitude, position.coords.longitude);
						},
						error: function(error) {
							alert('Geolocation failed: ' + error.message);
						},
						not_supported: function() {
							alert("Your browser does not support geolocation");
						}
					});
				}
			}
		});
	},

	/**
	 * Add the geolocate button to the map
	 * 
	 * @param {type} $map
	 */
	addLegend: function( map, templates )
	{
		var self = this;

	  	var list = "";
	  	var templates_cookies = null;
	  	
	  	if ($.cookie('templates') != undefined)
	  	{
	  		templates_cookies = JSON.parse($.cookie("templates"));
	  	}  	

	  	if (templates_cookies != null)
	  	{
	  		//Cookie
			for (var i = 0; i < templates.length; i++)
		  	{
		  		list += '<label class="checkbox inline"><input type="checkbox" '+ (($.inArray(""+templates[i].id, templates_cookies) != -1) ? 'checked' : '') +' data-id="'+ templates[i].id +'" value="'+ templates[i].id +'">'+ templates[i].title +'</label>'
		  	}
	  	}
	  	else
	  	{
	  		//Default
	  		for (var i = 0; i < templates.length; i++)
		  	{
		  		list += '<label class="checkbox inline"><input type="checkbox" '+((templates[i].visible) ? 'checked' : '') +' data-id="'+ templates[i].id +'" value="'+ templates[i].id +'">'+ templates[i].title +'</label>'
		  	}
	  	}
	  	
	  	
	  	var legendDiv = $('<div id="legend" class="box" style="width: 150px; margin: 5px;"></div>').get(0);
	  	var legendTitle = $('<div id="legend-title" class="box-header level-2"><h2><i class="icon-eye-open"></i><span class="break"></span>Leyenda</h2></div>').get(0);
	  	var legendContent = $('<div id="legend-content" class="box-content report-data-view" style="height: 150px; overflow-y: scroll;"><form class="report-content">'+ list +'</form></div>').get(0);

	  	legendDiv.appendChild(legendTitle);
	  	legendDiv.appendChild(legendContent);

	  	google.maps.event.addDomListener(legendTitle, 'click', function() {
	  		$('#legend-content').slideToggle();
	  	});

	  	google.maps.event.addDomListener(legendContent, 'click', function(e) {
	  		if ( e.target.type == 'checkbox' ){
	  			self.updateMarkers( true );
	  		}	  		
	  	});

	  	map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legendDiv);
	  },
};

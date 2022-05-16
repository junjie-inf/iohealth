CRMaps3D = function (holder, width, height) {
	//Private global variables/properties
	var scene = null;
	var renderer = null;
	var camera = null;
	var projection;
	var plane;
	var geometry;
	var path;
	var region;
	var maxValue = 0;
	var minValue = 0;
	var isTwoDimensions = false;	
	var mouse = new THREE.Vector2(), INTERSECTED; // To interact
	var projector, raycaster; // To interact
	var fToolTip;
	var data = [];
	var options = null;
	var geojosn = null;
	var measures = null;
	var FizzyText = null;
	
	
	// Constants	
	var width_3d = 300, height_3d = 300;
	
	init();
	
	function init() {
		// set some camera attributes
		var VIEW_ANGLE = 45, ASPECT = width / height, NEAR = 0.1, FAR = 10000;

		// create a WebGL renderer, camera, and a scene
		renderer = new THREE.WebGLRenderer({antialias:true, alpha: true});
		camera = new THREE.PerspectiveCamera(VIEW_ANGLE, ASPECT,
											  NEAR, FAR);
		scene = new THREE.Scene();

		// add and position the camera at a fixed position
		scene.add(camera);
		camera.position.z = 220;
		camera.position.x = 0;
		camera.position.y = 200;
		camera.lookAt( scene.position );

		// start the renderer, and black background
		renderer.setSize(width, height);
		//renderer.setClearColor(0x000);
		renderer.setClearColor( 0xffffff, 0.5); // background color and alpha 

		// add the render target to the page
		$('#' + holder).append("<div id='canvas-wraper' style='position: relative; margin: 0 auto;'></div>");
		$("#canvas-wraper").css({'width':width, 'height':height}).append(renderer.domElement).append("<div id='tooltip'></div>");
		$("#canvas-wraper").append("<div id='3d-fullscreen' class='button-toolbar' ></div>");
		$('#3d-fullscreen').click(function () {
		   var el = document.getElementById(holder);

		   if(el.webkitRequestFullScreen) {
			   el.webkitRequestFullScreen();
		   }
		  else {
			 el.mozRequestFullScreen();
		  }            
		});
		$(renderer.domElement).attr("id","canvas-map");

		controls = new THREE.OrbitControls( camera, renderer.domElement );

		controls.addEventListener( 'change', render );


		// add a light at a specific position
		var pointLight = new THREE.PointLight(0xFFFFFF);
		scene.add(pointLight);
		pointLight.position.x = 800;
		pointLight.position.y = 800;
		pointLight.position.z = 800;
	}
	
	function render() {
		//requestAnimationFrame( render );
		renderer.render( scene, camera );
	}
	
	function initOptionsPanel() {
		
		FizzyText = {
		  'Color gradient' : options.color_gradient,
		  'Color' : options.color,
		  Opacity : options.opacity,
		  'Max Height' : options.mxHeight,
		  background : {'Color': options.background_color, 'Opacity' : options.background_opacity},
		  tooltip : {'Enable': options.tooltip_enable, 'Property' : options.tooltip_property, 'Magnitude' : options.tooltip_magnitude, 'Magnitude2' : options.tooltip_magnitude2},
		  Controls : options.guiControls,
		  //this.explode = function() { ... },
		  // Define render logic ...
		};
	
		var gui = new dat.GUI();
		var fHM = gui.addFolder('Choropleth 3D');
		var gui_color_gradient = fHM.add(FizzyText, 'Color gradient', { 'Power': 0, 'Full Gradient': 1, 'Grayscale': 2, 'Heat (Fire)' : 3, 'Color-to-Black' : 4, 'Color-to-White' : 5 } );
		gui_color_gradient.onChange( function(value) { options.color_gradient = value; for (var i = 0 ; i < region.length ; i++) {region[i].material.color = colorRange((region[i].value-(isTwoDimensions? minValue [1] : minValue))/(isTwoDimensions? maxValue [1] : maxValue)); } render(); } );
		var gui_color = fHM.addColor(FizzyText, 'Color');
		gui_color.onChange( function(value) { options.color = value; for (var i = 0 ; i < region.length ; i++) {region[i].material.color = colorRange((region[i].value-(isTwoDimensions? minValue [1] : minValue))/(isTwoDimensions? maxValue [1] : maxValue)); } render(); });
		//fHM.add(FizzyText, 'explode');
		var gui_opacity = fHM.add(FizzyText, 'Opacity', 0, 1);
		gui_opacity.onChange( function(value) { options.opacity = value; for (var i = 0 ; i < region.length ; i++) {region[i].material.opacity = options.opacity; } render(); });
		var gui_mxHeight = fHM.add(FizzyText, 'Max Height', 0, 10).listen();
		gui_mxHeight.onChange( function(value) { if(value == 0) {value = 0.001;} options.mxHeight = value; for (var i = 0 ; i < region.length ; i++) {region[i].scale.z = value/5; region[i].position.setY(region[i].extrude*region[i].scale.z);} render();});
		fHM.open();		
		var fBackground = gui.addFolder('Background');
		var gui_backgroundColor = fBackground.addColor(FizzyText.background, 'Color');
		gui_backgroundColor.onChange( function(value) { options.background_color = value; renderer.setClearColor( options.background_color, options.background_opacity/*renderer.getClearAlpha*/); render(); });
		var gui_backgroundOpacity = fBackground.add(FizzyText.background, 'Opacity', 0, 1);
		gui_backgroundOpacity.onChange( function(value) { options.background_opacity = value; renderer.setClearColor( options.background_color, options.background_opacity); render(); });
		fBackground.open();
		fTooltip = gui.addFolder('Tooltip');
		var gui_enableTooltip = fTooltip.add(FizzyText.tooltip, 'Enable');
		gui_enableTooltip.onChange( function(value) { options.tooltip_enable = value; });
		var gui_magnToolTip = fTooltip.add(FizzyText.tooltip, 'Magnitude');
		gui_magnToolTip.onChange( function(value) { options.tooltip_magnitude = value; for (var i = 0 ; i < region.length ; i++) {region[i].magnitude = value; }});
		if(isTwoDimensions) {
			var gui_magnToolTip2 = fTooltip.add(FizzyText.tooltip, 'Magnitude2');
			gui_magnToolTip2.onChange( function(value) { options.tooltip_magnitude2 = value; for (var i = 0 ; i < region.length ; i++) {region[i].magnitude2 = value; }});
		}
		fTooltip.open();
		var gui_controls = gui.add(FizzyText, 'Controls');
		gui_controls.onChange( function(value) { options.guiControls = value; });
	}


	function plotMap() {
		render();
	}
	
	function loadTextureFromGeoJSON(callback) {

		d3.json(geojson, function(collection) {	 	 
			/* For Tooltip */
			var geoPropsAux = {};
			var geoProperties = Object.keys(collection.features[0].properties);
			for(var i = 0; i<geoProperties.length ;i++){geoPropsAux[geoProperties[i]]=i};
			FizzyTextAux = {
			  tooltip : {'Property' : geoPropsAux},
			};
			gui_propertyTooltip = fTooltip.add(FizzyTextAux.tooltip, 'Property', geoPropsAux);
			gui_propertyTooltip.onChange( function(value) { options.tooltip_property = value;});
			/* For Tooltip */
			
			loadTexture(collection); 
			if(callback != undefined) {
				callback(); //Función que se llama una vez procesado, probablemente para pintarlo
			}
  		});

		function loadTexture(json){
			projection = d3.geo.mercator();
	
			path = d3.geo.path().projection(projection);

			////////////// ADJUST IT TO THE CONTAINER  /////////////////
			projection.scale(1).translate([0, 0]);

			var b = path.bounds();
			if(json.type.toLowerCase() == 'featurecollection') {		
				b = path.bounds(json);
				if(!(isFinite(b[0][0]) || isFinite(b[0][1]) || isFinite(b[1][0]) || isFinite(b[1][1]))) {
					$.each(json.features, function(index, element){
						var baux = path.bounds(element);
						b[0][0] = Math.min(b[0][0],baux[0][0]); //left
						b[1][0] = Math.max(b[1][0],baux[1][0]); //right
						b[0][1] = Math.min(b[0][1],baux[0][1]); //bottom
						b[1][1] = Math.max(b[1][1],baux[1][1]); //up
					});
				}
			} else if(json.type.toLowerCase() == 'topology') {
				$.each(json.objects, function(index, element){
					var baux = path.bounds(topojson.feature(json, element));
					b[0][0] = Math.min(b[0][0],baux[0][0]); //left
					b[1][0] = Math.max(b[1][0],baux[1][0]); //right
					b[0][1] = Math.min(b[0][1],baux[0][1]); //bottom
					b[1][1] = Math.max(b[1][1],baux[1][1]); //up
				});
			}
	
			var s = .95 / Math.max((b[1][0] - b[0][0]) / width_3d, (b[1][1] - b[0][1]) / height_3d),
			t = [(width_3d - s * (b[1][0] + b[0][0])) / 2, (height_3d - s * (b[1][1] + b[0][1])) / 2];
			
			// Update the projection to use computed scale & translate.
			projection.scale(s).translate(t);

			////////////// ADJUST IT TO THE CONTAINER  /////////////////
	
			if(json.type.toLowerCase() == 'topology'){
				for (key in json.objects) {
					addGeoObject(topojson.feature(json, json.objects[key]));
				}
			} else if(json.type.toLowerCase() == 'featurecollection') {
				addGeoObject(json);
			}			
		}
	}
	
	// add the loaded gis object (in geojson format) to the map
	function addGeoObject(data) {
	
		  // keep track of rendered objects
		  var meshes = [];
		  region = [];

		 // convert to mesh and calculate values
		  if(data.features != null) {
			  for (var i = 0 ; i < data.features.length ; i++) {
				  var geoFeature = data.features[i]
				  if(geoFeature.geometry == null) {
				  	continue;
				  }
				  var feature = path(geoFeature);
				  // we only need to convert it to a three.js path
				  var mesh = transformSVGPathExposed(feature, width_3d, height_3d);
				  // add to array
				  meshes.push(mesh);
				
				  /////// VISUALIZE ////////
				  var maxValueAbs = isTwoDimensions?  Math.max(Math.abs(maxValue[0]),Math.abs(minValue[0])) : Math.max(Math.abs(maxValue),Math.abs(minValue));

			  	  var mathColor = colorRange(0);
				  if (maxValueAbs != 0) {
				  		mathColor = colorRange(isTwoDimensions? (measures[1][i]-minValue[1])/(maxValue[1]-minValue[1]) : (measures[i]-minValue)/(maxValue-minValue));
				  }
				  	
				  var material = new THREE.MeshLambertMaterial({
					  color: mathColor,
					  opacity: options.opacity
				  });
				  
				  var extrude = 0;
				  if (maxValueAbs != 0) {
				  	extrude = (isTwoDimensions? measures[0][i]/maxValueAbs : measures[i]/maxValueAbs)*50.0; //height
				  }
				  			  				  
				  for(var j = 0; j < mesh.length ; j++) {
					  var shape3d = mesh[j].extrude({amount: extrude, bevelEnabled: false});

					  // create a mesh based on material and extruded shape
					  var toAdd = new THREE.Mesh(shape3d, material);
			  
					  // rotate and position the elements nicely in the center
					  toAdd.translateY(extrude);
					  toAdd.rotation.x = Math.PI/2;
					  //toAdd.translateX(-302);
					  //toAdd.translateZ(-350);
					  
	
					  toAdd.name = geoFeature.properties;
				      toAdd.value = isTwoDimensions? measures[1][i] : measures[i]; //colour
				      if(isTwoDimensions) {
				      	toAdd.value2 = measures[0][i] //height
				      }
				      toAdd.magnitude = options.tooltip_magnitude;
				      toAdd.magnitude2 = options.tooltip_magnitude2;

					  // If some scale (mxHeight) is set
					  toAdd.scale.z = options.mxHeight/5;
					  toAdd.position.setY(extrude*toAdd.scale.z);

					  // Store it
					  toAdd.extrude = extrude; //height
					  region.push(toAdd);
					  // add to scene
					  scene.add(toAdd);
				  }
				  /////// VISUALIZE ////////
		  }
	  }
	}
	
	function colorRange(value) {
		var color = null;
		if (options.color_gradient == 0) { //green to yellow to red {
			var hue = (1-(value))*0.4;
			//var color= "hsl(" + hue + ", 100%, 50%)";
			color = new THREE.Color().setHSL(hue,1,.5);
		} else if (options.color_gradient == 1) { //blue to green to yellow to red
			if(value<=0.3333) {
				r = 0.0;
				g = (value/0.3333);
				b = 1.0;
			} else if(value<=+0.6666) {
				r = (+value/0.3333)-1.0;
				g = 1.0
				r = (-value/0.3333)+1.0;
			} else {
				r = 1.0;
				g = (value/0.3333)-2.0;
				b= 0.0;
			}
			color = new THREE.Color().setRGB(r, g, b);
		} else if (options.color_gradient == 2) { ////grayscale
			var gray = 1-value;
			r = gray;
			g = gray;
			b = gray;
			color = new THREE.Color().setRGB(r, g, b);
		} else if (options.color_gradient == 3) { // fire
			r = 0.5+0.6*Math.cos(2.0106194*value+-1.5079645);
			g = 0.5+1.0*Math.cos(4.5867257*value+2.576106);
			b = 0.5+0.82*Math.cos(3.015929*value+2.3247786);
			color = new THREE.Color().setRGB(r, g, b);
		} else if (options.color_gradient == 4) { // monochromatic scale. Color to black
			/*r = value;
			g = 0;
			b = 0;
			color = new THREE.Color().setRGB(r, g, b);*/
			r = parseInt(options.color.substr(1,2), 16); // Grab the hex representation of red (chars 1-2) and convert to decimal (base 10).
			g = parseInt(options.color.substr(3,2), 16);
			b = parseInt(options.color.substr(5,2), 16);
			var hue = rgbToHsl(r, g, b)[0];
			color = new THREE.Color().setHSL(hue,value,0.5);
		} else if (options.color_gradient == 5) { // monochromatic scale. Color to white
			r = parseInt(options.color.substr(1,2), 16); // Grab the hex representation of red (chars 1-2) and convert to decimal (base 10).
			g = parseInt(options.color.substr(3,2), 16);
			b = parseInt(options.color.substr(5,2), 16);
			var hue = rgbToHsl(r, g, b)[0];
			color = new THREE.Color().setHSL(hue,1,1.2-value);
		}
		// color = 'rgb(' + Math.round((r+1)/2*255) + ',' + Math.round((g+1)/2*255) + ',' + Math.round((b+1)/2*255) + ')';
		return color;
	}
	
	function onCanvasMouseMove( event ) { // To interact
		
		if(!options.tooltip_enable) {return;}

		event.preventDefault();

		mouse.x = ( (event.pageX - $('canvas').offset().left) / $('canvas').width() ) * 2 - 1;
		mouse.y = - ( (event.pageY - $('canvas').offset().top) / $('canvas').height() ) * 2 + 1;
				
		// find intersections

		var vector = new THREE.Vector3( mouse.x, mouse.y, 1 );
		projector.unprojectVector( vector, camera );

		raycaster.set( camera.position, vector.sub( camera.position ).normalize() );

		var intersects = raycaster.intersectObjects( scene.children );

		if ( intersects.length > 0 ) {
			$('#tooltip').css({'left': (event.pageX - $('canvas').offset().left) - $('#tooltip').outerWidth()/2, 
				'top': (event.pageY - $('canvas').offset().top) - $('#tooltip').outerHeight() - 13}).show();
			if ( INTERSECTED != intersects[ 0 ].object ) {

				if ( INTERSECTED ) INTERSECTED.material.emissive.setHSL( INTERSECTED.currentHSL.h, INTERSECTED.currentHSL.s, INTERSECTED.currentHSL.l );

				INTERSECTED = intersects[ 0 ].object;
				INTERSECTED.currentHSL = INTERSECTED.material.emissive.getHSL();
				var colorhsl = INTERSECTED.material.color.getHSL();
				INTERSECTED.material.emissive.setHSL(colorhsl.h, colorhsl.s-0.3, colorhsl.l-0.2);
							
				$('#tooltip').html('<div id="tooltip-title"' + (isTwoDimensions? 'style="margin-bottom: 0px"' : '') + '>' + INTERSECTED.name[Object.keys(INTERSECTED.name)[options.tooltip_property]] + '</div><div id="tooltip-value"' + (isTwoDimensions? 'style="font-size:13px;"' : '') + '>' + INTERSECTED.value.toFixed(2) + ' ' + INTERSECTED.magnitude + 
				( isTwoDimensions? '<br/>' + INTERSECTED.value2.toFixed(2) + ' ' + INTERSECTED.magnitude2 : '' ) + '</div>');
			}

		} else {

			if ( INTERSECTED ) INTERSECTED.material.emissive.setHSL( INTERSECTED.currentHSL.h, INTERSECTED.currentHSL.s,INTERSECTED.currentHSL.l );

			INTERSECTED = null;
			$('#tooltip').hide();

		}
		
		renderer.render( scene, camera );


	}
	
	function onCanvasMouseLeave( event ) { // To interact
		event.preventDefault();

		$('#tooltip').hide();
	}
	
	function rgbToHsl(r, g, b){
		r /= 255, g /= 255, b /= 255;
		var max = Math.max(r, g, b), min = Math.min(r, g, b);
		var h, s, l = (max + min) / 2;

		if(max == min){
			h = s = 0; // achromatic
		}else{
			var d = max - min;
			s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
			switch(max){
				case r: h = (g - b) / d + (g < b ? 6 : 0); break;
				case g: h = (b - r) / d + 2; break;
				case b: h = (r - g) / d + 4; break;
			}
			h /= 6;
		}

		return [h, s, l];
	}
	
	this.choropleth3D = function(originalData, map, opt) {
	
		//Inicializamos las variables globales geojson, las opciones y data
		measures = originalData;
		isTwoDimensions = (measures[1].constructor === Array);
		maxValue = isTwoDimensions? [Math.max.apply(null,originalData[0]),Math.max.apply(null,originalData[1])] : Math.max.apply(null,originalData);
		minValue = isTwoDimensions? [Math.min.apply(null,originalData[0]),Math.min.apply(null,originalData[1])] : Math.min.apply(null,originalData);
		geojson = map; 
		var default_args = {
			color_gradient: 0,
			color: '#FF0000',
			opacity: 1.0,
			mxHeight: 2, //scale, 5 means no scale 1:1 and 10 means double
			background_color: '#FFFFFF',
			background_opacity: 0.5,
			tooltip_enable: true,
			tooltip_property: 0,
			tooltip_magnitude: '%',
			tooltip_magnitude2: '%',
			guiControls: false,
		}
		if(typeof opt == "undefined") {
			options = default_args;
		} else {
			for(var index in default_args) {
				if(typeof opt[index] == "undefined") opt[index] = default_args[index];
			}
			options = opt;
		}
		//update scene with options about background
		renderer.setClearColor( options.background_color, options.background_opacity);
		// To interact
		renderer.domElement.addEventListener( 'mousemove', onCanvasMouseMove, false ); 
		renderer.domElement.addEventListener( 'mouseleave', onCanvasMouseLeave, false ); 
		projector = new THREE.Projector();
		raycaster = new THREE.Raycaster();
		
		initOptionsPanel();
		loadTextureFromGeoJSON(function (){
			plotMap();

			// FIX-ME: oculto el panel de opciones hasta que se almacene la info y se controle
			$('.dg.ac').remove();
		});
	}
	
	this.getOptions = function() {
		return options;
	}
	
	this.to2D = function () {
		FizzyText['Max Height'] -= 0.1;
		plotMap();
		requestAnimationFrame( to2D );
	}
}

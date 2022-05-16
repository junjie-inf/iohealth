
/*function getValue(x,y) {
    var value = 0;
    var distanceq = 0;
    for (var i = 0; i<measures.length; i++) {
    	distanceq = (Math.pow((x-measures[i].x),2) + Math.pow((y-measures[i].y),2))
    	if(distanceq<efradiusq) {
		    value += (measures[i].m)*Math.exp(-distanceq/(coef_dist));
		}
    }
	return value;
}*/


CRMaps = function (holder, width, height) {
	//Private global variables/properties
	var scene = null;
	var renderer = null;
	var camera = null;
	var projection;
	var plane;
	var geometry;
	var data = [];
	var coef_dist = null;
	var efradius = null; //  full width at tenth of maximum (FWTM) for a Gaussian
	var efradiusq = null; // radio efectivo de acción al cuadrado
	var lambertShader = THREE.ShaderLib['lambert'];
	var uniforms = THREE.UniformsUtils.clone(lambertShader.uniforms);
	var options = null;
	var geojosn = null;
	var measures = null;
		
		
	var attributes = {
	  displacement: {
		type: 'f', // a float
		value: [] // an empty array
	  },
	  /*map: {
		type: 'f', // a float
		value: [] // an empty array
	  }*/
	};
	
	// Constants	
	var width_3d = 300, height_3d = 300;
	var vertexshader = 		
		[

			"#define LAMBERT",

			"varying vec3 vLightFront;",

			"#ifdef DOUBLE_SIDED",

			"	varying vec3 vLightBack;",

			"#endif",

			THREE.ShaderChunk[ "map_pars_vertex" ],
			THREE.ShaderChunk[ "lightmap_pars_vertex" ],
			THREE.ShaderChunk[ "envmap_pars_vertex" ],
			THREE.ShaderChunk[ "lights_lambert_pars_vertex" ],
			THREE.ShaderChunk[ "color_pars_vertex" ],
			THREE.ShaderChunk[ "morphtarget_pars_vertex" ],
			THREE.ShaderChunk[ "skinning_pars_vertex" ],
			THREE.ShaderChunk[ "shadowmap_pars_vertex" ],
			THREE.ShaderChunk[ "logdepthbuf_pars_vertex" ],

			"attribute float displacement;",
			"varying float z;",
			"uniform float mxHeight;",
			//"attribute float map;",
			//"varying float trans;",

			"void main() {",

				THREE.ShaderChunk[ "map_vertex" ],
				THREE.ShaderChunk[ "lightmap_vertex" ],
				THREE.ShaderChunk[ "color_vertex" ],

				THREE.ShaderChunk[ "morphnormal_vertex" ],
				THREE.ShaderChunk[ "skinbase_vertex" ],
				THREE.ShaderChunk[ "skinnormal_vertex" ],
				THREE.ShaderChunk[ "defaultnormal_vertex" ],

				THREE.ShaderChunk[ "morphtarget_vertex" ],
				THREE.ShaderChunk[ "skinning_vertex" ],
				
				// Sustitution of THREE.ShaderChunk[ "default_vertex" ]
				/*****/
				
				"vec4 mvPosition;",
				
				"vec3 newPosition = position + normal * vec3(displacement*mxHeight);",
				
				"#ifdef USE_SKINNING",
				"	mvPosition = modelViewMatrix * skinned;",
				"#endif",
				
				"#if !defined( USE_SKINNING ) && defined( USE_MORPHTARGETS )",
				"	mvPosition = modelViewMatrix * vec4( morphed, 1.0 );",
				"#endif",
				
				"#if !defined( USE_SKINNING ) && ! defined( USE_MORPHTARGETS )",
				"	mvPosition = modelViewMatrix * vec4( newPosition, 1.0 );",
				"#endif",

				"z = displacement;",
				//"trans = map;",

				"gl_Position = projectionMatrix * mvPosition;",
				
				/*****/
				
				THREE.ShaderChunk[ "logdepthbuf_vertex" ],

				THREE.ShaderChunk[ "worldpos_vertex" ],
				THREE.ShaderChunk[ "envmap_vertex" ],
				THREE.ShaderChunk[ "lights_lambert_vertex" ],
				THREE.ShaderChunk[ "shadowmap_vertex" ],

			"}"

		].join("\n");
		
		var fragmentshader = 		
				[

					"uniform float opacity;",

					"varying vec3 vLightFront;",
					"varying float z;",
					"uniform int gradient;",
					//"varying float trans;",

					"#ifdef DOUBLE_SIDED",

					"	varying vec3 vLightBack;",

					"#endif",

					THREE.ShaderChunk[ "color_pars_fragment" ],
					THREE.ShaderChunk[ "map_pars_fragment" ],
					THREE.ShaderChunk[ "alphamap_pars_fragment" ],
					THREE.ShaderChunk[ "lightmap_pars_fragment" ],
					THREE.ShaderChunk[ "envmap_pars_fragment" ],
					THREE.ShaderChunk[ "fog_pars_fragment" ],
					THREE.ShaderChunk[ "shadowmap_pars_fragment" ],
					THREE.ShaderChunk[ "specularmap_pars_fragment" ],
					THREE.ShaderChunk[ "logdepthbuf_pars_fragment" ],

					"void main() {",

						"if(gradient == 0) {", //ice to fire (negative)
							"gl_FragColor = vec4(0.5+0.6*cos(2.0106194*z+-1.5079645),0.5+1.0*cos(4.5867257*z+2.576106),0.5+0.82*cos(3.015929*z+2.3247786),opacity);", //trans);",
						"} else if (gradient == 1) {", //green to yellow to red (negative)
							"if(z<=0.0) {",
								"gl_FragColor = vec4(z+1.0,1.0,0,opacity);", //trans);"
							"} else {",
								"gl_FragColor = vec4(1.0,1.0-z,0,opacity);", //trans);"
							"}",
						"} else if (gradient == 2) {", //blue to green to yellow to red (negative)
							"if(z<=-0.3333) {",
								"gl_FragColor = vec4(0,(z/0.6667)+1.5,1.0,opacity);", //trans);"
							"} else if(z<=+0.3333) {",
								"gl_FragColor = vec4((+z/0.6667)+0.5,1.0,(-z/0.6667)+0.5,opacity);", //trans);"
							"} else {",
								"gl_FragColor = vec4(1.0,(-z/0.6667)+1.5,0,opacity);", //trans);"
							"}",
						"} else if (gradient == 3) {", //grayscale (negative)
							"float gray = (-z*0.5)+0.5;",
							"gl_FragColor = vec4(gray,gray,gray,opacity);", //trans);"
						"}",
										
						THREE.ShaderChunk[ "logdepthbuf_fragment" ],
						THREE.ShaderChunk[ "map_fragment" ],
						THREE.ShaderChunk[ "alphamap_fragment" ],
						THREE.ShaderChunk[ "alphatest_fragment" ],
						THREE.ShaderChunk[ "specularmap_fragment" ],

					/*"	#ifdef DOUBLE_SIDED",

							//"float isFront = float( gl_FrontFacing );",
							//"gl_FragColor.xyz *= isFront * vLightFront + ( 1.0 - isFront ) * vLightBack;",

					"		if ( gl_FrontFacing )",
					"			gl_FragColor.xyz *= vLightFront;",
					"		else",
					"			gl_FragColor.xyz *= vLightBack;",

					"	#else",

					"		gl_FragColor.xyz *= vLightFront;",

					"	#endif",*/

						THREE.ShaderChunk[ "lightmap_fragment" ],
						THREE.ShaderChunk[ "color_fragment" ],
						THREE.ShaderChunk[ "envmap_fragment" ],
						THREE.ShaderChunk[ "shadowmap_fragment" ],

						THREE.ShaderChunk[ "linear_to_gamma_fragment" ],

						THREE.ShaderChunk[ "fog_fragment" ],

					"}"

				].join("\n");

	
	var xsegments = width_3d;
	var ysegments = height_3d;
	var nVerticesx = xsegments+1;
	var nVerticesy = ysegments+1;
	
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
		  Style : options.wire? 'wire':'solid',
		  'Color gradient' : options.color_gradient,
		  Radius : options.coef,
		  Precision : Math.round(((xsegments)/width_3d)*10),
		  Opacity : options.opacity,
		  'Max Height' : options.mxHeight/10.0,
		  background : {'Color': options.background_color, 'Opacity' : options.background_opacity},
		  boundaries : {'Width' : options.boundaries_width},
		  Controls : options.guiControls,
		  //this.explode = function() { ... },
		  // Define render logic ...
		};
	
		var gui = new dat.GUI({ autoPlace: false });

		var rootFolder = gui.addFolder('Display Options');

		var fHM = rootFolder.addFolder('HeatMap 3D');
		var gui_style = fHM.add(FizzyText, 'Style', [ 'Solid', 'Wire', 'Solid+Wire' ] );
		gui_style.onChange( function(value) { value == "Wire"? options.wire = true : options.wire = false; plotMapD(); } );
		var gui_color = fHM.add(FizzyText, 'Color gradient', { 'Heat (Fire-Ice)' : 0, 'Power': 1, 'Full Gradient': 2, 'Grayscale': 3 } );
		gui_color.onChange( function(value) { options.color_gradient = value; plotMapD(); } );
		var gui_radius = fHM.add(FizzyText, 'Radius', 0, 10);
		gui_radius.onChange( function(value) { options.coef = value; calculateEfRadius(); plotMap(); } );
		var gui_precision = fHM.add(FizzyText, 'Precision', 0, 10).step(1);
		gui_precision.onChange( function(value) { xsegments = Math.round(width_3d*(value/10)); ysegments = Math.round(height_3d*(value/10)); nVerticesx = xsegments+1;
			nVerticesy = ysegments+1; plotMap();} );
		//fHM.add(FizzyText, 'explode');
		var gui_opacity = fHM.add(FizzyText, 'Opacity', 0, 1);
		gui_opacity.onChange( function(value) { options.opacity = value; plotMapD();});
		var gui_mxHeight = fHM.add(FizzyText, 'Max Height', 0, 10);
		gui_mxHeight.onChange( function(value) { options.mxHeight = value*10.0; plotMapD();});
		fHM.open();		
		var fBackground = rootFolder.addFolder('Background');
		var gui_backgroundColor = fBackground.addColor(FizzyText.background, 'Color');
		gui_backgroundColor.onChange( function(value) { options.background_color = value; renderer.setClearColor( options.background_color, options.background_opacity/*renderer.getClearAlpha*/); render(); });
		var gui_backgroundOpacity = fBackground.add(FizzyText.background, 'Opacity', 0, 1);
		gui_backgroundOpacity.onChange( function(value) { options.background_opacity = value; renderer.setClearColor( options.background_color, options.background_opacity); render(); });
		fBackground.open();
		var fBoundaries = rootFolder.addFolder('Boundaries');
		var gui_boundariesWidth = fBoundaries.add(FizzyText.boundaries, 'Width', 0, 10);
		gui_boundariesWidth.onChange( function(value) {options.boundaries_width = value; loadTextureFromGeoJSON(function() {plotMapD();}); });
		fBoundaries.open();
		var gui_controls = rootFolder.add(FizzyText, 'Controls');
		gui_controls.onChange( function(value) { options.guiControls = value; });

		$('#map_menu_container').prepend(gui.domElement);
	}

	
	function plotMap() {
		updateDisplacement();
		plotMapD();
	}

	function plotMapD() {
		uniforms['opacity'].value = options.opacity;
		uniforms['mxHeight'] = {type: 'f', value: options.mxHeight};
		uniforms['gradient'] = {type: 'i', value: options.color_gradient};
		var shaderMaterial = new THREE.ShaderMaterial({
			uniforms: uniforms,
			attributes:     attributes,
			vertexShader:    vertexshader,
			fragmentShader:  fragmentshader,
			wireframe: options.wire,
			transparent: true,
			lights: true,
			side: THREE.DoubleSide,
			defines: {
				USE_ALPHAMAP: true
			}
		});

		if (plane) 
		{
			scene.remove( plane );
		}
		plane = new THREE.Mesh( geometry, shaderMaterial );

		plane.rotation.x = -Math.PI/2;
		scene.add( plane );

		render();
	}
	
	function updateDisplacement () {

		xValue = (width_3d/(xsegments));
		yValue = (height_3d/(ysegments));
	
		geometry = new THREE.PlaneGeometry(width_3d, height_3d, xsegments, ysegments);

		//****** DISPLACEMENT ******//
		attributes.displacement.value = [];

		values =
		  attributes.displacement.value;
	
		var max = -Infinity;
		var min = Infinity;
		
		for(var y = 0; y < nVerticesy; y++) {
			for(var x = 0; x < nVerticesx; x++){
				var value = getValue(x*xValue,y*yValue);
				values.push(value);
				if(value<min) {
					min = value;
				}
				if(value>max) {
					max = value;
				}
			}
		}
	
		var maxA = Math.max(Math.abs(max),Math.abs(min));

		// iterate over all pixels
		if(maxA != 0) {
			for(var i = 0; i<values.length; i++) {
				values[i] = (values[i])/maxA; //normalize between 0 and 1
			}
		}
	
		/*var range = Math.abs(max-min);

		// iterate over all pixels
		for(var i = 0; i<values.length; i++) {
			values[i] = (values[i]-min)/range; //normalize between 0 and 1
		}*/
	
		//values[1500] = 70;
	
		//****** DISPLACEMENT ******//
	}
	
	function getValue(xp,yp) {
		var value = 0;
		var distanceq = 0;

		var r = Math.floor(efradius);
		var x0 = Math.max(Math.round(xp)-r,0);
		var y0 = Math.max(Math.round(yp)-r,0);
		for (var y = y0; y<=(yp + r) && y <= height_3d; y++) {
			var dy =  Math.pow((yp-y),2);
			for (var x = x0; x<=(xp + r) && x <= width_3d; x++) {
				if(data[y*(width_3d+1)+x] != 0) {
					distanceq = (Math.pow((xp-x),2) + dy)
					if(distanceq<efradiusq) {
						value += (data[y*(width_3d+1)+x])*Math.exp(-distanceq/(coef_dist));
					}
				}
			}
		}
		return value;
	}
	
	function calculateEfRadius () {
		coef_dist = 2*Math.pow(options.coef,2);
		efradius = 4.29193*options.coef; //  full width at tenth of maximum (FWTM) for a Gaussian
		efradiusq = Math.pow(efradius,2)// radio efectivo de acción al cuadrado
	}
	
	function loadTextureFromGeoJSON(callback) {
	
		var canvas2=document.createElement("canvas");
		canvas2.width=width_3d;
		canvas2.height=height_3d;
		var context2=canvas2.getContext("2d");

		d3.json(geojson, function(collection) {
	 
			//var roadsTest = [collection];
			loadTexture(collection);
			if(callback != undefined) {
				callback(); //Función que se llama una vez procesado, probablemente para pintarlo
			}
		});

		//uniforms['alphaMap'].value = THREE.ImageUtils.loadTexture("spain.png", new THREE.UVMapping(), render);

		///////////////////////////////////////

		function loadTexture(json){
			projection = d3.geo.mercator();
	
			var path = d3.geo.path().projection(projection).context(context2);

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
				//draw topojson
				d3.select("#drop").remove();
				path(topojson.mesh(json));
				context2.strokeStyle='#FFFFFF';
				context2.lineWidth=1;
				context2.stroke();
			} else 
			if(json.type.toLowerCase() == 'featurecollection'){
				//draw geojson
				/*d3.select("#drop").remove(); // Para un solo path
				path({type: "FeatureCollection", features: json.features});
				context2.strokeStyle="#111";
				context2.lineWidth=1;
				context2.fillStyle = getRandomColor();
				context2.fill();
				context2.stroke();*/
		
				// Loop over the features…  // Múltiples paths
				for (var i = 0; i < json.features.length; i++) {
					path(json.features[i]);
					context2.strokeStyle= options.boundaries_width == 0? '#FFFFFF' : '#000000' ;
					context2.lineWidth = options.boundaries_width;
					context2.stroke();
					context2.fillStyle = '#FFFFFF';
					context2.fill();
					context2.beginPath();
				}
			} else {
				//document.getElementById("map_canvas").innerHTML = "This file does not seem to be topojson or geojson.";
			}
	
			// Create texture for alphaMap
			var texture = new THREE.Texture(canvas2);
			texture.needsUpdate = true;
			uniforms['alphaMap'].value = texture;
	
			//Create attributes for custom and high density alphaMap
			/*var imgData = context2.getImageData(0,0,width_3d,height_3d).data;
			attributes.map.value = [];

			var valuesMap =
			  attributes.map.value;
	
			var imgDataL = imgData.length/4;

			for(var i = 0; i < imgDataL; i++) {
				valuesMap.push(imgData[i*4+4]);
			}*/
		}
	}
	
	function preProcessing() {
			var dataL = nVerticesx*nVerticesy;
			for(var i = 0; i < dataL; i++) {
				data[i] = 0;
			}
			for (var i = 0; i < measures.length; i++) {
				var xy = projection([measures[i].lon,measures[i].lat]);
				data[Math.round(xy[1])*nVerticesx+Math.round(xy[0])] += measures[i].m;
			}
	}
	
	
	
	this.dasymetric3D = function(originalData, map, opt) {
	
		//Inicializamos las variables globales geojson, las opciones y data
		measures = originalData;
		geojson = map; 
		var default_args = {
			wire: false,
			color_gradient: 0,
			coef: 5, //for radius
			precision: 10.0,
			opacity: 1.0,
			mxHeight: 50.0,
			background_color: '#FFFFFF',
			background_opacity: 0.5,
			boundaries_enable: true,
			boundaries_width: 2.0,
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
		
		
		calculateEfRadius();
		initOptionsPanel();
		loadTextureFromGeoJSON(function (){
			preProcessing();
			plotMap();

			// FIX-ME: oculto el panel de opciones hasta que se almacene la info y se controle
			$('.dg.main').remove();
		});
	}
	
	this.getOptions = function() {
		return options;
	}
}

	

/* 

/*** probar con light a ver que pasa en el shader, el light principal, parametro = true igual que en el wireframe***/
/*1. Optimización
2. 40K puntos. y con latitud y longitud
el mínimo no tiene por qué ser 0, aunque tal y como está siempre cogerá 0, hy que hacerlo sobre el array antes no una vez haya pasado
*/
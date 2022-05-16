// 1. Inicializar con r = Raphael("holder", width_holder, height_holder);

// 2. drawDonut (value, R, width, backcolor, fillcolor)
// Pinta el donut con el valor porcentual del parámetro
// value: valor en porcentaje
// options: opciones
// - R: radio del donut
// - width: ancho del donut
// - backcolor: color de fondo del donut
// - fillcolor: color para el relleno del donut con el valor

// 3. updateDonut(value)
// Actualiza el valor del donut
// value: valor en porcentaje

//EJEMPLO
window.onload = function () {
	var r = Raphael("holder-donutParam", 400, 400);
	r.drawDonutParam(90, {R: 40, width: 14, backcolor: "#7BC2E5", fillcolor: "#777777"});
}

//EJEMPLO 2 - Actualizar una gráfica
/*
	r.updateDonutParam(25);
*/


// *******************************************************


Raphael.fn.updateDonutParam = function (value) {
	//the animated arc
	this.donut.path.animate({
		arc: [100, 100, value, 100, this.donut.R]
	}, Math.abs(value - this.donut.value)*25, function() {
		//when the animation is done unbind
		eve.unbind("raphael.anim.frame.*", onAnimate);
	});
	this.donut.value = value;

	//event fired on each animation frame
	eve.on("raphael.anim.frame.*", onAnimate);

	var handler = this;
	//on each animation frame we change the text in the middle
	function onAnimate() {
		var howMuch = handler.donut.path.attr("arc");
		handler.donut.text.attr("text", Math.floor(howMuch[2]) + "%");
	}	
}

Raphael.fn.drawDonutParam = function (value, options) {
	var default_args = {
		'R'	:	40,
		'width'	:	14,
		'backcolor'	: "#7BC2E5",
		'fillcolor' : "#f5f5f5"
	}		
	for(var index in default_args) {
		if(typeof options[index] == "undefined") options[index] = default_args[index];
	}

	this.donut = {
		value: value,
		path: null,
		text: null,
		R: null
	};

	this.donut.R = options.R;

	//// Custom Attribute
	this.customAttributes.arc = function(xloc, yloc, value, total, R) {
		var alpha = 360 / total * value,
			a = (90 - alpha) * Math.PI / 180,
			x = xloc + R * Math.cos(a),
			y = yloc - R * Math.sin(a),
			path;
		if (total == value) {
			path = [
				["M", xloc, yloc - R],
				["A", R, R, 0, 1, 1, xloc - 0.01, yloc - R]
				];
		} else {
			path = [
				["M", xloc, yloc - R],
				["A", R, R, 0, +(alpha > 180), 1, x, y]
				];
		}
		return {
			path: path
		};
	};

	var backCircle = this.circle(100, 100, options.R).attr({
		"stroke": options.backcolor,
		"stroke-width": options.width
	});

	this.donut.path = this.path().attr({
		"stroke": options.fillcolor,
		"stroke-width": options.width,
		arc: [100, 100, 0, 100, options.R]
	});

	//text in the middle
	this.donut.text = this.text(100, 100, "0%").attr({
		"font-size": 18,
		"fill": options.fillcolor,
		"font-weight": "bold"
	});

	//event fired on each animation frame
	eve.on("raphael.anim.frame.*", onAnimate);

	var handler = this;
	//the animated arc
	this.donut.path.rotate(180, 100, 100).animate({
		arc: [100, 100, value, 100, options.R]
	}, value*25, function() {
		//when the animation is done unbind
		eve.unbind("raphael.anim.frame.*", onAnimate);
	});


	//on each animation frame we change the text in the middle
	function onAnimate() {
		var howMuch = handler.donut.path.attr("arc");
		handler.donut.text.attr("text", Math.floor(howMuch[2]) + "%");
	}		
}
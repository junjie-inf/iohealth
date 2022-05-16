// 1. Inicializar con r = Raphael("holder", width_holder, height_holder);

// 2. drawDonut (data, R, options)
// Pinta el donut con el valor porcentual de cada sector
// data: matriz de datos compuestos por una matriz que contiene valores (NO tienen porque ser en valor porcentual, ya que se calculará), descripción y 
// color a usar para el sector. EJ: [[25,"Lion","#00FF04"],[20,"Elephant","#FF0000"],[50,"Giraffe","#0066FD"],[5,"Cocrodile","#FF8500"]] 
// options: opciones
// - R: radio del donut
// - width: ancho del donut
// - title: titulo para mostrar en los popup

// 3. updateDonut(data)
// Actualiza el valor del donut con los nuevos datos
// data: matriz de datos compuestos por una matriz que contiene valores (NO tienen porque ser en valor porcentual, ya que se calculará), descripción y 
// color a usar para el sector. EJ: [[25,"Lion","#00FF04"],[20,"Elephant","#FF0000"],[50,"Giraffe","#0066FD"],[5,"Cocrodile","#FF8500"]] 


//EJEMPLO
$(document).ready(function(){
	// Plot donut
	var r = Raphael("holder-donut", 600, 600);
	r.drawDonut([[25,"Lion","#00FF04"],[20,"Elephant","#FF0000"],[50,"Giraffe","#0066FD"],[5,"Cocrodile","#FF8500"]],{R: 100, width: 50, title: "Animal Poblation"});

	// Update data
	r.updateDonut([[60,"Lion","#00FF04"],[20,"Elephant","#FF0000"],[0,"Giraffe","#0066FD"],[20,"Cocrodile","#FF8500"]]);	

	// Plot new donut
	/*
	r.remove()
	r = Raphael("holder", 600, 600);
	r.drawDonut([[60,"Wolf","#00FF04"],[20,"Tiger","#FF0000"],[0,"Spider","#0066FD"],[20,"Bird","#FF8500"]],{R: 100, width: 50, title: "Animal Poblation"});
	*/
});


// *******************************************************


Raphael.fn.updateDonut = function (data) {
	//Calculate total
	var total = 0;
	for (var i = 0; i < data.length; i++) {
		total += data[i][0];
	}	
	
	//Add donut sections
	var valuei = 0;
	for (var i = 0; i < data.length; i++) {
		var valuef = data[i][0] + valuei, string = data[i][1], color = data[i][2];
		this.donut.sector[i].path = this.donut.sector[i].path.animate({arc2: [valuei, valuef, total, this.donut.R], stroke: color}, 400, "<>");
		valuei = valuef;
		
		this.addPopupPath(this.donut.sector[i].path, color, string, (data[i][0]/total)*100);
	}
}

Raphael.fn.drawDonut = function (data, options) {
	var default_args = {
		'R'	:	100,
		'width'	:	50,
		'title'	: "",
	}
	for(var index in default_args) {
		if(typeof options[index] == "undefined") options[index] = default_args[index];
	}
	
	this.donut = {
		is_label_visible: false,
		leave_timer: null,
		sector: [], // {'path': null}
		R: null,
		frame: null,
		label: null,
		title: null
	};
	
	this.donut.R = options.R;
	this.donut.title = options.title;
	
	//// Custom Attribute
	this.customAttributes.arc2 = function (valuei, valuef, total, R) {
		var alphai = 360 / total * valuei,
			ai = (90 - alphai) * Math.PI / 180,
			xi = 300 + R * Math.cos(ai),
			yi = 300 - R * Math.sin(ai);
		var alpha = 360 / total * valuef,
			a = (90 - alpha) * Math.PI / 180,
			x = 300 + R * Math.cos(a),
			y = 300 - R * Math.sin(a),
			path;
		if (total == valuef) {
			path = [["M", xi, yi], ["A", R, R, 0, +((alpha - alphai) > 180), 1, 299.99, 300 - R]];
		} else {
			path = [["M", xi, yi], ["A", R, R, 0, +((alpha - alphai) > 180), 1, x, y]];
		}
		return {path: path};
	};
	
	
	//Calculate total
	var total = 0;
	for (var i = 0; i < data.length; i++) {
		total += data[i][0];
	}	
	
	//Add popup
	var txt = {font: '12px Helvetica, Arial', fill: "#fff"},
	txt1 = {font: '12px Helvetica, Arial', fill: "#fff"};
	var $holder = $('#holder-donut');

	this.donut.label = this.set();
	this.donut.label.push(this.text(60, 12, "24 hits").attr(txt));
	this.donut.label.push(this.text(60, 27, "22 September 2008").attr(txt1).attr({fill: color}));
	this.donut.label.hide();
	this.donut.frame = this.popup(100, 100, this.donut.label, "right").attr({fill: "#000", stroke: "#666", "stroke-width": 2, "fill-opacity": .7}).hide();
	
	//Add donut sections
	var valuei = 0;
	for (var i = 0; i < data.length; i++) {
		var valuef = data[i][0] + valuei, string = data[i][1], color = data[i][2];
		var path = this.path().attr({stroke: color, "stroke-width": options.width}).attr({arc2: [0, 0, total, options.R]});
		path.animate({arc2: [valuei, valuef, total, options.R]}, 900, "bounce");
		valuei = valuef;
		
		this.donut.sector.push({'path': path});
		this.addPopupPath(path, color, string, (data[i][0]/total)*100);
	}
	
	var handler = this;
	handler.donut.frame.mousemove(function (event) {
		clearTimeout(handler.donut.leave_timer);
		x = event.pageX - $(document).scrollLeft() - $holder.offset().left;
		y = event.pageY - $(document).scrollTop() - $holder.offset().top;
		var side = "right";
		if (x + handler.donut.frame.getBBox().width > $holder.width() - 5) {
			side = "left";
		}
		var ppp = handler.popup(x, y, handler.donut.label, side, 1),
			anim = Raphael.animation({
				path: ppp.path,
				transform: ["t", ppp.dx + 5, ppp.dy]
			}, 0 * handler.donut.is_label_visible);
		lx = handler.donut.label[0].transform()[0][1] + ppp.dx + 5;
		ly = handler.donut.label[0].transform()[0][2] + ppp.dy;
		handler.donut.frame.show().stop().animate(anim);
		handler.donut.label[0].show().stop().animateWith(handler.donut.frame, anim, {transform: ["t", lx, ly]}, 0 * handler.donut.is_label_visible);
		handler.donut.label[1].show().stop().animateWith(handler.donut.frame, anim, {transform: ["t", lx, ly]}, 0 * handler.donut.is_label_visible);
		handler.donut.is_label_visible = true;
	});
	handler.donut.frame.mouseout (function () {
		handler.donut.leave_timer = setTimeout(function () {
			handler.donut.frame.hide();
			handler.donut.label[0].hide();
			handler.donut.label[1].hide();
			for (var p = 0; p < handler.donut.sector.length; p++) {
				handler.donut.sector[p].path.attr({'stroke-opacity': 1});
			}
			handler.donut.is_label_visible = false;
		}, 1);
	});
	
	this.donut.frame.toFront();
	this.donut.label[0].toFront();
	this.donut.label[1].toFront();
}

// Popup
Raphael.fn.addPopupPath = function (path, color, string, percentage) {
	var handler = this;
	var timer;
	var $holder = $('#holder-donut');
	path.unmousemove();
	path.mousemove(function (event) {
			clearTimeout(handler.donut.leave_timer);
			for (var p = 0; p < handler.donut.sector.length; p++) {
				handler.donut.sector[p].path.attr({'stroke-opacity': 1});
			}
			path.attr({'stroke-opacity': 0.7});
			handler.donut.label[1].attr({fill: color});
			x = event.pageX - $(document).scrollLeft() - $holder.offset().left;
			y = event.pageY - $(document).scrollTop() - $holder.offset().top;
			var side = "right";
			if (x + handler.donut.frame.getBBox().width > $holder.width() - 5) {
				side = "left";
			}
			var ppp = handler.popup(x, y, handler.donut.label, side, 1),
				anim = Raphael.animation({
					path: ppp.path,
					transform: ["t", ppp.dx + 5, ppp.dy]
				}, 0 * handler.donut.is_label_visible);
			lx = handler.donut.label[0].transform()[0][1] + ppp.dx + 5;
			ly = handler.donut.label[0].transform()[0][2] + ppp.dy;
			handler.donut.frame.show().stop().animate(anim);

			handler.donut.label[0].attr({text: (handler.donut.title? handler.donut.title + ": ": "") + percentage + " %"}).show().stop().animateWith(handler.donut.frame, anim, {transform: ["t", lx, ly]}, 0 * handler.donut.is_label_visible);
			handler.donut.label[1].attr({text: string}).show().stop().animateWith(handler.donut.frame, anim, {transform: ["t", lx, ly]}, 0 * handler.donut.is_label_visible);
			handler.donut.is_label_visible = true;
	});
	path.unmouseout(); 
	path.mouseout (function () {
		handler.donut.leave_timer = setTimeout(function () {
			handler.donut.frame.hide();
			handler.donut.label[0].hide();
			handler.donut.label[1].hide();
			path.attr({'stroke-opacity': 1});
			handler.donut.is_label_visible = false;
		}, 1);
	});	
}	

// TO-DO Legends
/*this.circle(10, 410, 6).attr({fill: "blue", stroke: "none"});
this.text(30, 410, "Hola").attr({fill: "#fff", "text-anchor": "start"})*/

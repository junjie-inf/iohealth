// 1. Inicializar con r = Raphael("holder", width_holder, height_holder);

// 2. newGrid (labels)
// Si no existe, crea un grid con la escala de las X.
// Si existe cambia la escala del eje de las X  y borra las gráficas existentes (para los cambios de fecha).
// labels: matriz con las fechas en formato "01/01/14 00:00:00" o "01/01/14 00:00:00 +01:00"

// 3. addPlot(data, magnitude, id, options)
// Pinta la nueva gráfica y ajusta escala y las gráficas existentes en caso necesario
// data: matriz con los valores de la gráfica (valores en eje y)
// magnitude: cadena de texto que marca las unidades de las gráficas
// id: cadena de texto que sirve como identificador de la gráfica, para borrarla más tarde mediante su id
// options: opciones 
// - color: color que se quiere para la gráfica, en caso de no especificarse, se selecciona uno internamente
// - smooth: suaviza la gráfica
// - dash: puntear la línea (opciones: [“”, “-”, “.”, “-.”, “-..”, “. ”, “- ”, “--”, “- .”, “--.”, “--..”])
// La función devuelve el color asignado

// 4. removePlotById(id)
// Borra la gráfica por Id
// Ajusta la escala y el resto de gráficas si fuese necesario
// id: id de la gráfica a borrar

// 5. updateGrid(labels)
// Actualiza las etiquetas del eje de la X

// 6. updatePlotById(id, data)
// Actualiza los datos de una gráfica
// id: id de la gráfica a actualizar
// data: datos de la gráfica a actualizar

//EJEMPLO
$(document).ready(function(){
	labels = ["01/01/14 00:00:00", "02/01/14 00:00:00", "03/01/14 00:00:00", "04/01/14 00:00:00"]
	r = Raphael("holder-chart", 930, 330);
	r.newGrid(labels);
	var data = [0,3,4,5];
	var data = [10, 7, 2, 10];
	r.addPlot(data, "Wh", 'Id1',{color: "red"})

	var data = [2, 3, 1, 0];
	r.addPlot(data, "Wh", 'Id2', {color: "#fff", smooth: "false", dash: "-"})

	var data = [0, 5, 10, 3];
	r.addPlot(data, "Wh", 'Id3', {color: "green", smooth: "true"})

	r.removePlotById('Id1');
});

//EJEMPLO 2 - Actualizar una gráfica
/*
	labels = ["05/01/14 00:00:00", "06/01/14 00:00:00", "07/01/14 00:00:00", "08/01/14 00:00:00"]
	r.updateGrid(labels)
	var data = [3, 2, 1, 9];
	r.updatePlotById('Id3', data)
*/


// *******************************************************


Raphael.fn.drawGrid = function (x, y, w, h, wv, hv, color, minValy, maxValy, magnitude) {
    color = color || "#000";
    var path = ["M",Math.round(x),Math.round((this.chart.bottomgutter + 1))," l 0 ", Math.round(this.chart.height - this.chart.topgutter - this.chart.bottomgutter + 1),"z",
    			"M",Math.round(w),Math.round((this.chart.bottomgutter + 1))," l 0 ", Math.round(this.chart.height - this.chart.topgutter - this.chart.bottomgutter + 1),"z"],
        rowHeight = h / hv,
        columnWidth = w / wv;
    for (var i = 0; i <= hv; i++) {
		path = path.concat(["M", Math.round(x), Math.round(y + i * rowHeight) + .5, "H", Math.round(w)]);
    	if(minValy !== undefined && maxValy !== undefined) {
			if(i==0) {
				this.text(Math.round(x) - 10, Math.round(y + i * rowHeight) + .5, maxValy.toFixed(1)).attr(this.chart.txtAxis).toBack();
			} else if (i==hv) {
				this.text(Math.round(x) - 10, Math.round(y + i * rowHeight) + .5, minValy.toFixed(1)).attr(this.chart.txtAxis).toBack();
			} else {
				this.text(Math.round(x) - 10, Math.round(y + i * rowHeight) + .5, ((hv-i)*(maxValy-minValy)/hv).toFixed(1)).attr(this.chart.txtAxis).toBack();
			}
		}
    }
    for (i = 1; i < wv; i++) {
        path = path.concat(["M", Math.round(x + i * columnWidth) + .5, Math.round(y) + .5, "V", Math.round(y + h) + .5]);
    }
    if(magnitude !== undefined) {
	    this.text(Math.round(x) - 50, Math.round(y + (hv/2) * rowHeight) + .5, magnitude).rotate(-90).attr(this.chart.txtMagnitude).toBack();
    }
    return this.path(path.join(",")).attr({stroke: color});
};

Raphael.fn.newGrid = function (labels) {
	this.chart = {
		graph: [],
		grid: {path: null, pathdates:null, textx: [], texty: [], legendy: null},
		xaxis: [],
		leftgutter: 90,
		bottomgutter: 20,
		topgutter: 20,
		gridColor: "#525151",
		txt: {font: '12px Helvetica, Arial', fill: "#fff"},
		txt1: {font: '10px Helvetica, Arial', fill: "#fff"},
		txtAxis: {font: '12px Helvetica, Arial', fill: "#fff", 'dominant-baseline': "central", 'text-anchor' : "end"},
		txtMagnitude: {font: 'bold 14px Helvetica, Arial', fill: "#fff", 'dominant-baseline': "central", 'text-anchor' : "middle"},
	}
	this.chart.width = (this.width - this.chart.leftgutter);
	this.chart.height = (this.height - (this.chart.topgutter + this.chart.bottomgutter)*2);
	
	this.chart.xaxis = labels;
	//Remove current graphs
    for(var i= 0; i < this.chart.graph.length ; i++) {
    	this.chart.graph[i].path.remove();
    	for (var j=0; j < this.chart.graph[i].circles.length; j++) {
    		this.chart.graph[i].circles[j].remove();
    	}
    	this.chart.graph[i].circles.lenght = 0;
    	this.chart.graph[i].circles = this.set();
    }
    this.chart.graph.length = 0;
    this.chart.graph = []; 
    
	if(!this.chart.grid.path) { //if Grid does NOT exits
		this.chart.grid.path = this.drawGrid(this.chart.leftgutter, this.chart.topgutter + .5, this.chart.width, this.chart.height - this.chart.topgutter - this.chart.bottomgutter, 0, 5, this.chart.gridColor);
		this.newGridScaleX(labels);
	} else {
		this.newGridScaleX(labels);
	}
}

Raphael.fn.newGridScaleY = function (x, y, h, hv, color, minValy, maxValy, magnitude) {
    color = color || "#000";
    var rowHeight = h / hv;
    //Remove current scale
    if(magnitude !== undefined) {
		if(this.chart.grid.legendy) {
			this.chart.grid.legendy.remove();	
			this.chart.grid.legendy = null;
		}
	}
    for(var i= 0; i < this.chart.grid.texty.length ; i++) {
    	this.chart.grid.texty[i].remove();
    }
    this.chart.grid.texty.length = 0;
    this.chart.grid.texty = [];
    
    //Draw current scale
    for (var i = 0; i <= hv; i++) {
    	if(minValy !== undefined && maxValy !== undefined) {
			if(i==0) {
				this.chart.grid.texty.push(this.text(Math.round(x) - 10, Math.round(y + i * rowHeight) + .5, maxValy.toFixed(1)).attr(this.chart.txtAxis).toBack());
			} else if (i==hv) {
				this.chart.grid.texty.push(this.text(Math.round(x) - 10, Math.round(y + i * rowHeight) + .5, minValy.toFixed(1)).attr(this.chart.txtAxis).toBack());
			} else {
				this.chart.grid.texty.push(this.text(Math.round(x) - 10, Math.round(y + i * rowHeight) + .5, ((hv-i)*(maxValy-minValy)/hv).toFixed(1)).attr(this.chart.txtAxis).toBack());
			}
		}
    }
    if(magnitude !== undefined) {
	    this.chart.grid.legendy = this.text(Math.round(x) - 50, Math.round(y + (hv/2) * rowHeight) + .5, magnitude).rotate(-90).attr(this.chart.txtMagnitude).toBack();
    }
};

Raphael.fn.newGridScaleX = function (labels) {
	//Remove current scale
    for(var i= 0; i < this.chart.grid.textx.length ; i++) {
    	this.chart.grid.textx[i].remove();
    }
    this.chart.grid.textx.length = 0;
    this.chart.grid.textx = []; 
    
    if(this.chart.grid.pathdates) {
		this.chart.grid.pathdates.remove();
		this.chart.grid.pathdates = null;
	}
	    
    // D raw current scale 
    var date1 = labels[0].split(' ');
	date1 = date1[0].split('/');
	date1 = new Date (date1[2], date1[1]-1, date1[0]);
	var date2 = labels[labels.length-1].split(' ');
	date2 = date2[0].split('/');
	date2 = new Date (date2[2], date2[1]-1, date2[0]);
	var timeDiff = Math.abs(date2.getTime() - date1.getTime());
	var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
	
	var months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

	X = (this.chart.width - this.chart.leftgutter) / (labels.length -1);
	
	var vline = [];
	var limitDays = 2,
	limitDaysMonth = 60;
	for (var i = 0, ii = labels.length; i < ii; i++) {
		
		var x = Math.round(this.chart.leftgutter + X * (i));
			
		var hour = labels[i].split(" ")[1].split(":")[0];
		if(diffDays < limitDays) {		
			if(hour != "00") {
				this.chart.grid.textx.push(this.text(x, this.chart.height - 6, (hour < 10 ? houthis.substring(1) : hour)).attr(this.chart.txtAxis).toBack());
			} else {
				this.chart.grid.textx.push(this.text(x, this.chart.height - 6, labels[i].substring(0,8)).rotate(-75).attr(this.chart.txtAxis).toBack());
				var vpath = "M " + x + " " + (this.chart.bottomgutter + 1) + " l 0 " + (this.chart.height - this.chart.topgutter - this.chart.bottomgutter + 1) + " z";
				vline = vline.concat(vpath);
			}
		} else if (diffDays >= limitDays && diffDays <= limitDaysMonth){
			if(hour == "00") {
				this.chart.grid.textx.push(t = this.text(x, this.chart.height - 6, labels[i].substring(0,8)).rotate(-75).attr(this.chart.txtAxis).toBack());
				var vpath = "M " + x + " " + (this.chart.bottomgutter + 1) + " l 0 " + (this.chart.height - this.chart.topgutter - this.chart.bottomgutter + 1) + " z";
				vline = vline.concat(vpath);
			}
		} else {
			var day = labels[i].split(" ")[0].split("/")[0];
			if(!(hour != "00" || day != "01")) {
				var month = labels[i].split(" ")[0].split("/")[1];
				var year = labels[i].split(" ")[0].split("/")[2];
				this.chart.grid.textx.push(this.text(x, this.chart.height - 6, months[month-1] + " " + (yeathis.length > 2? yeathis.substring(2) : year)).rotate(-75).attr(this.chart.txtAxis).toBack());
				var vpath = "M " + x + " " + (this.chart.bottomgutter + 1) + " l 0 " + (this.chart.height - this.chart.topgutter - this.chart.bottomgutter + 1) + " z";
				vline = vline.concat(vpath);
			}
		}
	}
	this.chart.grid.pathdates = this.path(vline).attr({stroke: this.chart.gridColor}).toBack();
       
};
	

function getAnchors(p1x, p1y, p2x, p2y, p3x, p3y) {
        var l1 = (p2x - p1x) / 2,
            l2 = (p3x - p2x) / 2,
            a = Math.atan((p2x - p1x) / Math.abs(p2y - p1y)),
            b = Math.atan((p3x - p2x) / Math.abs(p2y - p3y));
        a = p1y < p2y ? Math.PI - a : a;
        b = p3y < p2y ? Math.PI - b : b;
        var alpha = Math.PI / 2 - ((a + b) % (Math.PI * 2)) / 2,
            dx1 = l1 * Math.sin(alpha + a),
            dy1 = l1 * Math.cos(alpha + a),
            dx2 = l2 * Math.sin(alpha + b),
            dy2 = l2 * Math.cos(alpha + b);
        return {
            x1: p2x - dx1,
            y1: p2y + dy1,
            x2: p2x + dx2,
            y2: p2y + dy2
        };
    }


Raphael.fn.addPlot = function (data,magnitude,id,options) {

	var colorhue = ((this.chart.graph.length+1)/10) || Math.random();
	var	colordefault = "hsl(" + [colorhue, .5, .5] + ")";
	var default_args = {
		'color'	:	colordefault,
		'smooth'	:	false,
		'dash'	: ""
	}
	for(var index in default_args) {
		if(typeof options[index] == "undefined") options[index] = default_args[index];
	}
	
	var max = this.chart.grid.texty[0] ? parseInt(this.chart.grid.texty[0].attr('text')) : 0;
	var max_newPlot = Math.max.apply(Math, data); 
    if(max_newPlot == 0 && max ==0) {max_newPlot = 5;}
	if(max_newPlot > max){ // Check the new max
    	this.adjustScaleY(max_newPlot, magnitude);
    	//draw new plot    	
    	this.plot(data,magnitude,id,options.color,options.smooth,options.dash);
	} else {
		this.plot(data,magnitude,id,options.color,options.smooth,options.dash);
	}
	
	// Order elements so you can see the popups in the front
	for(var i = this.chart.graph.length-1 ; i >= 0 ; i--) {
		this.chart.graph[i].path.toBack();
	}
	this.chart.grid.path.toBack();
	this.chart.grid.pathdates.toBack();
	
	return options.color;
}

Raphael.fn.plot = function (data,magnitude,id,color,smooth,dash) {
	//Add plot
	var max = this.chart.grid.texty[0] ? parseInt(this.chart.grid.texty[0].attr('text')) : 0;
	Y = (this.chart.height - this.chart.bottomgutter - this.chart.topgutter) / max;
	var path = this.path().attr({stroke: color, "stroke-width": 4, "stroke-linejoin": "round", 'stroke-dasharray': dash}),
	bgp = this.path().attr({stroke: "none", opacity: .3, fill: color}),
	label = this.set(),
	lx = 0, ly = 0,
	blanket = this.set();

	label.push(this.text(60, 12, "24 hits").attr(this.chart.txt));
	label.push(this.text(60, 27, "22 September 2008").attr(this.chart.txt1).attr({fill: color}));
	label.hide();
	var frame = this.popup(100, 100, label, "right").attr({fill: "#000", stroke: "#666", "stroke-width": 2, "fill-opacity": .7}).hide();

	this.chart.graph.push({'circles': this.set(), 'path': path, 'data': data, 'id': id, 'magnitude': magnitude, 'color': color, 'smooth': smooth, 'dash': dash});

	var p = [], bgpp =[], pprevious =[]; // pprevious is for animate
	for (var i = 0, ii = data.length; i < ii; i++) {
		if(data[i] != null) { //Do not plot the point/circle if value is null
			var y = Math.round(this.chart.height - this.chart.bottomgutter - Y * data[i]),
				x = Math.round(this.chart.leftgutter + X * (i));
					
			if (!i || (data[i] != null && !p.length)) {
				if (smooth) {
					p = ["M", x, y, "C", x, y];
				} else {
					p = ["M", x, y, "L"];
				}
				bgpp = ["M", this.chart.leftgutter, this.chart.height - this.chart.bottomgutter, "L", x, y, "C", x, y];
				pprevious = ["M", x, Math.round(this.chart.height - this.chart.bottomgutter + .5) + .5];
			} else if (i && i < ii - 1) {
				var Y0 = Math.round(this.chart.height - this.chart.bottomgutter - Y * data[i - 1]),
					X0 = Math.round(this.chart.leftgutter + X * (i - 1)),
					Y2 = Math.round(this.chart.height - this.chart.bottomgutter - Y * data[i + 1]),
					X2 = Math.round(this.chart.leftgutter + X * (i + 1));
				var a = getAnchors(X0, Y0, x, y, X2, Y2);
				if(smooth) {
					p = p.concat([a.x1, a.y1, x, y, a.x2, a.y2]);
				} else {
					p = p.concat([x, y, "L"]);
				}
				bgpp = bgpp.concat([a.x1, a.y1, x, y, a.x2, a.y2]);
				pprevious = pprevious.concat(["L", x, Math.round(this.chart.height - this.chart.bottomgutter + .5) + .5,]);
			}
			//var dot = this.circle(x, y, 4).attr({fill: "#333", stroke: color, "stroke-width": 2});
			var dot = this.circle(x, y, 6).attr({fill: "#fff", stroke: "none", opacity: 0});
			this.chart.graph[this.chart.graph.length-1].circles.push(dot);
			blanket.push(dot);
			this.addPopupDot(x, y, data[i], this.chart.xaxis[i], dot, frame, label, magnitude);	
		}
	}
	p = p.concat([x, y, x, y]);
	bgpp = bgpp.concat([x, y, x, y, "L", x, this.chart.height - this.chart.bottomgutter, "z"]);
	pprevious = pprevious.concat(["L", x, Math.round(this.chart.height - this.chart.bottomgutter + .5) + .5,]);
	
	//path.attr({path: ["M", Math.round(this.chart.leftgutter), Math.round(this.chart.height - this.chart.bottomgutter + .5) + .5, "H", Math.round(this.chart.width)]});
	path.attr({path: pprevious});
	path.animate({'path': p},300);
	//bgp.attr({path: bgpp});
	frame.toFront();
	label[0].toFront();
	label[1].toFront();
	blanket.toFront();
}

Raphael.fn.addPopupDot = function (x, y, data, lbl, dot, frame, label, magnitude) {
	var timer, i = 0, leave_timer, is_label_visible = false;
	
	var handler = this;
	dot.hover(function () {
		clearTimeout(leave_timer);
		var side = "right";
		if (x + frame.getBBox().width > handler.chart.width) {
			side = "left";
		}
		var ppp = handler.popup(x, y, label, side, 1),
			anim = Raphael.animation({
				path: ppp.path,
				transform: ["t", ppp.dx, ppp.dy]
			}, 200 * is_label_visible);
		lx = label[0].transform()[0][1] + ppp.dx;
		ly = label[0].transform()[0][2] + ppp.dy;
		frame.show().stop().animate(anim);
		label[0].attr({text: data + " " + magnitude}).show().stop().animateWith(frame, anim, {transform: ["t", lx, ly]}, 200 * is_label_visible);
		label[1].attr({text: lbl}).show().stop().animateWith(frame, anim, {transform: ["t", lx, ly]}, 200 * is_label_visible);
		dot.attr("r", 6);
		is_label_visible = true;
	}, function () {
		dot.attr("r", 4);
		leave_timer = setTimeout(function () {
			frame.hide();
			label[0].hide();
			label[1].hide();
			is_label_visible = false;
		}, 1);
	});
}

Raphael.fn.removePlotById = function (id) {
	var max_plots = 0;
	var indexToRemove = null; 
	for(var i = 0 ; i < this.chart.graph.length ; i++) {
		if(this.chart.graph[i].id == id) {
			this.chart.graph[i].circles.remove();
			this.chart.graph[i].path.remove();
			this.chart.graph[i].data = [];
			this.chart.graph[i].data.length = 0;
			indexToRemove = i;
		}
		max_plots = max_plots > Math.max.apply(Math, this.chart.graph[i].data) ? max_plots : Math.max.apply(Math, this.chart.graph[i].data);
	}
	if(indexToRemove != null) {
		this.chart.graph.splice(indexToRemove, 1);
	}
	
	//Ajustar las gráficas que quedan
	var max = this.chart.grid.texty[0] ? parseInt(this.chart.grid.texty[0].attr('text')) : 0;
    if(max_plots < max && this.chart.graph.length > 0){ // Check the new max
		this.adjustScaleY(max_plots);	
	}
	
	//Eliminar el máximo si no hay más gráficas
	if(this.chart.graph.length == 0) {
		for(var i = 0; i < this.chart.grid.texty.length; i++) {
			this.chart.grid.texty[i].remove();
		}
		this.chart.grid.texty.length = 0;
		this.chart.grid.texty = [];
	}
}

Raphael.fn.adjustScaleY = function (newMax, magnitude) {
	//new scale
	this.newGridScaleY(this.chart.leftgutter, this.chart.topgutter + .5, this.chart.height - this.chart.topgutter - this.chart.bottomgutter, 5, this.chart.gridColor, 0, newMax, magnitude);

	//change existing plots scale
	Y = (this.chart.height - this.chart.bottomgutter - this.chart.topgutter) / newMax;
	
	for(var i= 0; i < this.chart.graph.length ; i++) {
		var color2 = this.chart.graph[i].color;
		var label2 = this.set();
		label2.push(this.text(60, 12, "24 hits").attr(this.chart.txt));
		label2.push(this.text(60, 27, "22 September 2008").attr(this.chart.txt1).attr({fill: color2}));
		label2.hide();
		var frame = this.popup(100, 100, label2, "right").attr({fill: "#000", stroke: "#666", "stroke-width": 2, "fill-opacity": .7}).hide();
		

		
		// Path & circles
		var p = [], bgpp = [];
		for (var j = 0, ic = 0, ii = this.chart.graph[i].data.length; j < ii; j++) {
			if(this.chart.graph[i].data[j] != null) { // Consider the circles just if there is data associated		
				var newy = Math.round(this.chart.height - this.chart.bottomgutter - Y * this.chart.graph[i].data[j]),
					x = Math.round(this.chart.leftgutter + X * (j));
			
				//Circles
				this.chart.graph[i].circles[ic].unhover();
				this.chart.graph[i].circles[ic].animate({'cy':newy},300);
				this.addPopupDot(this.chart.graph[i].circles[ic].attr('cx'), newy, this.chart.graph[i].data[j], this.chart.xaxis[j], this.chart.graph[i].circles[ic], frame, label2, this.chart.graph[i].magnitude);	

				//Path			
				if (!j || (this.chart.graph[i].data[j] != null && !p.length)) {
					if (this.chart.graph[i].smooth) {
						p = ["M", x, newy, "C", x, newy];
					} else {
						p = ["M", x, newy, "L"];
					}
					bgpp = ["M", this.chart.leftgutter, this.chart.height - this.chart.bottomgutter, "L", x, newy, "C", x, newy];
				}
				if (j && j < ii - 1) {
					var Y0 = Math.round(this.chart.height - this.chart.bottomgutter - Y * this.chart.graph[i].data[j - 1]),
						X0 = Math.round(this.chart.leftgutter + X * (j - 1)),
						Y2 = Math.round(this.chart.height - this.chart.bottomgutter - Y * this.chart.graph[i].data[j + 1]),
						X2 = Math.round(this.chart.leftgutter + X * (j + 1));
					var a = getAnchors(X0, Y0, x, newy, X2, Y2);
					if(this.chart.graph[i].smooth) {
						p = p.concat([a.x1, a.y1, x, newy, a.x2, a.y2]);
					} else {
						p = p.concat([x, newy, "L"]);
					}
					bgpp = bgpp.concat([a.x1, a.y1, x, newy, a.x2, a.y2]);
				}
				ic++;
			}
		}
		p = p.concat([x, newy, x, newy]);
		bgpp = bgpp.concat([x, newy, x, newy, "L", x, this.chart.height - this.chart.bottomgutter, "z"]);
		this.chart.graph[i].path.animate({'path': p},300);
		//bgp.attr({path: bgpp});
		frame.toFront();
		label2[0].toFront();
		label2[1].toFront();
		this.chart.graph[i].circles.toFront(); 
	}    		
}


Raphael.fn.updateGrid = function (labels) {
	this.chart.xaxis = labels;
    this.newGridScaleX(labels);
}

Raphael.fn.updatePlotById = function (id, data) {
	//change data of existing plot
	var max = this.chart.grid.texty[0] ? parseInt(this.chart.grid.texty[0].attr('text')) : 0;
	Y = (this.chart.height - this.chart.bottomgutter - this.chart.topgutter) / max;
	
	for(var i= 0; i < this.chart.graph.length ; i++) {
		if(this.chart.graph[i].id == id) {
			this.chart.graph[i].data = data;
			var color2 = this.chart.graph[i].color;
			var label2 = this.set();
			label2.push(this.text(60, 12, "24 hits").attr(this.chart.txt));
			label2.push(this.text(60, 27, "22 September 2008").attr(this.chart.txt1).attr({fill: color2}));
			label2.hide();
			var frame = this.popup(100, 100, label2, "right").attr({fill: "#000", stroke: "#666", "stroke-width": 2, "fill-opacity": .7}).hide();
	
			// Path & circles
			var p = [], bgpp = [];
			for (var j = 0, ic = 0, ii = this.chart.graph[i].data.length; j < ii; j++) {
				if(this.chart.graph[i].data[j] != null) { // Consider the circles just if there is data associated		
					var newy = Math.round(this.chart.height - this.chart.bottomgutter - Y * this.chart.graph[i].data[j]),
						x = Math.round(this.chart.leftgutter + X * (j));
			
					//Circles
					this.chart.graph[i].circles[ic].unhover();
					this.chart.graph[i].circles[ic].animate({'cy':newy},300);
					this.addPopupDot(this.chart.graph[i].circles[ic].attr('cx'), newy, this.chart.graph[i].data[j], this.chart.xaxis[j], this.chart.graph[i].circles[ic], frame, label2, this.chart.graph[i].magnitude);	

					//Path			
					if (!j || (this.chart.graph[i].data[j] != null && !p.length)) {
						if (this.chart.graph[i].smooth) {
							p = ["M", x, newy, "C", x, newy];
						} else {
							p = ["M", x, newy, "L"];
						}						
						bgpp = ["M", this.chart.leftgutter, this.chart.height - this.chart.bottomgutter, "L", x, newy, "C", x, newy];
					}
					if (j && j < ii - 1) {
						var Y0 = Math.round(this.chart.height - this.chart.bottomgutter - Y * this.chart.graph[i].data[j - 1]),
							X0 = Math.round(this.chart.leftgutter + X * (j - 1)),
							Y2 = Math.round(this.chart.height - this.chart.bottomgutter - Y * this.chart.graph[i].data[j + 1]),
							X2 = Math.round(this.chart.leftgutter + X * (j + 1));
						var a = getAnchors(X0, Y0, x, newy, X2, Y2);
						if(this.chart.graph[i].smooth) {
							p = p.concat([a.x1, a.y1, x, newy, a.x2, a.y2]);
						} else {
							p = p.concat([x, newy, "L"]);
						}
						bgpp = bgpp.concat([a.x1, a.y1, x, newy, a.x2, a.y2]);
					}
					ic++;
				}
			}
			p = p.concat([x, newy, x, newy]);
			bgpp = bgpp.concat([x, newy, x, newy, "L", x, this.chart.height - this.chart.bottomgutter, "z"]);
			this.chart.graph[i].path.animate({'path': p},300);
			//bgp.attr({path: bgpp});
			this.chart.graph[i].path.toFront();
			frame.toFront();
			label2[0].toFront();
			label2[1].toFront();
			this.chart.graph[i].circles.toFront(); 
		}
	}
}
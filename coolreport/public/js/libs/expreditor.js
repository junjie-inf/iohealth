/**
 * ExprEditor Lib
 * 
 * This library contains methods to edit expressions.
 * 
 * @type type
 */

var ExprEditor = {
	baseTree : [{
		text: "Operators",
		icon: "icon-superscript",
		a_attr: {'draggable':'false'},
		children: [
			{
				text: "Logical",
				icon: "icon-check",
				a_attr: {'draggable':'false'},
				children: [
					{
						text: "And",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : 'AND',
						},
					},
					{
						text: "Or",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : 'OR',
						},
					},
					{
						text: "Not",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : 'NOT',
						}
					}
				]
			},
			{
				text: "Comparison",
				icon: "icon-chevron-right",
				a_attr: {'draggable':'false'},
				children: [
					{
						text: "=",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : '=',
						},
					},
					{
						text: "<",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : '<',
						},
					},
					{
						text: "≤",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : '<=',
						}
					},
					{
						text: ">",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : '>',
						}
					},
					{
						text: "≥",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : '>=',
						}
					},
					{
						text: "≠",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : '<>',
						}
					},
					{
						text: "LIKE",
						icon: false,
						li_attr: {
							'draggable':'true', 
							'data-expr' : 'LIKE'
						}
					},
				]
			}
		]
	}],
	
	showExprEditor: function( input, templates, operators )
	{
		ExprEditor.initExprEditor(input);

		var fieldsRoot = { text: "Fields", icon: 'icon-edit', children: [] }
		for (var t = 0; t < templates.length; t++)
		{
			var template = templates[t];
			var tnode = {
				text: template.title,
				icon: 'icon-copy',
				children: [],
				a_attr: {'draggable':'false'}
			};
			
			for (var f = 1; f < DashItems.fixedFields.length; f++)
			{
				//Se empieza por 1 en vez de 0 para quitar el id del fixed (id-local)
				var field = DashItems.fixedFields[f];
				
				var fnode = {
					text: field.label,
					icon: DashItems.icons[field.type],
					li_attr: {
						'draggable':'true', 
						// 'data-expr' : '$' + template.id + '.' + field.id,
						'data-expr' : '[' + template.title + '.' + field.id + "]",
					}
				};
				tnode.children.push(fnode);
			}
			
			for (var f = 0; f < template.fields.length; f++)
			{
				var field = template.fields[f];
				
				var fnode = {
					text: field.label,
					icon: DashItems.icons[field.type],
					children: [],
					li_attr: {
						'draggable':'true', 
						// 'data-expr' : '$' + template.id + '.' + field.id,
						'data-expr' : '[' + template.title + '.' + field.label + "]",
					}
				};

				if (field.type == 'checkbox' || field.type == 'radio' || field.type == 'select')
				{
					for (var o = 0; o < field.options.length; o++)
					{
						var option = field.options[o];

						var onode = {
							text: option.value,
							icon: DashItems.icons[field.type],
							li_attr: {
								'draggable':'true',
								// 'data-expr' : "'%" + option.id + "%'",
								'data-expr' : "[" + option.value + "]",
							}
						};
						fnode.children.push(onode);
					}
				}

				tnode.children.push(fnode);
			}

			if(template.id == 10){ //Persona
				tnode.children.push({
					text: 'Edad',
					icon: "icon-user",
					children: [],
					li_attr: {
						'draggable':'true', 
						// 'data-expr' : '$' + template.id + '.' + field.id,
						'data-expr' : '[' + template.title + '.edad]',
					}
				});
			}
				
			fieldsRoot.children.push(tnode);
		}

		// Expressions
		var ownRow = $(input).closest('.field-row');

		if (ownRow.attr('data-type') == 'expression')
		{
			var onode = {
				text: 'Other',
				icon: 'icon-th',
				children: [],
				a_attr: {'draggable':'false'}
			};

			$(ownRow.siblings()).each(function(k,v) {
				var crow = $(v);
				var type = crow.attr('data-type');

				if ($.inArray(type, ['avg', 'count', 'expression', 'sum', 'max', 'min']) < 0)
					return;

				var rnode = {
					text: crow.find('.field-name').val(),
					icon: DashItems.icons[type],
					li_attr: {
						'draggable':'true',
						'data-expr' : '$_' + crow.attr('id'),
					}
				};

				onode.children.push(rnode);
			});

			if (onode.children.length > 0)
				fieldsRoot.children.push(onode);
		}

		var jsTreeConfig = {
			plugins : ['search'],
			core : {
				// Por defecto los operadores se muestran, salvo en caso de que se especifique el parametro operators como false
				data : ((typeof operators !== 'undefined' && operators !== true) ? fieldsRoot : [fieldsRoot].concat(this.baseTree))
			}
		};
		
		var html = Handlebars.compile( $("#tpl-expreditor").html() )({
			expr: $(input).val()
		});
		$('#cr-expreditor-modal').remove();
		
		var modal = $(html).appendTo($('#content')).modal();
		
		var tree = modal.find('.cr-expreditor-tree').jstree(jsTreeConfig);
		
		modal.on('keyup', '.cr-expreditor-search', function(){
			tree.jstree(true).search($(this).val());
		}).on('change', '.cr-expreditor-search', function(){
			tree.jstree(true).search($(this).val());
		}).on('dblclick', 'a',function (e) {
			var node = $(e.target).closest("li");
			var expr = node.data('expr');
			if (expr)
			{
				//Añadir espacio al final si no lo hay y concatenar
				var val = modal.find('.cr-expreditor-expression').val();
				if (val.length > 0 && val[val.length - 1] != ' ')
					val += ' ';
				modal.find('.cr-expreditor-expression').val(val + expr);
			}
		}).on('dragstart', 'li',function (e) {
			e.originalEvent.dataTransfer.setData("text/plain", $(this).data("expr"));
			e.stopPropagation();
		}).on('click', '.cr-expreditor-save', function(e) {
			$(input).val(modal.find('.cr-expreditor-expression').val());
			ExprEditor.getExpr( input, templates );
			modal.modal('hide');
		});
	},

	initExprEditor: function (input) {
		
		var hidden = $(input).next();
		if ( !($(hidden).prop('tagName') === $(input).prop('tagName') && $(hidden).hasClass('hidden')) ) {
			//Si no se ha creado ya un clon del input invisible, se crea
			hidden = $(input).clone();
			$(hidden).removeAttr('id');
			$(hidden).addClass('hidden');
			$(input).after(hidden);

			// se le quita el name para que no se envie al form y se envie el invisible
			$(input).removeAttr('name');

			// Se actualiza el campo invisible cuando se cambia el input
			$(input).on('change', function(){
				ExprEditor.getExpr(this, CR_TEMPLATE);
			})
		}
	},

	// obtiene el valor bonito de la expresion y lo muestra, no actualiza el hidden
	viewExpr: function( input , templates )
	{
		ExprEditor.initExprEditor(input);

		var string = $(input).val();

		// ---------FIELDS
		var fields = string.match(/\$\w+.\w+/gi);

		for ( f in fields){
			var words = fields[f].match(/\w+/gi);

			var label = "";
			var template;

			for (var t in templates) {
				// obtenemos el template con el identificador antes del punto
				if((templates[t].id) == (words[0]) ){
					template = templates[t];
				}
			}

			if (template != null) {
				// Si empieza por _ el identificador despues de punto es de los fixed fields
				if (words[1].startsWith("_") ) {
					for (var ff in DashItems.fixedFields) {
						// Si se ha encontrado el field
						if((DashItems.fixedFields[ff].id) === (words[1]) ){
							label = DashItems.fixedFields[ff].id;
						}
					}
				}else{
					for (var tf in template.fields) {
						// Si se ha encontrado el field
						if((template.fields[tf].id) === (words[1]) ){
							label = template.fields[tf].label;
						}
					}
				}

				if (label !== "") {
					string = string.replace(fields[f] , "[" + template.title + "." + label + "]" );
				}
			}
		}

		// ---------OPTIONS
		var options = string.match(/\'\%\w+\%\'/gi);

		for (var op in options){

			var option_label = "";
			var word = options[op].match(/\w+/gi);

			// por cada template recibido
			for (var t in templates) {
				//Por cada field del templates
				for (var f in templates[t].fields) {
					if (templates[t].fields[f].type == 'checkbox' || templates[t].fields[f].type == 'radio' || templates[t].fields[f].type == 'select')
					{
						for (var o in templates[t].fields[f].options)
						{
							if(templates[t].fields[f].options[o].id === word[0] ){
								option_label = templates[t].fields[f].options[o].value;
							}						
						}
					}
				}
			}

			if (option_label !== "") 
				string = string.replace(options[op], "[" + option_label + "]" );
		}

		// console.log('templates', templates);
		// console.log('fixed-templates', DashItems.fixedFields);
		// console.log('string', string);

		$(input).val(string);
	},

	//Obtiene la expresion del input sin mostrarlo y actualiza el hidden
	getExpr: function( input , templates )
	{
		ExprEditor.initExprEditor(input);

		var string = $(input).val();

		// ---------FIELDS

		var fields = string.match(/\[[\w -()\u00C0-\u017F]+\.[\w -()\u00C0-\u017F]+\]/gi);
		var options = string.match(/\[[\w -()\u00C0-\u017F]+\]/gi);

		for ( f in fields){
			var words = fields[f].match(/[\w -()\u00C0-\u017F]+/gi);

			var template_id = "";
			var id = "";
			var template;

			for (var t in templates) {
				// obtenemos el template con el identificador antes del punto
				if((templates[t].title) === (words[0]) ){
					template = templates[t];
				}
			}

			if (template != null) {
				if (words[1].startsWith("_") ) {		
					//Se comprueban los fields fijos
					for (var ff in DashItems.fixedFields) {
						// Si se ha encontrado el field
						if((DashItems.fixedFields[ff].id).toUpperCase() === (words[1]).toUpperCase() ){
							id = DashItems.fixedFields[ff].id;
						}
					}
				}else{
					//Se comprueban los fields
					if(!((words[1]).toUpperCase() == "EDAD"))
						for (var tf in template.fields) {
							// Si se ha encontrado el field
							if((template.fields[tf].label).toUpperCase() === (words[1]).toUpperCase() ){
								id = template.fields[tf].id;
							}
						}
					else
						id = "edad";
				}

				if (id !== "") 
					string = string.replace(fields[f] , '$' + template.id + '.' + id );
			}
		}

		// ---------OPTIONS

		for (var op in options){

			var option_id = "";
			var word = options[op].match(/[\w -()\u00C0-\u017F]+/gi);

			// por cada template recibido
			for (var t in templates) {
				//Por cada field del templates
				for (var f in templates[t].fields) {
					if (templates[t].fields[f].type == 'checkbox' || templates[t].fields[f].type == 'radio' || templates[t].fields[f].type == 'select')
					{
						for (var o in templates[t].fields[f].options)
						{
							if( (templates[t].fields[f].options[o].value).toUpperCase() === (word[0]).toUpperCase() ){
								option_id = templates[t].fields[f].options[o].id;
							}						
						}
					}
				}
			}

			if (option_id !== "") 
				string = string.replace(options[op], "'%" + option_id + "%'" );
		}

		// console.log('templates', templates);
		// console.log('fixed-templates', DashItems.fixedFields);
		// console.log('string', string);

		$(input).next().val(string);
	},

	addSwitchSelect: function( control, original ){
		// Añadir a tipo de seleccion al control
		var toggle = $(
			'<label class="switch switch-primary switch-select-report">'+
		    '  <input type="checkbox" class="switch-input">'+
		    '  <span class="switch-label" data-on="Other" data-off="Original"></span>'+
		    '  <span class="switch-handle switch-select-report"></span>'+
		    '</label>');

		$(control).prepend(toggle);

		var type;
		var template = $('#template-select').val();
		var id = $(control).find('[name]').first().attr('name').match(/\w+/gi);

		if ($(control).find('select').length) 
			type = 'select';
		else if ($(control).find('.btn').length)
			type = 'report';
		else if ($(control).find('.radio').length) 
			type = 'radio';
		else if ($(control).find('.checkbox').length) 
			type = 'checkbox';
		else if ($(control).find('input[type=number]').length) 
			type = 'number';
		else if ($(control).find('input').length) 
			type = 'text';


		if (type === 'report' || type === 'text') {
			if (original){
				$(control).find('.form-control[name]').val("$"+ template +"."+ id );
				$(toggle).siblings().hide();
			}else{
				$(toggle).find('.switch-input').prop('checked',true);
			}

			$(toggle).on('click', function(){
				if ($(this).find('.switch-input').prop('checked')) {

					$(this).find('.switch-input').prop('checked',true);

					$(this).siblings('.form-control').val("");
					$(this).siblings('.report-title').html("[No report selected]");

					$(this).siblings().show(200);

				}else{

					$(this).find('.switch-input').prop('checked',false);

					// se pone la expresion del report 
					$(this).siblings('.form-control[name]').val("$"+ template +"."+ id );
					$(this).siblings('.form-control:not([name])').val("");

					$(this).siblings().hide(200);
				}
			});
		}
		else if (type === 'number') {

			if (original){
				$(control).find('.form-control').attr('type','text');
				$(control).find('.form-control').val("$"+ template +"."+ id );
				$(toggle).siblings().hide();
			}else{
				$(toggle).find('.switch-input').prop('checked',true);
			}

			$(toggle).on('click', function(){
				if ($(this).find('.switch-input').prop('checked')) {

					$(this).find('.switch-input').prop('checked',true);

					$(control).find('.form-control').attr('type','number');
					$(control).find('.form-control').val("");

					$(this).siblings().show(200);

				}else{

					$(this).find('.switch-input').prop('checked',false);

					$(this).siblings().hide(200);

					$(control).find('.form-control').attr('type','text');
					$(control).find('.form-control').val("$"+ template +"."+ id );
				}
			});
		}
		else if (type === 'checkbox' || type === 'radio') {

			// Añado una opcion del original
			var box = $(control).find('input').last().clone();

			$(box).val("$"+ template +"."+ id );
			$(box).addClass("hidden");

			$(control).prepend(box);

			if (original){
				$(control).find('input').prop('checked',false);
				$(box).prop('checked',true);
				$(toggle).siblings().hide();
			}else{
				$(toggle).find('.switch-input').prop('checked',true);
			}

			$(toggle).on('click', function(){
				if ($(this).find('.switch-input').prop('checked')) {

					$(this).find('.switch-input').prop('checked',true);
					$(box).prop('checked',false);

					$(this).siblings().show(200);
					
				}else{

					$(control).find('input').prop('checked',false);
					$(box).prop('checked',true);

					$(this).siblings().hide(200);
				}
			});

		}else if (type === 'select') {

			// Añado una opcion del original
			var option = new Option("Original", "$"+ template +"."+ id);

			$(option).addClass('hidden');
			$(control).find('select[multiple=multiple]').addClass('hidden');
			$(control).find('select').prepend(option);
			$(control).find('select').trigger("chosen:updated");


			if (original){
				$(control).find('option').prop('checked',false);
				$(option).prop('selected',true);
				$(toggle).siblings().hide();
			}else{
				$(toggle).find('.switch-input').prop('checked',true);
			}

			$(toggle).on('click', function(){
				if ($(this).find('.switch-input').prop('checked')) {

					$(this).find('.switch-input').prop('checked',true);

					$(option).prop('selected',false);
					$(control).find('select').trigger("chosen:updated");

					$(this).siblings().show(200);
					
				}else{

					$(control).find('option').prop('selected',false);
					$(option).prop('selected',true);

					$(this).siblings().hide(200);
					$(control).find('select').trigger("chosen:updated");
				}
			});

			// $(toggle).trigger('click');

		}
	},

	addSwitchReportSelect: function( control, original ){
		// Añadir a tipo de seleccion al control
		var toggle = $(
			'<label class="switch switch-primary switch-select-report">'+
		    '  <input type="checkbox" class="switch-input">'+
		    '  <span class="switch-label" data-on="Other" data-off="Original"></span>'+
		    '  <span class="switch-handle switch-select-report"></span>'+
		    '</label>');

		$(control).prepend(toggle);

		var template = $('#template-select').val();
		var id = $(control).find('[name]').first().attr('name').match(/\w+/gi);

		if (original){
			$(control).find('.form-control[name]').val("");
			$(toggle).siblings().hide();
			$(control).find('.form-control[name]').attr("type","text").show();

		}else{
			$(toggle).find('.switch-input').prop('checked',true);
		}

		$(toggle).on('click', function(){
			if ($(this).find('.switch-input').prop('checked')) {

				$(this).find('.switch-input').prop('checked',true);

				$(this).siblings('.form-control').val("");
				$(this).siblings('.report-title').html("[No report selected]");

				$(this).siblings().hide();
				$(this).siblings("button, .report-title").show(200);

			}else{

				$(this).find('.switch-input').prop('checked',false);

				// se pone la expresion del report 
				$(this).siblings('.form-control').val("");

				$(this).siblings().hide();
				$(this).siblings(".form-control").show(200);
			}
		});
	},
};

/**
 * Manager Lib
 * 
 * This library contains the CRUD methods to manage reports, attachments and comments.
 * 
 * @type type
 */

var Manager = {
	
	urls: {
		report : 'report',
		group : 'group',
		role : 'role',
		user : 'user',
		profile : 'profile',
		comment: 'report/%d/comment',
		attachment: 'report/%d/attachment',
		template: 'template',
		answer: 'report/%d/answer',
		dashitem : 'dashitem',
		alert: 'alert',
		map: 'map',
		dashboard : 'dashboard',
		vote: 'report/%d/vote',
	},
	
	init: function(){

		var self = this;
		
		/* ------ CRUD actions for tables ------ */
		// Delete
		$(document).on('click', '.action-delete', function(e){
			e.preventDefault();
			Manager.remove( $(this) );
		});
		
		/* ------ CRUD actions for tables ------ */
		// Follow
		$(document).on('click', '.action-follow', function(e){
			e.preventDefault();
			Manager.follow( $(this), 'true' );
		});
		// Unfollow
		$(document).on('click', '.action-unfollow', function(e){
			e.preventDefault();
			Manager.follow( $(this), 'false' );
		});

		// Store
		$('.form-store').on('submit', function(e) {
			e.preventDefault();
			Manager.store( $(this).closest('form') );
		});

		// Edit
		$('.form-edit').on('submit', function(e) {
			e.preventDefault();
			Manager.edit( $(this).closest('form') );
		});
		
		/* ------------ Comments ------------ */
		// Insert comment in report
		$(document).on('click', '.send-comment', function(e){
			e.preventDefault();
			self.sendComment( $(this) );
		});

		$(document).on('click', '.action-vote', function(e){
			e.preventDefault();
			id = $(this).data('vote-id');
			type = $(this).data('vote-type');
			$(this).addClass("disabled");
			$('.action-unvote.'+ type +"-"+ id).addClass("disabled");
			Manager.vote( $(this), 'true' );
		});

		$(document).on('click', '.action-unvote', function(e){
			e.preventDefault();
			id = $(this).data('vote-id');
			type = $(this).data('vote-type');
			$('.action-vote.'+ type +"-"+ id).addClass("disabled");
			$(this).addClass("disabled");
			Manager.vote( $(this), 'false' );				
		});
	},
	
	/**
	 * [follow description]
	 * @param  {jQuery} $button Button clicked
	 * @param  {string} follow  new status for follow ('true' or 'false')
	 */
	follow: function( $button, follow ){
		var id = $button.data('followable-id'),
			type = $button.data('followable-type'),
			toggle = $button.data('toggle'), // true/false (.data() hace el casting a boolean)
			follow = (follow === 'true'); // casting to boolean

		console.log($button.closest('form'));
		Forms.post(
			$SITE_PATH + type + '/' + id + '/follow',
			{
				follow: follow
			},
			$button,
			function(d){
				if( d.status === "OK" ){
					
					// Toggleable (es un botón)
					if( toggle )
					{
						$button.siblings('.action-' + ( follow ? 'unfollow' : 'follow' ) ).show(); // Muestro botón contrario
						
						$button.hide(); // Oculto botón actual
					}
					// No toggleable (estoy en una tabla)
					else
					{
						// Elimino fila
						$button.closest('tr').fadeTo('slow', 0.00, function(){ //fade
							$(this).slideUp("slow", function() { //slide up
								$(this).remove(); //then remove from the DOM
							});
						});
					}
				}
			}
		);
	},

	remove: function( $button ){
		var $container = $button.closest('.action-data'),
			parent_id = $container.data('parent-id'),
			id = $container.data('id'),
			type = $container.data('type'),
			redirect = $container.data('redirect-url');

		bootbox.confirm('<h2><small class="inh_bold text-danger"><i class="icon-remove icon-2x"></i> ¿Está seguro de su eliminación?</small></h2>', function(result){
			if( result ){

				Forms.post(
					$SITE_PATH + sprintf(Manager.urls[type], parent_id) + '/' + id,
					{
						_method: 'DELETE'
					},
					$button,
					function(d){
						if( d.status === "OK" ){
							if( redirect ){
								window.location.href = redirect;
							}else{
								// Elimino fila
								$container.fadeTo('slow', 0.00, function(){ //fade
									$(this).slideUp("slow", function() { //slide up
										$(this).remove(); //then remove from the DOM
									});
								});
							}
						}
					}
				);

				$(".modal").modal("hide");

				// Solo si estoy en modo mapa
				if ($container.hasClass("modal"))
				{
					CoolReport.infowindow.close();
					CoolReport.updateMarkers(true);
				}

			}
		});
		return false;
	},
	
	store: function( $form ){
		var type = $form.data('type'),
			parent_id = $form.data('parent-id'),
			$button = $form.find(':submit');
			
		Forms.post( $SITE_PATH + sprintf(Manager.urls[type], parent_id), $form.serialize(), $button );
	},
	
	edit: function( $form ){
		var type = $form.data('type'),
			id = $form.data('id'),
			parent_id = $form.data('parent-id'),
			$button = $form.find(':submit');
		
		Forms.post( $SITE_PATH + sprintf(Manager.urls[type], parent_id) + '/' + id, $form.serialize(), $button );
	},
	
	
	// Enviar comentario
	sendComment: function( $button ){
		var $form = $button.closest('form'),
			$li = $form.closest('li'),
			content = $form.find('textarea').val(),
			id_report = $form.attr('data-report');
		
		Forms.post(
			$SITE_PATH + 'report/' + id_report  + '/comment',
			{
				id_report: id_report,
				content: content
			},
			$button,
			function(d) {
				if( d.status === "OK" ){
					/* Insertamos comentario en el DOM */
					var newHtml = 
						'<li data-id="'+d.id+'" data-parent-id="'+id_report+'" data-type="comment" class="action-data">\
							<div class="name"><a href="' + $USER.url + '" title="View profile">' + $USER.firstname + ' ' + $USER.surname + '</a></div>\
							<div class="date">' + $_LANG.GENERIC.few_seconds_ago + '</div>\
							<div class="delete action-delete"><i class="icon-remove"></i></div>\
							<div class="message">' + nl2br( htmlentities(content) ) + '</div>\
						</li>';

					$(newHtml).insertAfter($li);
					$form.find('textarea').val('');
				}
			}
		);
	},


	vote: function ( $button, vote ){
		var id = $button.data('vote-id'),
			type = $button.data('vote-type');

		console.log(id, type);

		Forms.post(
			$SITE_PATH + type + '/' + id + '/vote', 
			{
				id: id,
				value: vote,
				votable_type: type
			},
			$button, 
			function(d){
				console.log(d);
				if( d.status === "OK" ){
					$(".action-vote." + type + "-" + id).html($(".action-vote." + type + "-" + id).html() + " " + d.votes[0]);
					$(".action-unvote." + type + "-" + id).html($(".action-unvote." + type + "-" + id).html() + " " + d.votes[1]);
				}
			},
			true
		);
	}

	
};

// Init Lib
Manager.init();
jQuery( document ).ready( function() {

	jQuery( 'body' ).on( 'click', '.sidemenu-item a:not(.child-item a)', function( e ) {

		e.preventDefault();

		var clickedid = jQuery( this ).data('id');

		if ( jQuery( this ).hasClass( 'sidemenu_open' ) ) {

			jQuery('.sidemenu_submenu[data-id="' + clickedid + '"]').empty();

			jQuery( this ).removeClass( 'sidemenu_open' );

	} else {
		jQuery( this ).addClass( 'sidemenu_open' );

		var raw_menu = JSON.parse( vars.children );

		raw_menu.forEach( function(element) {
			var parts = element.split('::');

			var title = parts[0],
			url = parts[1],
			id = parts[2],
			parentid = parts[3];

			if ( parentid == clickedid ) {
				jQuery('.sidemenu_submenu[data-id="' + clickedid + '"]').append('<li class="child-item">' +'<a href="' + url + '" data-id="' + id + '">' + title + '</a>' + '</li>');

			}

		});
	}


	}); // end on click


}); // end on ready

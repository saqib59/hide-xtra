(function($) {
	$(function() {
		$( '#adminmenu > li > a:first-child' ).each( function( index, element ) {
					let key = $( this ).attr( 'href' );
					console.log(key);

					if (key.search("page=") > 0) {
						key = key.substr(key.indexOf("=") + 1)
					}
					if (key == 'xtra_settings') {
						return;
					}
					// else{
						$( '#always_show_xtra' ).append(
						'<input type="checkbox" name="always_show_checkbox[]" class="always_show_checkbox" value="' + key + '"' + ( ( -1 !== $.inArray( key, xtraSettings.always_show ) ) ? 'checked="checked"' : '' ) + '> ' + $( this ).find('.wp-menu-name').clone().children().remove().end().text() + '<br><br>'
						)	
					// }
					 });
		
		$(".always_show_checkbox").change(function(){
			$always_show = $('input[name=always_show]');
				var searchIDs = $('input:checked').map(function(){
      				return $(this).val();
			    });
				$always_show.val(searchIDs.get());

		});


			// Add search
			$( '#adminmenu' ).prepend('<li id="xtra_search"><input type="text" placeholder="Search"></li>');

			$( '#xtra_search input' ).on('keyup', function(){

				var search = $( this ).val().toLowerCase();

				if ( '' === search ){
					$( 'li.xtra_hidden' ).hide();
				} else {
					$( '.wp-menu-name' ).each( function() {
						var wrap = $(this).closest("li.menu-top")
						var menu = $(this).text();
						if ( -1 === menu.toLowerCase().indexOf( search ) ) {
							if ( 'toplevel_page_xtra_show_more' !== wrap.attr( 'id' ) ) {
								wrap.hide();
							}
							// search submenu
							$(this).closest( "li.menu-top" ).find( "ul.wp-submenu li" ).each( function() {
								var submenu = $(this).text();
								if ( -1 !== submenu.toLowerCase().indexOf( search ) ) {
									wrap.show();
								}
							});
						} else {
							wrap.show();
						}

					});
				}
			});
	});


	// Save click on menu items
	$( 'html' ).on( 'click', '#adminmenu a', function(e) {
		var $current = $( e.currentTarget );
		var href = $current.attr( 'href' );
		var $li = $current.closest( 'li.menu-top' );
		var slug = $li.find( 'a.menu-top' ).attr( 'href' );
		var redirect = false;

		if ( $li.hasClass( 'xtra_hidden' ) ) {
			// wait ajax for updating hidden menu
			e.preventDefault();
			redirect = true;
		}
		var data = {
			action: 'xtra_click_on_menu',
			slug: slug
		};

		$.post( ajaxurl, data, function( response ) {
			if ( redirect ) {
				window.location = href;
			}
		});
	});

})(jQuery);

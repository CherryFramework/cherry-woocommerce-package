/**
 * Debouncedresize: special jQuery event that happens once after a window resize
 *
 * Copyright 2012 @louis_remi
 * Licensed under the MIT license.
 */
(function( $ ) {

	'use strict';

	var $event = $.event,
		$special,
		resizeTimeout;

	$special = $event.special.debouncedresize = {
		setup: function() {
			$( this ).on( 'resize', $special.handler );
		},
		teardown: function() {
			$( this ).off( 'resize', $special.handler );
		},
		handler: function( event, execAsap ) {
			var context = this,
				args = arguments,
				dispatch = function() {

					// Set correct event type
					event.type = 'debouncedresize';
					$event.dispatch.apply( context, args );
				};

			if ( resizeTimeout ) {
				clearTimeout( resizeTimeout );
			}

			execAsap ?
				dispatch() :
				resizeTimeout = setTimeout( dispatch, $special.threshold );
		},
		threshold: 150
	};

})( jQuery );

/**
 * Dropdowns handle plugin
 */
(function( $ ) {

	'use strict';

	$.fn.CherryWCDropdown = function() {

		return this.each( function() {
			var $this    = $( this ),
				$trigger = $( '[data-dropdown="trigger"]', $this );

			$trigger.on( 'click', function( event ) {

				event.preventDefault();
				event.stopPropagation();
				event.stopImmediatePropagation();

				$this.attr( 'data-dropdown-active', 'true' );

			});

			$( document ).mouseup( function( e ) {
				if ( ! $this.is( e.target ) && 0 === $this.has( e.target ).length ) {
					$this.attr( 'data-dropdown-active', 'false' );
				}
			});
		});

	};
})( jQuery );

jQuery( document ).ready(function( $ ) {

	'use strict';

	var initial_width = $( '#site-wrapper' ).width(),
		current_width = initial_width,
		reinit        = false;

	// init dropdowns
	$( '[data-dropdown="box"]' ).CherryWCDropdown();

	// quick view
	$(document).on( 'click', '.cherry-quick-view', function( event ) {

		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();

		var product_id = $( this ).data( 'product' ),
			item = $( this ).parents( 'li.product' ),
			current_popup = 'cherry-quick-view-popup-' + product_id;

		var send_ajax_request = function() {
			jQuery.ajax({
				type : 'post',
				dataType : 'html',
				url : window.cherry_woocommerce.ajaxurl,
				data : {
					action: 'cherry_wc_quick_view',
					_wpnonce: window.cherry_woocommerce.nonce,
					product: product_id
				},
				success: function( response ) {
					$( '#'+current_popup ).find( '.cherry-quick-view-popup-content' ).html( response );
				}
			});
		};

		if ( ! item.find( '.cherry-quick-view-popup' ).length ) {
			item.append( '<div id="' + current_popup + '" class="cherry-quick-view-popup mfp-hide"><span href="#" class="mfp-close">&times;</span><div class="cherry-quick-view-popup-content"><div class="cherry-quick-view-load">' + window.cherry_woocommerce.loading + '</div></div></div>' );
			send_ajax_request();
		}

		if ( $.isFunction( jQuery.fn.magnificPopup ) ) {
			$.magnificPopup.open( {
				items: {
					src: '#' + current_popup
				},
				type: 'inline'
			}, 0 );
		}

		return false;

	});

	// zoom init
	function zoomInit() {

		if ( ! $( '.product-large-image img' ).length ) {
			return;
		}

		$( '.zoomContainer' ).remove();

		if ( $.isFunction( jQuery.fn.elevateZoom ) ) {
			$( '.product-large-image img' ).elevateZoom({
				zoomType: 'inner',
				cursor: 'crosshair',
				zoomWindowFadeIn: 500,
				zoomWindowFadeOut: 750
			});
		}

	}

	// single product page
	$( document ).on( 'click', '.product-thumbnails_item', function( event ) {

		event.preventDefault();
		var _this      = $( this ),
			_parent    = _this.parents( '.product-images' ),
			_large_img = _this.attr( 'data-large-img' ),
			_orig_img  = _this.attr( 'data-original-img' );

		if ( $( '.placeholder-thumb', _this ).length > 0 ) {
			return;
		}

		_this.addClass( 'active' ).siblings().removeClass( 'active' );
		_parent.find( '.product-large-image img' ).attr( 'src', _large_img );
		_parent.find( '.product-large-image img' ).attr( 'data-zoom-image', _orig_img );
		zoomInit();
	});

	zoomInit();

	function reinit_scripts() {

		reinit        = false;
		current_width = $( '#site-wrapper' ).width();

		if ( initial_width > 979 && current_width <= 979) {
			reinit = true;
		} else if ( initial_width <= 979 && current_width > 979 ) {
			reinit = true;
		} else if ( initial_width > 450 && current_width <= 450 ) {
			reinit = true;
		} else if ( initial_width <= 450 && current_width > 450 ) {
			reinit = true;
		}

		if ( true === reinit ) {
			initial_width = current_width;
			zoomInit();
			if ( $.isFunction( jQuery.fn.cycle ) ) {
				$( '.cycle-slideshow' ).cycle( 'reinit' );
			}
		}

	}

	$( window ).on( 'orientationchange debouncedresize', reinit_scripts );

	//Change variation images on variation change
	$( document ).on('found_variation', function( event, variation ) {
		event.preventDefault();
		var thumb     = variation.image_src,
			large_img = variation.image_link,
			item      = $( '.product-large-image' ),
			image     = $( 'img', item );

		if ( '' === thumb || '' === large_img ) {
			thumb     = image.data( 'initial-thumb' ),
			large_img = image.data( 'initial-thumb-large' );
		} else if ( $( '.product-thumbnails_item.active-image' ).length > 0 ) {
			$( '.product-thumbnails_item.active-image' ).removeClass( 'active-image' );
		} else if ( $('.owl-item.active-image').length > 0 ) {
			$( '.owl-item.active-image' ).removeClass( 'active-image' );
		}

		image.attr( 'src', thumb ).data( 'zoom-image', large_img ).attr( 'data-zoom-image', large_img );
		zoomInit();
	});

	$(document).on( 'reset_data', function( event ) {
		event.preventDefault();
		var item         = $('.product-large-image'),
			image        = $('img', item),
			initial_thmb = image.data('initial-thumb'),
			initial_lrg  = image.data('initial-thumb-large');

		image.attr ('src', initial_thmb ).data( 'zoom-image', initial_lrg ).attr( 'data-zoom-image', initial_lrg );
		zoomInit();
	});

	/**
	 * Open sharing popup
	 */
	$(document).on( 'click', '.share-buttons_link', function( event ) {
		event.preventDefault();
		var width  = 816,
			height = 400,
			url    = $( this ).data( 'url' );

		var leftPosition, topPosition;
		//Allow for borders.
		leftPosition = ( window.screen.width / 2 ) - ( ( width / 2 ) + 10 );
		//Allow for title and status bars.
		topPosition = ( window.screen.height / 2 ) - ( ( height / 2 ) + 50 );
		//Open the window.
		window.open( url, 'Share this', 'status=no,height=' + height + ',width=' + width + ',resizable=yes,left=' + leftPosition + ',top=' + topPosition + ',screenX=' + leftPosition + ',screenY=' + topPosition + ',toolbar=no,menubar=no,scrollbars=no,location=no,directories=no' );
	});

});
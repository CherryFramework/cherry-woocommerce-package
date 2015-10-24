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

			var $this = $( this );

			$this.on( 'click', '[data-dropdown="trigger"]', function( event ) {

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

/**
 * Quantity controls
 */
(function( $ ) {

	'use strict';

	$.fn.CherryWCQty = function() {

		return this.each( function() {

			var $this  = $( this ),
				add    = $( '.qty-controls-add', $this ),
				remove = $( '.qty-controls-remove', $this ),
				input  = $( '.qty', $this ),
				min    = input.attr( 'min' ),
				max    = input.attr( 'max' ),
				step   = input.attr( 'step' ),
				current;

			if ( undefined === step ) {
				step = 1;
			} else {
				step = parseInt( step );
			}

			if ( undefined === min ) {
				min = 1;
			} else {
				min = parseInt( min );
			}

			if ( undefined === max ) {
				max = false;
			} else {
				max = parseInt( max );
			}

			$this.on( 'click', add.selector, function( event ) {

				event.preventDefault();
				current = parseInt( input.val() );

				if ( false == max || max <= ( current + step ) ) {
					input.val( current + step );
				}

			});

			$this.on( 'click', remove.selector, function( event ) {

				event.preventDefault();
				current = parseInt( input.val() );

				if ( min <= ( current - step ) ) {
					input.val( current - step );
				}

			});

		});

	};
})( jQuery );

jQuery( document ).ready(function( $ ) {

	'use strict';

	var initialWidth = $( '#site-wrapper' ).width(),
		currentWidth = initialWidth,
		reinit        = false;

	// Init dropdowns
	$( '[data-dropdown="box"]' ).CherryWCDropdown();

	// Init Qty controls
	$( '.quantity-wrap' ).CherryWCQty();

	// Quick view
	$( document ).on( 'click', '.cherry-quick-view', function( event ) {

		var productId, item, currentPopup;

		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();

		productId = $( this ).data( 'product' ),
		item = $( this ).parents( 'li.product' ),
		currentPopup = 'cherry-quick-view-popup-' + productId;

		function sendAjaxRequest() {
			jQuery.ajax({
				type: 'post',
				dataType: 'html',
				url: window.CherryWoocommerce.ajaxurl,
				data: {
					action: 'cherry_wc_quick_view',
					_wpnonce: window.CherryWoocommerce.nonce,
					product: productId
				},
				success: function( response ) {
					$( '#' + currentPopup ).find( '.cherry-quick-view-popup-content' ).html( response );
				}
			});
		}

		if ( ! item.find( '.cherry-quick-view-popup' ).length ) {
			item.append( '<div id="' + currentPopup + '" class="cherry-quick-view-popup mfp-hide"><span href="#" class="mfp-close">&times;</span><div class="cherry-quick-view-popup-content"><div class="cherry-quick-view-load">' + window.CherryWoocommerce.loading + '</div></div></div>' );
			sendAjaxRequest();
		}

		if ( $.isFunction( jQuery.fn.magnificPopup ) ) {
			$.magnificPopup.open( {
				items: {
					src: '#' + currentPopup
				},
				type: 'inline'
			}, 0 );
		}

		return false;

	});

	// Zoom init
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

	// Single product page
	$( document ).on( 'click', '.product-thumbnails_item', function( event ) {

		var _this, _parent, _largeImg, _origImg;

		event.preventDefault();

		_this     = $( this ),
		_parent   = _this.parents( '.product-images' ),
		_largeImg = _this.attr( 'data-large-img' ),
		_origImg  = _this.attr( 'data-original-img' );

		if ( $( '.placeholder-thumb', _this ).length > 0 ) {
			return;
		}

		_this.addClass( 'active' ).siblings().removeClass( 'active' );
		_parent.find( '.product-large-image img' ).attr( 'src', _largeImg );
		_parent.find( '.product-large-image img' ).attr( 'data-zoom-image', _origImg );
		zoomInit();
	});

	zoomInit();

	function reinitScripts() {

		zoomInit();
		if ( $.isFunction( jQuery.fn.cycle ) ) {
			$( '.cycle-slideshow' ).cycle( 'reinit' );
		}

	}

	$( window ).on( 'orientationchange debouncedresize', reinitScripts );

	//Change variation images on variation change
	$( document ).on( 'found_variation', function( event, variation ) {

		var thumb, largeImg, item, image;

		event.preventDefault();

		// jscs:disable
		thumb    = variation.image_src,
		largeImg = variation.image_link,
		// jscs:enable

		item     = $( '.product-large-image' ),
		image    = $( 'img', item );

		if ( '' === thumb || '' === largeImg ) {
			thumb    = image.data( 'initial-thumb' ),
			largeImg = image.data( 'initial-thumb-large' );
		} else if ( $( '.product-thumbnails_item.active-image' ).length > 0 ) {
			$( '.product-thumbnails_item.active-image' ).removeClass( 'active-image' );
		} else if ( $( '.owl-item.active-image' ).length > 0 ) {
			$( '.owl-item.active-image' ).removeClass( 'active-image' );
		}

		image.attr( 'src', thumb ).data( 'zoom-image', largeImg ).attr( 'data-zoom-image', largeImg );
		zoomInit();
	});

	$( document ).on( 'reset_data', function( event ) {

		var item, image, initialThmb, initialLrg;

		event.preventDefault();

		item        = $( '.product-large-image' ),
		image       = $( 'img', item ),
		initialThmb = image.data( 'initial-thumb' ),
		initialLrg  = image.data( 'initial-thumb-large' );

		image.attr( 'src', initialThmb ).data( 'zoom-image', initialLrg ).attr( 'data-zoom-image', initialLrg );
		zoomInit();
	});

	/**
	 * Open sharing popup
	 */
	$( document ).on( 'click', '.share-buttons_link', function( event ) {

		var width, height, url, leftPosition, topPosition;

		event.preventDefault();

		width  = 816,
		height = 400,
		url    = $( this ).data( 'url' );

		//Allow for borders.
		leftPosition = ( window.screen.width / 2 ) - ( ( width / 2 ) + 10 );

		//Allow for title and status bars.
		topPosition = ( window.screen.height / 2 ) - ( ( height / 2 ) + 50 );

		//Open the window.
		window.open( url, 'Share this', 'status=no,height=' + height + ',width=' + width + ',resizable=yes,left=' + leftPosition + ',top=' + topPosition + ',screenX=' + leftPosition + ',screenY=' + topPosition + ',toolbar=no,menubar=no,scrollbars=no,location=no,directories=no' );
	});

	/**
	 * Open comapare popup
	 */
	$( document ).on( 'click', '.cherry-compare', function( event ) {

		var button = $( this );

		event.preventDefault();

		$( 'body' ).trigger( 'yith_woocompare_open_popup', { response: window.CherryWoocommerce.compare_table_url, button: button } );
	});

});

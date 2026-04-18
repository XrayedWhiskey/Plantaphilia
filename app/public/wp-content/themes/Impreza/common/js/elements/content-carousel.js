/**
 * UpSolution Element: Content Carousel
 */
! function( $, undefined ) {
	"use strict";

	window.$us = window.$us || {};

	/**
	 * @param {String} container The container.
	 * @param {{}} options The options [optional].
	 */
	function usContentCarousel( container ) {
		let self = this;

		// Elements
		let $carouselContainer = $( '.owl-carousel', container );

		// Predefined options suitable for content carousel
		// https://owlcarousel2.github.io/OwlCarousel2/docs/api-options.html
		let carouselOptions = {
			navElement: 'button',
			navText: [ '', '' ],
			responsiveRefreshRate: 50,
		}

		if ( $carouselContainer.is( '[onclick]' ) ) {
			$.extend( carouselOptions, $carouselContainer[0].onclick() || {} );
			if ( ! $us.usbPreview() ) {
				$carouselContainer.removeAttr( 'onclick' );
			}
		}

		// To prevent scroll blocking on mobiles
		if ( $us.$html.hasClass( 'touch' ) || $us.$html.hasClass( 'ios-touch' ) ) {
			$.extend( carouselOptions, {
				mouseDrag: false,
			} );
		}

		// Override specific options for proper operation in Live Builder
		if ( $us.usbPreview() ) {
			$.extend( carouselOptions, {
				autoplayHoverPause: true,
				mouseDrag: false,
				touchDrag: false,
				loop: false,
			} );
			// TODO: Find a more elegant solution to work correctly in Live Builder!
			$carouselContainer.one( 'initialized.owl.carousel', () => {
				$( '.owl-item', $carouselContainer ).each( ( _, node ) => {
					let $node = $( node ),
						$element = $( '> *', node ),
						usbid = $element.data( 'usbid' ) || $element.data( 'usbid2' );
					$node.attr( 'data-usbid', usbid );
					$element.data( 'usbid2', usbid ).removeAttr( 'data-usbid' );
				} );
				$ush.timeout( () => {
					$( '.owl-dots *, .owl-prev, .owl-next', $carouselContainer )
						.addClass( 'usb_skip_elmSelected' );
				}, 1 );
			} );
			$( 'style[data-for]', $carouselContainer ).each( ( _, node ) => {
				$( node ).next().prepend( node );
			} );
		}

		if ( carouselOptions.autoplayContinual ) {
			carouselOptions.slideTransition = 'linear';
			carouselOptions.autoplaySpeed = carouselOptions.autoplayTimeout;
			carouselOptions.smartSpeed = carouselOptions.autoplayTimeout;
			carouselOptions.slideBy = 1;
		}

		if ( $carouselContainer.data( 'owl.carousel' )) {
			$carouselContainer.trigger( 'destroy.owl.carousel' )
		}

		// Init Owl Carousel
		$carouselContainer.owlCarousel( carouselOptions );

		// Trigger continual autoplay 
		if ( $carouselContainer && carouselOptions.autoplayContinual ) {
			$carouselContainer.trigger( 'next.owl.carousel' );
		}

		// Set aria labels for navigation arrows
		if (
			$carouselContainer
			&& carouselOptions.aria_labels.prev
			&& carouselOptions.aria_labels.next
		) {
			$( '.owl-prev', $carouselContainer ).attr( 'aria-label', carouselOptions.aria_labels.prev );
			$( '.owl-next', $carouselContainer ).attr( 'aria-label', carouselOptions.aria_labels.next );
		}
	}

	$.fn.usContentCarousel = function( options ) {
		return this.each( function() {
			$( this ).data( 'usContentCarousel', new usContentCarousel( this, options ) );
		} );
	};

	$( function() {
		$( '.w-content-carousel' ).usContentCarousel();
	} );

}( jQuery );

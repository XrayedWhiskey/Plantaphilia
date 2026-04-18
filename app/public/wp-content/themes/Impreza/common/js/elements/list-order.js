/**
 * UpSolution Element: List Order
 */
! function( $, undefined ) {
	"use strict";

	/**
	 * @param {Node} container.
	 */
	function usListOrder( container ) {
		let self = this;

		/**
		 * @var {{}} Bondable events.
		 */
		self._events = {
			selectChanged: self._selectChanged.bind( self ),
		};

		// Elements
		self.$container = $( container );
		self.$pageContent = $( 'main#page-content' );

		// Events
		self.$container.on( 'change', 'select', self._events.selectChanged );
	}

	// List Order API
	$.extend( usListOrder.prototype, {
		/**
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_selectChanged: function( e ) {
			var self = this,
				value = e.target.value,
				name = e.target.getAttribute('name'),
				$firstList = $( `
					.w-grid.us_post_list:not(.pagination_numbered):visible,
					.w-grid.us_product_list:not(.pagination_numbered):visible,
					.w-grid-none:visible
				`, self.$pageContent ).first();

			if ( $firstList.hasClass( 'w-grid' ) ) {
				$firstList.addClass( 'used_by_list_order' );
			}
			if ( value === self.lastValue || ! name ) {
				return;
			}
			$firstList.trigger( 'usListOrder', [ self.lastValue = value, name ] );
		}
	} );

	$.fn.usListOrder = function() {
		return this.each( function() {
			$( this ).data( 'usListOrder', new usListOrder( this ) );
		} );
	};

	$( function() {
		$( '.w-order.for_list' ).usListOrder();
	} );

}( jQuery );

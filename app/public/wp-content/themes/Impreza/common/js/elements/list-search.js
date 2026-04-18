/**
 * UpSolution Element: List Search
 */
! function( $, undefined ) {
	"use strict";

	const ENTER_KEY_CODE = 13;

	/**
	 * @param {Node} container.
	 */
	function usListSearch( container ) {
		let self = this;

		/**
		 * @var {{}} Bondable events.
		 */
		self._events = {
			searchTextChanged: self._searchTextChanged.bind( self ),
			formSubmit: self._formSubmit.bind( self ),
		};

		// Elements
		self.$container = $( container );
		self.$input = $( 'input', container );
		self.$pageContent = $( 'main#page-content' );
		self.$message = $( '.w-search-message', container );

		// Variables
		self.name = self.$input.attr( 'name' );
		self.lastValue = '';

		// Events
		self.$container
			.on( 'input', 'input', $ush.throttle( self._events.searchTextChanged, /* wait */300, /* no_trailing */false ) )
			.on( 'click', 'buttom', self._events.searchTextChanged )
			.on( 'submit', 'form', self._events.formSubmit );

		// Defines enter presses in field
		$us.$document.on( 'keypress', function( e ) {
			if ( self.$input.is( ':focus' ) && e.keyCode === ENTER_KEY_CODE ) {
				e.preventDefault();
				self._events.searchTextChanged( e );
			}
		});
	}

	// List Search API
	$.extend( usListSearch.prototype, {

		/**
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_formSubmit: function( e ) {
			e.preventDefault();
			this._events.searchTextChanged( e );
		},

		/**
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_searchTextChanged: function( e ) {
			var self = this,
				$firstList = $( `
					.w-grid.us_post_list:not(.pagination_numbered):visible,
					.w-grid.us_product_list:not(.pagination_numbered):visible,
					.w-grid-none:visible
				`, self.$pageContent ).first();

			if ( $firstList.hasClass( 'w-grid' ) ) {
				self.$message.addClass( 'hidden' ).text('');
				$firstList.addClass( 'used_by_list_search' );

			} else if ( ! $firstList.hasClass( 'w-grid-none' ) ) {
				self.$message
					.html( 'No suitable list found. Add <b>Post List</b> or <b>Product List</b> elements.' ) // do not need to translate
					.removeClass( 'hidden' );
			}
			if ( e.type === 'input' && ! self.$container.hasClass( 'live_search' ) ) {
				return;
			}
			let value = self.$input.val();
			if ( value === self.lastValue || ! self.name ) {
				return;
			}
			self.lastValue = value;
			$firstList.trigger( 'usListSearch', [ value, self.name ] );
		}

	} );

	$.fn.usListSearch = function() {
		return this.each( function() {
			$( this ).data( 'usListSearch', new usListSearch( this ) );
		} );
	};

	$( function() {
		$( '.w-search.for_list' ).usListSearch();
	} );

}( jQuery );

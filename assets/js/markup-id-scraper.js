;( function ( $, document ) {
	'use strict';

	/**
	 * The constructor for the Scraper.
	 *
	 * @since 1.1.0
	 *
	 * @param {string} ajaxurl URL for the AJAX request back to the server.
	 * @param {string} nonce The security code.
	 * @constructor
	 */
	function Scraper( ajaxurl, nonce ) {
		this.ajaxurl       = ajaxurl;
		this.nonce         = nonce;
		this.errorOccurred = false;
		this.scrapedMarkupIds     = new Array();
		this.elementsList  = document.querySelectorAll( '[data-markup-id]' );

		// Bail out if no elements exist.
		if ( this.elementsList.length < 1 ) {
			this.errorOccurred = true;
			return false;
		}

		this.runScraper();

		// Whoops, an error occurred. Bail out.
		if ( this.errorOccurred ) {
			return;
		}

		if ( this.scrapedMarkupIds.length > 0 ) {
			this.sendToServer();
		}
	}

	/**
	 * Loops over the nodeList and runs the scraper over each node.
	 * If an error occurs, it sets the error flag on the object and
	 * bails out.
	 *
	 * @since 1.1.0
	 *
	 * @function
	 */
	Scraper.prototype.runScraper = function () {

		for ( var index = 0; index < this.elementsList.length; index++ ) {
			var elem = this.elementsList[ index ];

			this.addMarkupIdToClassAttribute( elem.dataset.markupId );

			// Whoops, an error occurred. Bail out.
			if ( this.errorOccurred ) {
				return true;
			}

			this.scrapedMarkupIds.push( elem.dataset.markupId );
		}
	}

	/**
	 * Find the elements and then add the markup ID to each element's class attribute.
	 * If an error occurs, it sets the error flag on the object and bails out.
	 *
	 * @since 1.1.0
	 *
	 * @param {string} markupId The target markup ID.
	 * @function
	 */
	Scraper.prototype.addMarkupIdToClassAttribute = function ( markupId ) {
		var elementsList = document.querySelectorAll( '[data-markup-id="' + markupId + '"]' );

		// Flag an error and bail out if no nodes exist.
		if ( elementsList.length < 1 ) {
			this.errorOccurred = true;
			return false;
		}

		// Add the markup ID to each element's class attribute.
		for ( var index = 0; index < elementsList.length; index++ ) {
			elementsList[ index ].className += ' ' + markupId;
		}
	}

	/**
	 * Send the scraped markup IDs back to the server.
	 *
	 * @since 1.1.0
	 *
	 * @function
	 */
	Scraper.prototype.sendToServer = function () {
		var data = {
			action  : 'bvhg_save_scraped_markup_ids',
			security: this.nonce,
			markup  : this.scrapedMarkupIds,
		};

		$.post( this.ajaxurl, data );
	}

	/**
	 * Launch the script when the document is ready.
	 *
	 * @since 1.1.0
	 *
	 * @function
	 */
	$( document ).ready(function () {
		// Bail out if the object not available from the server.
		if ( typeof scraperParams === 'undefined' ) {
			return;
		}

		new Scraper( scraperParams.ajaxurl, scraperParams.nonce );
	});

} )( jQuery, document );

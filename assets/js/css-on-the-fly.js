;(function( document ) {
    'use strict';

    /**
     * The constructor for our script.
     *
     * @since 1.1.0
     *
     * @param {array} classAttributes An array of class attributes to process.
     * @constructor
     */
    function CssOnTheFly( classAttributes ) {
        // If did not receive from server, bail out.
        if ( !classAttributes ) {
            return;
        }

        classAttributes.forEach( this.cssHandler, this );
    }

    /**
     * Handles adjusting the CSS on the fly.
     *
     * @since 1.1.0
     *
     * @param {string} The given class attribute.
     * @callback
     */
    CssOnTheFly.prototype.cssHandler = function( classAttribute ) {
        this.adjustStyle( classAttribute );
        this.setColor( classAttribute, 'yellow' );
    }

    /**
     * Adjust the CSS on the Fly.
     *
     * @since 1.1.0
     *
     * @param {string} The given class attribute.
     * @function
     */
    CssOnTheFly.prototype.adjustStyle = function( classAttribute ) {
        var elems = document.getElementsByClassName( classAttribute );

        // Bail out if no elements were found.
        if ( elems.length < 1 ) {
            return false;
        }

        // Apply the styles to each of the found elements.
        var styles = this.getStyle( classAttribute );
        for ( var index = 0; index < elems.length; index++ ) {
            elems[ index ].setAttribute( 'style', styles );
        }
    }

    /**
     * Sets the color on each element.
     *
     * @since 1.1.0
     *
     * @param {string} The given class attribute.
     * @param {string} The color to set.
     * @function
     */
    CssOnTheFly.prototype.setColor = function( classAttribute, colorToSet ) {
        var parentElem = this.getParent( classAttribute );

        // Bail out if the parent element does not exist.
        if ( false === parentElem ) {
            return false;
        }

        var elems = parentElem.getElementsByClassName( 'ab-item' );
        if ( elems.length < 1 ) {
            return false;
        }

        // Apply the color to all of the elements.
        for ( var index = 0; index < elems.length; index++ ) {
            elems[ index ].style.color = colorToSet;
        }
    }

    /**
     * Get the parent element.  The class attribute is part of the
     * unique part of the element's ID.
     *
     * @since 1.1.0
     *
     * @param {string} The given class attribute.
     * @function
     */
    CssOnTheFly.prototype.getParent = function( classAttribute ) {
        var parentElem = document.getElementById( 'wp-admin-bar-bvhg_html_' + classAttribute + 'hook' );

        // Bail out if the parent does not exist.
	    if ( typeof parentElem === 'undefined' || null === parentElem )  {
		    return false;
	    }

        return parentElem;
    }

    /**
     * Get the styles for the given class attribute.
     *
     * @since 1.1.0
     *
     * @param {string} The given class attribute.
     * @function
     */
    CssOnTheFly.prototype.getStyle = function( classAttribute ) {
        if ( 'beans_html' === classAttribute ) {
            return 'border: solid 1px orange; margin: 39px 8px 8px !important;';
        }

        if ( 'beans_search_form_input_icon' === classAttribute ) {
            return 'border: solid 1px orange; margin: 5px !important; position: inherit; width: auto;';
        }

        return 'border: solid 1px orange; margin: 5px !important;';
    }

    /**
     * Launch the script when the document is ready.
     *
     * @since 1.1.0
     *
     * @function
     */
    document.onreadystatechange = function() {
        if ( 'complete' !== document.readyState ) {
            return;
        }

        // Bail out if the object not available from the server.
        if ( typeof cssOnTheFlyParams === 'undefined' ) {
            return;
        }

        new CssOnTheFly(cssOnTheFlyParams.classNames);
    }

})( document );

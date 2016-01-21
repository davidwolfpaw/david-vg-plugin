(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

})( jQuery );



function pocketGenerateRequestToken( consumerKey ) {

    jQuery.ajax({
        type: 'POST',
        url: dvgAdmin.ajaxurl,
        data: {
            consumerKey: consumerKey,
            action: 'pocket_generate_request_token'
        },
        success: function(data, textStatus, XMLHttpRequest) {
        	// return data;
        	jQuery('#pocket_request_token').val(data);
        	// alert( data );
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

}


function pocketGenerateAccessToken( requestToken ) {

    jQuery.ajax({
        type: 'POST',
        url: dvgAdmin.ajaxurl,
        data: {
            requestToken: requestToken,
            action: 'pocket_generate_access_token'
        },
        success: function(data, textStatus, XMLHttpRequest) {
        	// return data;
        	// jQuery('#pocket_access_token').val(data);
        	alert( data );
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });

}
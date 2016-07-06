( function( jQuery ) {
	'use strict';

	// Form Handling Module
	var formHandling = {
		submitButton: null,
		spinner: null,
		errorMessage: null,
		form: null,
		btnNewSidebar: null,
		btnRemoveSidebar: null,
		customSidebarHolder1: null,
		customSidebarHolder2: null,
		ajaxRequest: null,
		newSidebarData: {
			action: 'add_new_custom_sidebar',
			security: window.cherryFramework.ajax_nonce_new_sidebar,
			formdata:{}
		},
		removeSidebarData: {
			action: 'remove_custom_sidebar',
			id: '',
			security: window.cherryFramework.ajax_nonce_remove_sidebar
		},

		// Init function
		init: function() {

			// Init variable
			var formHandling = this;

			formHandling.form = jQuery( '#cherry-sidebars-form' );
			formHandling.submitButton = jQuery( '#sidebar-manager-submit', formHandling.form );
			formHandling.spinner = jQuery( '.spinner-wordpress-type-1', formHandling.form );
			formHandling.errorMessage = jQuery( '#cherry-error-message', formHandling.form );
			formHandling.customSidebarHolder1 = jQuery( '#cherry-sidebars-holder .sidebars-column-1' );
			formHandling.customSidebarHolder2 = jQuery( '#cherry-sidebars-holder .sidebars-column-2' );
			formHandling.btnNewSidebar = jQuery( '.btn-create-sidebar.thickbox' );
			formHandling.btnRemoveSidebar = jQuery( '.cherry-delete-sidebar-manager' );

			// Added handler
			formHandling.submitButton.on( 'click', formHandling.submitByttonHandler );
			formHandling.btnNewSidebar.on( 'click', formHandling.openThickBox );
			formHandling.btnRemoveSidebar.on( 'click', formHandling.removeCustomSidebar );

			jQuery.ajaxSetup( {
				type: 'GET',
				url: window.ajaxurl,
				cache: false
			} );
		},
		openThickBox: function() {
			jQuery( 'input[type="text"]', formHandling.form ).removeClass( 'error-invalid' );
		},
		submitByttonHandler: function() {

			// Validated form
			var formData = formHandling.form.serializeArray(),
				key,
				object,
				input;

			for ( key in formData ) {
				object = formData[ key ];
				input = jQuery( 'input[name="' + object.name + '"]', formHandling.form );

				if ( ! object.value && input.hasClass( 'required' ) ) {
					input.addClass( 'error-invalid' );
				} else {
					input.removeClass( 'error-invalid' );
				}
			}

			if ( ! jQuery( '.error-invalid', formHandling.form )[0] ) {
				formHandling.newSidebarData.formdata = formData;
				formHandling.aJaxRequestNewSitebar();
			}

			return ! 1;
		},
		aJaxRequestNewSitebar: function() {

			// Add new sidebar aJax request
			formHandling.ajaxRequest = jQuery.ajax( {
				data: formHandling.newSidebarData,
				beforeSend: function() {
					formHandling.submitButton.attr( { 'disabled':true } );
					formHandling.spinner.css( { 'display':'block' } );

					if ( formHandling.ajaxRequest ) {
						formHandling.ajaxRequest.abort();
					}
				},
				success: function( response ) {
					var sidebarCounter1 = jQuery( '.widgets-holder-wrap', formHandling.customSidebarHolder1 ).length,
						sidebarCounter2 = jQuery( '.widgets-holder-wrap', formHandling.customSidebarHolder2 ).length,
						newSidebar = jQuery( response );

					if ( sidebarCounter1 <= sidebarCounter2 ) {
						formHandling.customSidebarHolder1.append( newSidebar );
					} else {
						formHandling.customSidebarHolder2.append( newSidebar );
					}

					jQuery( '.cherry-delete-sidebar-manager', newSidebar ).on( 'click', formHandling.removeCustomSidebar );

					jQuery( 'input[type="text"]', formHandling.form ).val( '' );
					jQuery( '.tb-close-icon' ).trigger( 'click' );

					jQuery( '#widgets-right .sidebar-name' ).unbind( 'click' );
					jQuery( '#widgets-left .sidebar-name' ).unbind( 'click' );
					jQuery( document.body ).unbind( 'click.widgets-toggle' );
					jQuery( '.widgets-chooser' ).off( 'click.widgets-chooser' ).off( 'keyup.widgets-chooser' );
					jQuery( '#available-widgets .widget .widget-title' ).off( 'click.widgets-chooser' );
					jQuery( '.widgets-chooser-sidebars' ).empty();

					if ( window.wpWidgets ) {
						window.wpWidgets.init();
					}
				},
				complete: function() {
					formHandling.spinner.delay( 200 ).css( { 'display':'none' } );
					formHandling.submitButton.attr( { 'disabled':false } );
				},
				error: function() {
					formHandling.errorMessage.css( { 'display':'block' } ).delay( 3000 ).fadeOut( 800, 0 );
				}
			} );
		},

		// Remove button handler
		removeCustomSidebar: function() {
			var customSidebar = jQuery( this ).parents( '.cherry-widgets-holder-wrap' );

			formHandling.removeSidebarData.id = jQuery( '.widgets-sortables', customSidebar ).attr( 'id' );

			formHandling.aJaxRequestremoveSitebar( customSidebar );
		},

		// Remove sidebar aJax request
		aJaxRequestremoveSitebar: function( sidebar ) {
			formHandling.ajaxRequest = jQuery.ajax( {
				data: formHandling.removeSidebarData,
				beforeSend: function() {
					jQuery( '.cherry-spinner-wordpress', sidebar ).css( { 'display':'block' } );
				},
				success: function() {
					sidebar.remove();
				}
			} );
		}
	};

	jQuery( document ).ready( function() {

		formHandling.init();

		// Add handler on sidebar wrapper title
		jQuery( 'body:not(.widgets_access) .sidebar-manager-name' ).on( 'click', function() {
			var _this = jQuery( this );

			jQuery( '~ .sidebars-holder', _this ).toggleClass( 'closed' );
			jQuery( '.sidebar-name-arrow', _this ).toggleClass( 'closed-arrow' );
		} );

	});
}( jQuery ) );

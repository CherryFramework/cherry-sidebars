( function( $ ) {
'use strict';

	// Form Handling Module
	var formHandling = {
		submitButton: null,
		spinner: null,
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

			formHandling.form = $( '#cherry-sidebars-form' );
			formHandling.submitButton = $( '#sidebar-manager-submit', formHandling.form );
			formHandling.spinner = $( '.spinner-wordpress-type-1', formHandling.form );
			formHandling.customSidebarHolder1 = $( '#cherry-sidebars-holder .sidebars-column-1' );
			formHandling.customSidebarHolder2 = $( '#cherry-sidebars-holder .sidebars-column-2' );
			formHandling.btnNewSidebar = $( '.btn-create-sidebar.thickbox' );
			formHandling.btnRemoveSidebar = $( '.cherry-delete-sidebar-manager' );

			// Added handler
			formHandling.submitButton.on( 'click', formHandling.submitByttonHandler );
			formHandling.btnNewSidebar.on( 'click', formHandling.openThickBox );
			formHandling.btnRemoveSidebar.on( 'click', formHandling.removeCustomSidebar );

			$.ajaxSetup( {
				type: 'GET',
				url: window.ajaxurl,
				cache: false
			} );
		},
		openThickBox: function() {
			$( 'input[type="text"]', formHandling.form ).removeClass( 'error-invalid' );
		},
		submitByttonHandler: function() {

			// Validated form
			var formData = formHandling.form.serializeArray(),
				key,
				object,
				input;

			for ( key in formData ) {
				object = formData[ key ];
				input = $( 'input[name="' + object.name + '"]', formHandling.form );

				if ( ! object.value ) {
					input.addClass( 'error-invalid' );
				} else {
					input.removeClass( 'error-invalid' );
				}
			}

			if ( ! $( '.error-invalid', formHandling.form )[0] ) {
				formHandling.newSidebarData.formdata = formData;
				formHandling.aJaxRequestNewSitebar();
			}

			return ! 1;
		},
		reInitWidgets: function() {
			$( '#widgets-right .sidebar-name' ).off( 'click' );
			$( '#widgets-left .sidebar-name' ).off( 'click' );
			$( 'body' ).off( 'click.widgets-toggle' );
			$( '.widgets-chooser' ).off( 'click.widgets-chooser' ).off( 'keyup.widgets-chooser' );
			$( '#available-widgets .widget .widget-title' ).off( 'click.widgets-chooser' );

			$( '.widgets-chooser-sidebars > li' ).remove();

			if ( window.wpWidgets ) {
				window.wpWidgets.hoveredSidebar = null;
				window.wpWidgets.init();
			}
		},
		aJaxRequestNewSitebar: function() {

			// Add new sidebar aJax request
			formHandling.ajaxRequest = $.ajax( {
				data: formHandling.newSidebarData,
				beforeSend: function() {
					formHandling.disableButton( formHandling.submitButton );

					if ( formHandling.ajaxRequest ) {
						formHandling.ajaxRequest.abort();
					}
				},
				success: function( response ) {
					var sidebarCounter1 = $( '.widgets-holder-wrap', formHandling.customSidebarHolder1 ).length,
						sidebarCounter2 = $( '.widgets-holder-wrap', formHandling.customSidebarHolder2 ).length,
						newSidebar = $( response );

					if ( sidebarCounter1 <= sidebarCounter2 ) {
						formHandling.customSidebarHolder1.append( newSidebar );
					} else {
						formHandling.customSidebarHolder2.append( newSidebar );
					}

					$( '.cherry-delete-sidebar-manager', newSidebar ).on( 'click', formHandling.removeCustomSidebar );

					$( 'input[type="text"]', formHandling.form ).val( '' );
					$( '.tb-close-icon' ).trigger( 'click' );

					formHandling.reInitWidgets();
				},
				complete: function() {
					formHandling.enableButton( formHandling.submitButton );
				}
			} );
		},

		// Remove button handler
		removeCustomSidebar: function() {
			var customSidebar = $( this ).parents( '.cherry-widgets-holder-wrap' );

			formHandling.removeSidebarData.id = $( '.widgets-sortables', customSidebar ).attr( 'id' );

			formHandling.requestRemoveSidebar( customSidebar );
		},

		// Remove sidebar aJax request
		requestRemoveSidebar: function( sidebar ) {
			formHandling.ajaxRequest = $.ajax( {
				data: formHandling.removeSidebarData,
				beforeSend: function() {
					$( '.cherry-loader-wrapper', sidebar ).addClass( 'show' );
				},
				success: function() {
					sidebar.remove();
					formHandling.reInitWidgets();
				}
			} );
		},
		disableButton: function( button ) {
			$( button )
				.attr( 'disabled', 'disabled' );
		},

		enableButton: function( button ) {
			$( button )
				.removeAttr( 'disabled' );
		}
	};

	$( document ).ready( function() {

		formHandling.init();

		// Add handler on sidebar wrapper title
		$( 'body:not(.widgets_access) .sidebar-manager-name' ).on( 'click', function() {
			var _this = $( this );

			$( '~ .sidebars-holder', _this ).toggleClass( 'closed' );
			$( '.sidebar-name-arrow', _this ).toggleClass( 'closed-arrow' );
		} );

	});
}( jQuery ) );

/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

;(function($, window, document, undefined) {
	'use strict';

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );

	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( to ) { 
				$( '#topmenu ul li a,header.right-menu a, #topmenu' ).css( {
					'color': to,
				} );
			}
		} );
	} );

	// Header Image
	wp.customize( 'header_image', function( value ) {
		value.bind( function( to ) {
			if ( to ) { 
				$( '.header_top_bg' ).css( {
					'background-image': ' url(' + to + ')',
				} );
				$( 'header, header.right-menu, #topmenu' ).css( {
					'background-color': 'transparent',
				} );
			}
		} );
	} );


} )( jQuery );
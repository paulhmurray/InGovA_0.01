/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	
	/**************************************
	*********** SERVICES SECTION **********
	***************************************/
	
	/* azera_shop_our_services_show */
	wp.customize( 'azera_shop_our_services_show', function( value ) {
		value.bind( function( to ) {
			if ( '1' != to ) {
				$( 'section.services' ).removeClass('azera_shop_only_customizer');
			} else {
				$( 'section.services' ).addClass('azera_shop_only_customizer');
			}
		} );
	} );
	
	/* azera_shop_our_services_title */
	wp.customize("azera_shop_our_services_title", function(value) {
        value.bind(function( to ) {
			if( to != '' ) {
				$( 'section#services h2' ).removeClass( 'azera_shop_only_customizer' );
			} else {
				$( 'section#services h2' ).addClass( 'azera_shop_only_customizer' );
			}
			$( 'section#services h2' ).text( to );
	    } );
    });
	
	/* azera_shop_our_services_subtitle */
	wp.customize("azera_shop_our_services_subtitle", function(value) {
        value.bind(function( to ) {
			if( to != '' ) {
				$( 'section#services div.sub-heading' ).removeClass( 'azera_shop_only_customizer' );
			} else {
				$( 'section#services div.sub-heading' ).addClass( 'azera_shop_only_customizer' );
			}
			$( 'section#services div.sub-heading' ).text( to );
	    } );
    });
	
	/**************************************
	************* TEAM SECTION ************
	***************************************/
	
	/* azera_shop_our_team_show */
	wp.customize( 'azera_shop_our_team_show', function( value ) {
		value.bind( function( to ) {
			if ( '1' != to ) {
				$( 'section.team' ).removeClass('azera_shop_only_customizer');
			} else {
				$( 'section.team' ).addClass('azera_shop_only_customizer');
			}
		} );
	} );
	
	/* azera_shop_our_team_title */
	wp.customize("azera_shop_our_team_title", function(value) {
        value.bind(function( to ) {
			if( to != '' ) {
				$( 'section#team h2' ).removeClass( 'azera_shop_only_customizer' );
			} else {
				$( 'section#team h2' ).addClass( 'azera_shop_only_customizer' );
			}
			$( 'section#team h2' ).text( to );
	    } );
    });
	
	/* azera_shop_our_team_subtitle */
	wp.customize("azera_shop_our_team_subtitle", function(value) {
        value.bind(function( to ) {
			if( to != '' ) {
				$( 'section#team div.sub-heading' ).removeClass( 'azera_shop_only_customizer' );
			} else {
				$( 'section#team div.sub-heading' ).addClass( 'azera_shop_only_customizer' );
			}
			$( 'section#team div.sub-heading' ).text( to );
	    } );
    });
	
	/**************************************
	********* TESTIMONIALS SECTION *********
	***************************************/
	
	/* azera_shop_happy_customers_show */
	wp.customize( 'azera_shop_happy_customers_show', function( value ) {
		value.bind( function( to ) {
			if ( '1' != to ) {
				$( 'section.testimonials' ).removeClass('azera_shop_only_customizer');
			} else {
				$( 'section.testimonials' ).addClass('azera_shop_only_customizer');
			}
		} );
	} );
	
	/* azera_shop_happy_customers_title */
	wp.customize("azera_shop_happy_customers_title", function(value) {
        value.bind(function( to ) {
			if( to != '' ) {
				$( 'section#customers h2' ).removeClass( 'azera_shop_only_customizer' );
			} else {
				$( 'section#customers h2' ).addClass( 'azera_shop_only_customizer' );
			}
			$( 'section#customers h2' ).text( to );
	    } );
    });
	
	/* azera_shop_happy_customers_subtitle */
	wp.customize("azera_shop_happy_customers_subtitle", function(value) {
        value.bind(function( to ) {
			if( to != '' ) {
				$( 'section#customers div.sub-heading' ).removeClass( 'azera_shop_only_customizer' );
			} else {
				$( 'section#customers div.sub-heading' ).addClass( 'azera_shop_only_customizer' );
			}
			$( 'section#customers div.sub-heading' ).text( to );
	    } );
    });
	
} )( jQuery );
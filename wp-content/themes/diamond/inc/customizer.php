<?php

function diamond_footer_text_register( $wp_customize ) {
	$wp_customize->add_section( 'coyright_footer' , array(
		'title' =>  __( 'Change Footer Text', 'diamond' ),
        'priority' => 200
	) );
    $wp_customize->add_setting(
        'copyright_textbox',
        array(
            'default' => '',
            'sanitize_callback' => 'diamond_sanitize_text',
        )
    );
    $wp_customize->add_control(
        'copyright_textbox',
        array(
            'label' => 'Copyright text',
            'section' => 'coyright_footer',
            'type' => 'text',
        )
    );
}
add_action( 'customize_register', 'diamond_footer_text_register' );
function diamond_sanitize_text( $input ) {
    return wp_kses_post( force_balance_tags( $input ) );
}

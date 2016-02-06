<?php
/**
 * Deli_Customizer Class
 * Makes adjustments to Storefront cores Customizer implementation.
 *
 * @author   WooThemes
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Deli_Customizer' ) ) {

class Deli_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_customizer_css' ), 	1000 );
		add_action( 'customize_register', array( $this, 'edit_default_settings' ), 	99 );
		add_action( 'customize_register', array( $this, 'edit_default_controls' ), 	99 );
	}

	/**
	 * Returns an array of the desired default Storefront options
	 * @return array
	 */
	public function get_deli_defaults() {
		return apply_filters( 'deli_default_settings', $args = array(
			'storefront_heading_color'					=> '#2b2b2b',
			'storefront_footer_heading_color'			=> '#ffffff',
			'storefront_header_background_color'		=> '#b64902',
			'storefront_footer_background_color'		=> '#2b2b2b',
			'storefront_header_link_color'				=> '#ffffff',
			'storefront_header_text_color'				=> '#ffffff',
			'storefront_button_background_color'		=> '#0e7784',
			'storefront_button_text_color'				=> '#ffffff',
			'storefront_button_alt_background_color'	=> '#b64902',
			'storefront_button_alt_text_color'			=> '#ffffff',
			'storefront_footer_link_color'				=> '#e4decd',
			'storefront_text_color'						=> '#615d59',
			'storefront_footer_text_color'				=> '#ffffff',
			'storefront_accent_color'					=> '#0e7784',
			'storefront_background_color'				=> '#645846',
		) );
	}

	/**
	 * Set default Customizer settings based on Storechild design.
	 * @uses get_deli_defaults()
	 * @return void
	 */
	public function edit_default_settings( $wp_customize ) {
		foreach ( Deli_Customizer::get_deli_defaults() as $mod => $val ) {
			$setting = $wp_customize->get_setting( $mod );

			if ( is_object( $setting ) ) {
				$setting->default = $val;
			}
		}
	}

	/**
	 * Modify the default controls
	 * @return void
	 */
	public function edit_default_controls( $wp_customize ) {
		$wp_customize->get_control( 'storefront_header_background_color' )->section 	= 'header_image';
		$wp_customize->get_setting( 'storefront_header_background_color' )->transport 	= 'refresh';
		$wp_customize->get_control( 'storefront_header_background_color' )->label 		= __( 'Navigation background color', 'deli' );

		if ( class_exists( 'Storefront_Designer' ) ) {
			/**
			 * Header Background
			 */
			$wp_customize->add_setting( 'deli_header_background_color', array(
				'default'           => '',
				'sanitize_callback' => 'storefront_sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'deli_header_background_color', array(
				'label'	   		=> __( 'Background color', 'deli' ),
				'description' 	=> __( 'For use with the Sticky Header option', 'deli' ),
				'section'  		=> 'header_image',
				'settings' 		=> 'deli_header_background_color',
				'priority' 		=> 75,
			) ) );
		}
	}

	/**
	 * Add CSS using settings obtained from the theme options.
	 * @return void
	 */
	public function add_customizer_css() {
		$header_text_color			= get_theme_mod( 'storefront_header_text_color' );
		$header_link_color			= get_theme_mod( 'storefront_header_link_color' );
		$navigation_bg_color		= get_theme_mod( 'storefront_header_background_color' );
		$accent_color				= get_theme_mod( 'storefront_accent_color' );
		$footer_link_color			= get_theme_mod( 'storefront_footer_link_color' );
		$footer_heading_color		= get_theme_mod( 'storefront_footer_heading_color' );
		$footer_text_color			= get_theme_mod( 'storefront_footer_text_color' );
		$button_background_color	= get_theme_mod( 'storefront_button_background_color' );
		$button_text_color			= get_theme_mod( 'storefront_button_text_color' );
		$header_bg_color			= get_theme_mod( 'deli_header_background_color' );

		$darken_factor				= -15;
		$lighten_factor				= 15;
		$style						= '
			.deli-primary-navigation {
				background:' . $navigation_bg_color . ';
			}

			.main-navigation ul li:hover > a,
			a.cart-contents:hover,
			.site-header-cart .widget_shopping_cart a:hover,
			.site-header-cart:hover > li > a {
				color: ' . storefront_adjust_color_brightness( $header_link_color, -50 ) . ';
			}

			.single-product div.product .summary .price {
				color: ' . $accent_color . ';
			}

			.header-widget-region {
				color: ' . $footer_text_color . ';
			}

			.header-widget-region a:not(.button) {
				color: ' . $footer_link_color . ';
			}

			.single-product div.product .summary .price {
				color: ' . $button_text_color . ';
				background-color: ' . $button_background_color . ';
			}

			.header-widget-region h1, .header-widget-region h2, .header-widget-region h3, .header-widget-region h4, .header-widget-region h5, .header-widget-region h6 {
				color: ' . $footer_heading_color . ';
			}

			.main-navigation ul li.smm-active li ul.products li.product h3,
			.main-navigation ul li.smm-active li ul.products li.product .price {
				color: ' . $header_text_color . ';
			}';

		if ( class_exists( 'Storefront_Designer' ) ) {
			$sticky 				= get_theme_mod( 'sd_header_sticky', 'default' );

			if ( 'sticky-header' == $sticky ) {
				$style .= '
					.site-header {
						background-color:' . $header_bg_color . ' !important;
					}
				';
			}
		}

		wp_add_inline_style( 'storefront-child-style', $style );
	}
}

}

return new Deli_Customizer();
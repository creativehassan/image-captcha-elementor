<?php
/**
 * Plugin Name:  Image Captcha for Elementor
 * Plugin URI:   https://github.com:creativehassan/image-captcha-elementor.git
 * Description:  A lightweight image-based captcha field for Elementor Pro forms with honeypot spam protection.
 * Version:      1.3.0
 * Author:       Hassan Ali | Coresol Studio
 * Author URI:   https://coresolstudio.com
 * License:      GNU General Public License v2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  image-captcha-elementor
 * Domain Path:  /languages
 * Requires PHP: 8.0
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EIC_VERSION', '1.3.0' );
define( 'EIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

use Elementor\Controls_Manager;

function eic_load_textdomain() {
	load_plugin_textdomain( 'image-captcha-elementor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'eic_load_textdomain' );

class Image_Captcha_Field {

	public static $type = 'image_captcha';

	private static $allowed_svg_html = array(
		'svg'     => array(
			'xmlns'       => true,
			'width'       => true,
			'height'      => true,
			'aria-hidden' => true,
			'role'        => true,
			'viewBox'     => true,
			'viewbox'     => true,
			'fill'        => true,
		),
		'path'    => array( 'd' => true, 'fill' => true ),
		'circle'  => array( 'cx' => true, 'cy' => true, 'r' => true ),
		'g'       => array(),
		'rect'    => array( 'fill' => true, 'height' => true, 'width' => true ),
		'polygon' => array( 'points' => true ),
	);

	public static function get_captcha_icons() {
		return array(
			__( 'Heart', 'image-captcha-elementor' )  => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>',
			__( 'House', 'image-captcha-elementor' )  => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>',
			__( 'Star', 'image-captcha-elementor' )   => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M0 0h24v24H0z" fill="none"/><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>',
			__( 'Camera', 'image-captcha-elementor' ) => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="3.2"/><path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/></svg>',
			__( 'Cup', 'image-captcha-elementor' )    => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.11 0 2-.9 2-2V5c0-1.11-.89-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/></svg>',
			__( 'Flag', 'image-captcha-elementor' )   => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6z"/></svg>',
			__( 'Key', 'image-captcha-elementor' )    => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 20 20" fill="#000000"><g><rect fill="none" height="20" width="20"/></g><g><path d="M17.5,8.5h-6.75C10.11,6.48,8.24,5,6,5c-2.76,0-5,2.24-5,5s2.24,5,5,5c2.24,0,4.11-1.48,4.75-3.5h0.75L13,13l1.5-1.5L16,13 l3-3L17.5,8.5z M6,12.5c-1.38,0-2.5-1.12-2.5-2.5S4.62,7.5,6,7.5S8.5,8.62,8.5,10S7.38,12.5,6,12.5z"/></g></svg>',
			__( 'Truck', 'image-captcha-elementor' )  => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>',
			__( 'Tree', 'image-captcha-elementor' )   => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 20 20" fill="#000000"><g><rect fill="none" height="20" width="20"/></g><g><polygon points="13,10 15,10 9.97,3 5,10 7,10 4,14 9,14 9,17 11.03,17 11.03,14 16,14"/></g></svg>',
			__( 'Plane', 'image-captcha-elementor' )  => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>',
			__( 'Lock', 'image-captcha-elementor' )   => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" aria-hidden="true" role="img" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>',
		);
	}

	public function __construct() {
		add_action( 'elementor_pro/forms/fields/register', [ $this, 'register_fields' ] );
		add_action( 'elementor/element/after_section_end', [ $this, 'add_form_field' ], 10, 3 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_eic_regenerate_captcha', [ $this, 'ajax_regenerate_captcha' ] );
		add_action( 'wp_ajax_nopriv_eic_regenerate_captcha', [ $this, 'ajax_regenerate_captcha' ] );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'image-captcha', EIC_PLUGIN_URL . 'assets/css/image-captcha.css', array(), EIC_VERSION );
		wp_enqueue_script( 'image-captcha', EIC_PLUGIN_URL . 'assets/js/image-captcha.js', array( 'jquery' ), EIC_VERSION, true );

		wp_localize_script( 'image-captcha', 'eicData', array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'eic_regenerate' ),
			'captchas'    => self::get_captcha_icons(),
			'captchaText' => __( 'Please prove you are human by selecting the', 'image-captcha-elementor' ),
		) );
	}

	/**
	 * Generate captcha HTML with server-side token storage.
	 *
	 * @param int $image_quantity Number of icons to display.
	 * @return string Captcha HTML.
	 */
	public static function generate_captcha_html( $image_quantity = 3 ) {
		$captchas       = self::get_captcha_icons();
		$image_quantity = max( 3, min( 10, (int) $image_quantity ) );
		$token          = wp_generate_password( 32, false );
		$choices        = array();
		$choice_keys    = (array) array_rand( $captchas, $image_quantity );

		foreach ( $choice_keys as $key ) {
			$choices[ $key ] = $captchas[ $key ];
		}

		$human_index   = wp_rand( 0, count( $choices ) - 1 );
		$correct_value = wp_generate_password( 16, false );

		set_transient( 'eic_captcha_' . $token, $correct_value, 300 );

		$choice_names = array_keys( $choices );

		$output  = '<span class="captcha-image">';
		$output .= '<span class="eic_instructions">';
		$output .= esc_html__( 'Please prove you are human by selecting the', 'image-captcha-elementor' );
		$output .= '&nbsp;<span class="choosen-icon">' . esc_html( $choice_names[ $human_index ] ) . '</span>';
		$output .= esc_html__( '.', 'image-captcha-elementor' ) . '</span>';
		$output .= '<span class="captcha-icon-section">';

		$i = 0;
		foreach ( $choices as $title => $image ) {
			$value   = ( $i === $human_index ) ? $correct_value : wp_generate_password( 16, false );
			$output .= '<label><input type="radio" name="eic_captcha" value="' . esc_attr( $value ) . '" />' . wp_kses( $image, self::$allowed_svg_html ) . '</label>';
			$i++;
		}

		$output .= '</span></span>';
		$output .= '<input type="hidden" name="eic_token" value="' . esc_attr( $token ) . '" />';

		return $output;
	}

	/**
	 * Validate a captcha submission against its server-side token.
	 *
	 * @param string $submitted_value The radio value the user selected.
	 * @param string $token           The token from the hidden field.
	 * @return bool True if valid.
	 */
	public static function validate_captcha_token( $submitted_value, $token ) {
		if ( empty( $token ) || empty( $submitted_value ) ) {
			return false;
		}

		$token        = sanitize_text_field( $token );
		$stored_value = get_transient( 'eic_captcha_' . $token );

		delete_transient( 'eic_captcha_' . $token );

		if ( false === $stored_value ) {
			return false;
		}

		return hash_equals( $stored_value, $submitted_value );
	}

	/**
	 * AJAX handler to regenerate captcha after form submit/error.
	 */
	public function ajax_regenerate_captcha() {
		check_ajax_referer( 'eic_regenerate', 'nonce' );

		$image_quantity = isset( $_POST['image_quantity'] ) ? absint( $_POST['image_quantity'] ) : 3;
		$html           = self::generate_captcha_html( $image_quantity );

		wp_send_json_success( array( 'html' => $html ) );
	}

	public function register_fields( $manager ) {
		require_once EIC_PLUGIN_DIR . 'image-captcha-field-widget.php';
		$manager->register( new \Image_Captcha_Field_Widget() );
	}

	public function add_form_field( $element, $section_id, $args ) {
		if ( 'section_steps_style' !== $section_id ) {
			return;
		}

		$element->start_controls_section(
			'section_image_captcha_style',
			[
				'label' => esc_html__( 'Image Captcha', 'image-captcha-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		/* ── Container ────────────────────────────────────── */

		$element->add_control(
			'eic_heading_container',
			[
				'label' => esc_html__( 'Container', 'image-captcha-elementor' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$element->add_responsive_control(
			'eic_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'image-captcha-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'image-captcha-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'image-captcha-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .eic-form-control-wrap.eic_captcha' => 'text-align: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'eic_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .captcha-image' => 'background-color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'eic_container_border_style',
			[
				'label'     => esc_html__( 'Border Style', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''       => esc_html__( 'None', 'image-captcha-elementor' ),
					'solid'  => esc_html__( 'Solid', 'image-captcha-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'image-captcha-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'image-captcha-elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .captcha-image' => 'border-style: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'eic_container_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .captcha-image' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'eic_container_border_style!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'eic_container_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'image-captcha-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 1, 'max' => 10 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .captcha-image' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'eic_container_border_style!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'eic_container_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'image-captcha-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .captcha-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'eic_container_padding',
			[
				'label'      => esc_html__( 'Padding', 'image-captcha-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .captcha-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		/* ── Question Text ────────────────────────────────── */

		$element->add_control(
			'eic_heading_question',
			[
				'label'     => esc_html__( 'Question Text', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$element->add_control(
			'eic_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .eic_instructions' => 'color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'eic_highlight_color',
			[
				'label'     => esc_html__( 'Highlight Color', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .choosen-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$element->add_responsive_control(
			'eic_text_font_size',
			[
				'label'      => esc_html__( 'Font Size', 'image-captcha-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [ 'min' => 10, 'max' => 40 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .eic_instructions' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		/* ── Icons ────────────────────────────────────────── */

		$element->add_control(
			'eic_heading_icons',
			[
				'label'     => esc_html__( 'Icons', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$element->add_responsive_control(
			'eic_icon_size',
			[
				'label'      => esc_html__( 'Size', 'image-captcha-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [ 'min' => 20, 'max' => 120 ],
					'em' => [ 'min' => 1, 'max' => 8 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .captcha-image svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .captcha-image label > input[type="radio"]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'eic_icon_gap',
			[
				'label'      => esc_html__( 'Gap', 'image-captcha-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 40 ],
					'em' => [ 'min' => 0, 'max' => 3 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .captcha-icon-section' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'eic_icon_padding',
			[
				'label'      => esc_html__( 'Icon Padding', 'image-captcha-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .captcha-image label > input + svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->add_control(
			'eic_icon_color',
			[
				'label'     => esc_html__( 'Color', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .captcha-image svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'eic_icon_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .captcha-image label:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		/* ── Selected Icon ────────────────────────────────── */

		$element->add_control(
			'eic_heading_selected',
			[
				'label'     => esc_html__( 'Selected State', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$element->add_control(
			'eic_selected_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'image-captcha-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .captcha-image label > input:checked + svg, {{WRAPPER}} .captcha-image label > input:focus + svg' => 'border-color: {{VALUE}};',
				],
			]
		);

		$element->add_responsive_control(
			'eic_selected_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'image-captcha-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 1, 'max' => 10 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .captcha-image label > input:checked + svg, {{WRAPPER}} .captcha-image label > input:focus + svg' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'eic_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'image-captcha-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .captcha-image label > input + svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->end_controls_section();
	}

	public function print_template( $template_content, $object ) {
		if ( 'form' === $object->get_name() ) {
			$template_content = '';
		}
		return $template_content;
	}
}

new Image_Captcha_Field();

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorPro\Plugin;

class Image_Captcha_Field_Widget extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	public function get_type() {
		return 'image_captcha';
	}

	public function get_name() {
		return esc_html__( 'Image Captcha', 'image-captcha-elementor' );
	}

	/**
	 * @param Widget_Base $widget
	 */
	public function update_controls( $widget ) {
		$elementor = Plugin::elementor();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = [
			'image_quantity'      => [
				'name'         => 'image_quantity',
				'label'        => esc_html__( 'Total Captcha Images', 'image-captcha-elementor' ),
				'type'         => Controls_Manager::NUMBER,
				'default'      => 3,
				'min'          => 3,
				'max'          => 10,
				'separator'    => 'before',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'description'  => esc_html__( 'Number of icons displayed in the captcha challenge.', 'image-captcha-elementor' ),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'eic_hide_initially' => [
				'name'         => 'eic_hide_initially',
				'label'        => esc_html__( 'Hide Until Interaction', 'image-captcha-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'description'  => esc_html__( 'Keep the captcha hidden until the user starts filling in the form.', 'image-captcha-elementor' ),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
		];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );
		$widget->update_control( 'form_fields', $control_data );
	}

	public function add_preview_depends() {
		wp_enqueue_script(
			'image-captcha-preview',
			EIC_PLUGIN_URL . 'assets/js/image-captcha-preview.js',
			[ 'jquery' ],
			EIC_VERSION,
			true
		);
	}

	/**
	 * Render captcha field on the frontend.
	 */
	public function render( $item, $item_index, $form ) {
		$image_quantity  = ! empty( $item['image_quantity'] ) ? (int) $item['image_quantity'] : 3;
		$hide_initially  = ! empty( $item['eic_hide_initially'] ) && 'yes' === $item['eic_hide_initially'];
		$output          = Image_Captcha_Field::generate_captcha_html( $image_quantity );

		$wrapper_classes = 'eic-form-control-wrap eic_captcha';
		if ( $hide_initially ) {
			$wrapper_classes .= ' eic-captcha--hidden';
		}

		$form->add_render_attribute( 'input' . $item_index, 'type', 'hidden', true );
		$form->add_render_attribute( 'input' . $item_index, 'name', 'eic_honeypot', true );
		?>
		<input <?php $form->print_render_attribute_string( 'input' . $item_index ); ?>>
		<div class="<?php echo esc_attr( $wrapper_classes ); ?>" data-name="eic_captcha" data-quantity="<?php echo esc_attr( $image_quantity ); ?>">
			<span class="eic-form-control eic-radio"><?php echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in generate_captcha_html ?></span>
		</div>
		<?php
	}

	/**
	 * Validate the captcha submission server-side.
	 */
	public function validation( $field, $record, $ajax_handler ) {
		$captcha_value = isset( $_POST['eic_captcha'] ) ? sanitize_text_field( wp_unslash( $_POST['eic_captcha'] ) ) : '';
		$captcha_token = isset( $_POST['eic_token'] ) ? sanitize_text_field( wp_unslash( $_POST['eic_token'] ) ) : '';
		$honeypot      = isset( $_POST['eic_honeypot'] ) ? sanitize_text_field( wp_unslash( $_POST['eic_honeypot'] ) ) : '';

		if ( ! empty( $honeypot ) ) {
			$ajax_handler->add_error( $field['id'], esc_html__( 'Spam detected.', 'image-captcha-elementor' ) );
			return;
		}

		if ( empty( $captcha_value ) ) {
			$ajax_handler->add_error( $field['id'], esc_html__( 'Please select an icon.', 'image-captcha-elementor' ) );
			return;
		}

		if ( ! Image_Captcha_Field::validate_captcha_token( $captcha_value, $captcha_token ) ) {
			$ajax_handler->add_error( $field['id'], esc_html__( 'Please select the correct icon.', 'image-captcha-elementor' ) );
		}
	}
}

<?php
/**
 * Settings module for EDD Floating Cart.
 *
 * @package EDDFloatingCart
 */

namespace EDDFloatingCart;

defined( 'ABSPATH' ) || exit;

/**
 * Returns the option name used to store plugin settings.
 *
 * @return string
 */
function get_settings_option_name() {
	return 'edd_floating_cart_settings';
}

/**
 * Returns the settings page slug.
 *
 * @return string
 */
function get_settings_page_slug() {
	return 'edd-floating-cart';
}

/**
 * Returns the current settings page hook suffix.
 *
 * @return string
 */
function get_settings_page_hook_suffix() {
	return 'settings_page_' . get_settings_page_slug();
}

/**
 * Returns the default plugin configuration.
 *
 * @return array<string, mixed>
 */
function get_plugin_config_defaults() {
	$defaults = array(
		'enabled'                  => 1,
		'position'                 => 'bottom-right',
		'horizontal_offset'        => 20,
		'vertical_offset'          => 20,
		'empty_cart_display'       => 'icon-only',
		'icon_type'                => 'default',
		'custom_svg_attachment_id' => 0,
		'custom_svg_url'           => '',
		'custom_image_attachment_id' => 0,
		'custom_image_url'         => '',
		'display_on_all_pages'     => 1,
		'hide_checkout'            => 1,
		'hide_success'             => 1,
	);

	/**
	 * Filters the default floating cart settings.
	 *
	 * @param array<string, mixed> $defaults Default settings.
	 */
	return apply_filters( 'edd_floating_cart_defaults', $defaults );
}

/**
 * Returns the resolved plugin configuration.
 *
 * @return array<string, mixed>
 */
function get_plugin_config() {
	$stored_settings = get_option( get_settings_option_name(), array() );

	if ( ! is_array( $stored_settings ) ) {
		$stored_settings = array();
	}

	$config = wp_parse_args( $stored_settings, get_plugin_config_defaults() );

	/**
	 * Filters the resolved plugin configuration.
	 *
	 * @param array<string, mixed> $config Resolved configuration.
	 */
	return apply_filters( 'edd_floating_cart_config', $config );
}

/**
 * Returns a single plugin setting.
 *
 * @param string $key Setting key.
 * @return mixed|null
 */
function get_plugin_setting( $key ) {
	$config = get_plugin_config();

	return array_key_exists( $key, $config ) ? $config[ $key ] : null;
}

/**
 * Registers admin hooks for the settings UI.
 *
 * @return void
 */
function register_settings() {
	add_action( 'admin_menu', __NAMESPACE__ . '\register_settings_page' );
	add_action( 'admin_init', __NAMESPACE__ . '\register_plugin_settings' );
}

/**
 * Registers the settings page.
 *
 * @return void
 */
function register_settings_page() {
	add_options_page(
		__( 'EDD Floating Cart', 'edd-floating-cart' ),
		__( 'EDD Floating Cart', 'edd-floating-cart' ),
		'manage_options',
		get_settings_page_slug(),
		__NAMESPACE__ . '\render_settings_page'
	);
}

/**
 * Registers the plugin settings schema and fields.
 *
 * @return void
 */
function register_plugin_settings() {
	register_setting(
		'edd_floating_cart_settings_group',
		get_settings_option_name(),
		array(
			'type'              => 'array',
			'sanitize_callback' => __NAMESPACE__ . '\sanitize_plugin_settings',
			'default'           => get_plugin_config_defaults(),
		)
	);

	add_settings_section(
		'edd_floating_cart_general',
		__( 'Floating Cart Settings', 'edd-floating-cart' ),
		__NAMESPACE__ . '\render_settings_section_intro',
		get_settings_page_slug()
	);

	add_settings_field(
		'enabled',
		__( 'Enable Floating Cart', 'edd-floating-cart' ),
		__NAMESPACE__ . '\render_enabled_field',
		get_settings_page_slug(),
		'edd_floating_cart_general'
	);

	add_settings_field(
		'position',
		__( 'Cart Position', 'edd-floating-cart' ),
		__NAMESPACE__ . '\render_position_field',
		get_settings_page_slug(),
		'edd_floating_cart_general'
	);

	add_settings_field(
		'horizontal_offset',
		__( 'Horizontal Offset', 'edd-floating-cart' ),
		__NAMESPACE__ . '\render_horizontal_offset_field',
		get_settings_page_slug(),
		'edd_floating_cart_general'
	);

	add_settings_field(
		'vertical_offset',
		__( 'Vertical Offset', 'edd-floating-cart' ),
		__NAMESPACE__ . '\render_vertical_offset_field',
		get_settings_page_slug(),
		'edd_floating_cart_general'
	);

	add_settings_field(
		'empty_cart_display',
		__( 'When Cart Quantity = 0', 'edd-floating-cart' ),
		__NAMESPACE__ . '\render_empty_cart_display_field',
		get_settings_page_slug(),
		'edd_floating_cart_general'
	);

	add_settings_field(
		'icon_type',
		__( 'Cart Icon', 'edd-floating-cart' ),
		__NAMESPACE__ . '\render_icon_field',
		get_settings_page_slug(),
		'edd_floating_cart_general'
	);

	add_settings_field(
		'display_rules',
		__( 'Display On', 'edd-floating-cart' ),
		__NAMESPACE__ . '\render_display_rules_field',
		get_settings_page_slug(),
		'edd_floating_cart_general'
	);
}

/**
 * Renders the settings page.
 *
 * @return void
 */
function render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'EDD Floating Cart', 'edd-floating-cart' ); ?></h1>
		<?php settings_errors( get_settings_option_name() ); ?>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'edd_floating_cart_settings_group' );
			do_settings_sections( get_settings_page_slug() );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Renders the section intro text.
 *
 * @return void
 */
function render_settings_section_intro() {
	echo '<p>' . esc_html__( 'Configure the floating cart appearance and visibility from one place.', 'edd-floating-cart' ) . '</p>';
}

/**
 * Renders the enabled field.
 *
 * @return void
 */
function render_enabled_field() {
	render_checkbox_field(
		'enabled',
		__( 'Enabled', 'edd-floating-cart' ),
		__( 'Disable to stop both frontend rendering and asset loading.', 'edd-floating-cart' )
	);
}

/**
 * Renders the position field.
 *
 * @return void
 */
function render_position_field() {
	$current_value = (string) get_plugin_setting( 'position' );
	$options       = array(
		'top-left'     => __( 'Top Left', 'edd-floating-cart' ),
		'top-right'    => __( 'Top Right', 'edd-floating-cart' ),
		'bottom-left'  => __( 'Bottom Left', 'edd-floating-cart' ),
		'bottom-right' => __( 'Bottom Right', 'edd-floating-cart' ),
	);
	?>
	<select name="<?php echo esc_attr( get_settings_option_name() ); ?>[position]">
		<?php foreach ( $options as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_value, $value ); ?>>
				<?php echo esc_html( $label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<p class="description"><?php esc_html_e( 'Controls which corner the floating cart uses.', 'edd-floating-cart' ); ?></p>
	<?php
}

/**
 * Renders the horizontal offset field.
 *
 * @return void
 */
function render_horizontal_offset_field() {
	render_number_field(
		'horizontal_offset',
		__( 'px', 'edd-floating-cart' ),
		__( 'Distance from the left or right viewport edge.', 'edd-floating-cart' )
	);
}

/**
 * Renders the vertical offset field.
 *
 * @return void
 */
function render_vertical_offset_field() {
	render_number_field(
		'vertical_offset',
		__( 'px', 'edd-floating-cart' ),
		__( 'Distance from the top or bottom viewport edge.', 'edd-floating-cart' )
	);
}

/**
 * Renders the empty cart behavior field.
 *
 * @return void
 */
function render_empty_cart_display_field() {
	$current_value = (string) get_plugin_setting( 'empty_cart_display' );
	$options       = array(
		'icon-only' => __( 'Show icon only', 'edd-floating-cart' ),
		'hide-cart' => __( 'Hide floating cart', 'edd-floating-cart' ),
	);

	foreach ( $options as $value => $label ) {
		render_radio_option( 'empty_cart_display', $value, $label, $current_value );
	}

	echo '<p class="description">' . esc_html__( 'Controls how the cart behaves when the EDD cart is empty.', 'edd-floating-cart' ) . '</p>';
}

/**
 * Renders the icon field.
 *
 * @return void
 */
function render_icon_field() {
	$current_type = (string) get_plugin_setting( 'icon_type' );
	$svg_url      = (string) get_plugin_setting( 'custom_svg_url' );
	$image_url    = (string) get_plugin_setting( 'custom_image_url' );
	$svg_id       = (int) get_plugin_setting( 'custom_svg_attachment_id' );
	$image_id     = (int) get_plugin_setting( 'custom_image_attachment_id' );

	render_radio_option( 'icon_type', 'default', __( 'Default cart icon', 'edd-floating-cart' ), $current_type );
	render_radio_option( 'icon_type', 'custom-svg', __( 'Upload custom SVG', 'edd-floating-cart' ), $current_type );
	render_media_control(
		'custom_svg_attachment_id',
		'custom_svg_url',
		$svg_id,
		$svg_url,
		__( 'Choose SVG', 'edd-floating-cart' ),
		__( 'Remove SVG', 'edd-floating-cart' )
	);

	render_radio_option( 'icon_type', 'custom-image', __( 'Upload custom image', 'edd-floating-cart' ), $current_type );
	render_media_control(
		'custom_image_attachment_id',
		'custom_image_url',
		$image_id,
		$image_url,
		__( 'Choose Image', 'edd-floating-cart' ),
		__( 'Remove Image', 'edd-floating-cart' ),
		'image'
	);

	echo '<p class="description">' . esc_html__( 'A valid custom icon will replace the fallback cart icon. If the selected media is invalid, the default icon is used automatically.', 'edd-floating-cart' ) . '</p>';
}

/**
 * Renders the display rules field.
 *
 * @return void
 */
function render_display_rules_field() {
	render_checkbox_field( 'display_on_all_pages', __( 'All Pages', 'edd-floating-cart' ) );
	render_checkbox_field( 'hide_checkout', __( 'Hide Checkout', 'edd-floating-cart' ) );
	render_checkbox_field( 'hide_success', __( 'Hide Purchase Confirmation', 'edd-floating-cart' ) );

	echo '<p class="description">' . esc_html__( 'Hide rules rely on Easy Digital Downloads page detection helpers.', 'edd-floating-cart' ) . '</p>';
}

/**
 * Enqueues the settings page assets.
 *
 * @param string $hook_suffix Current admin hook suffix.
 * @return void
 */
function enqueue_settings_assets( $hook_suffix ) {
	if ( get_settings_page_hook_suffix() !== $hook_suffix ) {
		return;
	}

	wp_enqueue_media();

	wp_enqueue_script(
		'edd-floating-cart-admin-settings',
		EDD_FLOATING_CART_URL . 'assets/js/admin-settings.js',
		array( 'jquery' ),
		get_version(),
		true
	);
}

/**
 * Sanitizes plugin settings before save.
 *
 * @param mixed $input Submitted settings.
 * @return array<string, mixed>
 */
function sanitize_plugin_settings( $input ) {
	$defaults = get_plugin_config_defaults();
	$input    = is_array( $input ) ? $input : array();

	$settings = array(
		'enabled'                    => ! empty( $input['enabled'] ) ? 1 : 0,
		'position'                   => sanitize_position_value( $input['position'] ?? $defaults['position'] ),
		'horizontal_offset'          => sanitize_offset_value( $input['horizontal_offset'] ?? $defaults['horizontal_offset'] ),
		'vertical_offset'            => sanitize_offset_value( $input['vertical_offset'] ?? $defaults['vertical_offset'] ),
		'empty_cart_display'         => sanitize_empty_cart_display_value( $input['empty_cart_display'] ?? $defaults['empty_cart_display'] ),
		'icon_type'                  => sanitize_icon_type_value( $input['icon_type'] ?? $defaults['icon_type'] ),
		'custom_svg_attachment_id'   => 0,
		'custom_svg_url'             => '',
		'custom_image_attachment_id' => 0,
		'custom_image_url'           => '',
		'display_on_all_pages'       => ! empty( $input['display_on_all_pages'] ) ? 1 : 0,
		'hide_checkout'              => ! empty( $input['hide_checkout'] ) ? 1 : 0,
		'hide_success'               => ! empty( $input['hide_success'] ) ? 1 : 0,
	);

	$svg_media = sanitize_media_value(
		$input['custom_svg_attachment_id'] ?? 0,
		$input['custom_svg_url'] ?? '',
		'custom-svg'
	);

	$image_media = sanitize_media_value(
		$input['custom_image_attachment_id'] ?? 0,
		$input['custom_image_url'] ?? '',
		'custom-image'
	);

	$settings['custom_svg_attachment_id']   = $svg_media['attachment_id'];
	$settings['custom_svg_url']             = $svg_media['url'];
	$settings['custom_image_attachment_id'] = $image_media['attachment_id'];
	$settings['custom_image_url']           = $image_media['url'];

	if ( 'custom-svg' === $settings['icon_type'] && empty( $settings['custom_svg_url'] ) ) {
		$settings['icon_type'] = 'default';
		add_settings_error(
			get_settings_option_name(),
			'edd-floating-cart-invalid-svg',
			__( 'The selected SVG icon was invalid, so the default cart icon will be used instead.', 'edd-floating-cart' ),
			'warning'
		);
	}

	if ( 'custom-image' === $settings['icon_type'] && empty( $settings['custom_image_url'] ) ) {
		$settings['icon_type'] = 'default';
		add_settings_error(
			get_settings_option_name(),
			'edd-floating-cart-invalid-image',
			__( 'The selected image icon was invalid, so the default cart icon will be used instead.', 'edd-floating-cart' ),
			'warning'
		);
	}

	return $settings;
}

/**
 * Sanitizes a position value.
 *
 * @param mixed $value Raw position value.
 * @return string
 */
function sanitize_position_value( $value ) {
	$value = is_string( $value ) ? $value : '';

	return in_array( $value, get_allowed_positions(), true ) ? $value : 'bottom-right';
}

/**
 * Sanitizes an offset value.
 *
 * @param mixed $value Raw offset value.
 * @return int
 */
function sanitize_offset_value( $value ) {
	return max( 0, absint( $value ) );
}

/**
 * Sanitizes the empty cart display value.
 *
 * @param mixed $value Raw display mode.
 * @return string
 */
function sanitize_empty_cart_display_value( $value ) {
	$value         = is_string( $value ) ? $value : '';
	$allowed_modes = array(
		'icon-only',
		'hide-cart',
	);

	return in_array( $value, $allowed_modes, true ) ? $value : 'icon-only';
}

/**
 * Sanitizes the icon type value.
 *
 * @param mixed $value Raw icon type.
 * @return string
 */
function sanitize_icon_type_value( $value ) {
	$value         = is_string( $value ) ? $value : '';
	$allowed_types = array(
		'default',
		'custom-svg',
		'custom-image',
	);

	return in_array( $value, $allowed_types, true ) ? $value : 'default';
}

/**
 * Sanitizes media values for icon uploads.
 *
 * @param mixed  $attachment_id Attachment ID.
 * @param mixed  $url           Attachment URL.
 * @param string $media_type    Media type.
 * @return array{attachment_id:int,url:string}
 */
function sanitize_media_value( $attachment_id, $url, $media_type ) {
	$attachment_id = absint( $attachment_id );
	$url           = esc_url_raw( is_string( $url ) ? $url : '' );

	if ( $attachment_id > 0 ) {
		$resolved_url = wp_get_attachment_url( $attachment_id );

		if ( is_string( $resolved_url ) && validate_icon_url( $resolved_url, $media_type, $attachment_id ) ) {
			return array(
				'attachment_id' => $attachment_id,
				'url'           => esc_url_raw( $resolved_url ),
			);
		}
	}

	if ( validate_icon_url( $url, $media_type ) ) {
		return array(
			'attachment_id' => 0,
			'url'           => $url,
		);
	}

	return array(
		'attachment_id' => 0,
		'url'           => '',
	);
}

/**
 * Validates a media URL for icon usage.
 *
 * @param string   $url           Media URL.
 * @param string   $media_type    Media type.
 * @param int|null $attachment_id Optional attachment ID.
 * @return bool
 */
function validate_icon_url( $url, $media_type, $attachment_id = null ) {
	if ( empty( $url ) ) {
		return false;
	}

	$file_type = wp_check_filetype( $url );
	$extension = isset( $file_type['ext'] ) ? strtolower( (string) $file_type['ext'] ) : '';

	if ( 'custom-svg' === $media_type ) {
		if ( 'svg' === $extension ) {
			return true;
		}

		if ( $attachment_id ) {
			$mime = get_post_mime_type( $attachment_id );

			return 'image/svg+xml' === $mime;
		}

		return false;
	}

	if ( 'custom-image' === $media_type ) {
		if ( $attachment_id ) {
			return wp_attachment_is_image( $attachment_id );
		}

		return in_array( $extension, array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif' ), true );
	}

	return false;
}

/**
 * Renders a checkbox field.
 *
 * @param string      $key         Setting key.
 * @param string      $label       Checkbox label.
 * @param string|null $description Optional description text.
 * @return void
 */
function render_checkbox_field( $key, $label, $description = null ) {
	$value = ! empty( get_plugin_setting( $key ) );
	?>
	<label>
		<input
			type="checkbox"
			name="<?php echo esc_attr( get_settings_option_name() ); ?>[<?php echo esc_attr( $key ); ?>]"
			value="1"
			<?php checked( $value ); ?>
		/>
		<?php echo esc_html( $label ); ?>
	</label>
	<?php if ( ! empty( $description ) ) : ?>
		<p class="description"><?php echo esc_html( $description ); ?></p>
	<?php endif; ?>
	<?php
}

/**
 * Renders a number field.
 *
 * @param string $key         Setting key.
 * @param string $suffix      Unit suffix.
 * @param string $description Description text.
 * @return void
 */
function render_number_field( $key, $suffix, $description ) {
	$value = absint( get_plugin_setting( $key ) );
	?>
	<input
		type="number"
		min="0"
		step="1"
		name="<?php echo esc_attr( get_settings_option_name() ); ?>[<?php echo esc_attr( $key ); ?>]"
		value="<?php echo esc_attr( $value ); ?>"
	/>
	<span><?php echo esc_html( $suffix ); ?></span>
	<p class="description"><?php echo esc_html( $description ); ?></p>
	<?php
}

/**
 * Renders a radio option.
 *
 * @param string $key          Setting key.
 * @param string $value        Option value.
 * @param string $label        Option label.
 * @param string $current_value Current selected value.
 * @return void
 */
function render_radio_option( $key, $value, $label, $current_value ) {
	?>
	<label style="display:block;margin-bottom:6px;">
		<input
			type="radio"
			name="<?php echo esc_attr( get_settings_option_name() ); ?>[<?php echo esc_attr( $key ); ?>]"
			value="<?php echo esc_attr( $value ); ?>"
			<?php checked( $current_value, $value ); ?>
		/>
		<?php echo esc_html( $label ); ?>
	</label>
	<?php
}

/**
 * Renders a media selection control.
 *
 * @param string      $attachment_key Attachment ID key.
 * @param string      $url_key        Attachment URL key.
 * @param int         $attachment_id  Attachment ID.
 * @param string      $url            Attachment URL.
 * @param string      $button_label   Upload button label.
 * @param string      $remove_label   Remove button label.
 * @param string|null $library_type   Optional media library type.
 * @return void
 */
function render_media_control( $attachment_key, $url_key, $attachment_id, $url, $button_label, $remove_label, $library_type = null ) {
	$preview_style = empty( $url ) ? 'display:none;' : '';
	?>
	<div class="edd-floating-cart-media-control" style="margin:8px 0 14px 24px;">
		<input
			type="hidden"
			class="edd-floating-cart-media-id"
			name="<?php echo esc_attr( get_settings_option_name() ); ?>[<?php echo esc_attr( $attachment_key ); ?>]"
			value="<?php echo esc_attr( $attachment_id ); ?>"
		/>
		<input
			type="url"
			class="regular-text edd-floating-cart-media-url"
			name="<?php echo esc_attr( get_settings_option_name() ); ?>[<?php echo esc_attr( $url_key ); ?>]"
			value="<?php echo esc_attr( $url ); ?>"
			readonly
		/>
		<button
			type="button"
			class="button edd-floating-cart-media-upload"
			<?php if ( ! empty( $library_type ) ) : ?>
				data-library-type="<?php echo esc_attr( $library_type ); ?>"
			<?php endif; ?>
		>
			<?php echo esc_html( $button_label ); ?>
		</button>
		<button type="button" class="button-link-delete edd-floating-cart-media-remove">
			<?php echo esc_html( $remove_label ); ?>
		</button>
		<div class="edd-floating-cart-media-preview" style="<?php echo esc_attr( $preview_style ); ?>margin-top:8px;">
			<img src="<?php echo esc_url( $url ); ?>" alt="" style="max-width:80px;height:auto;" />
		</div>
	</div>
	<?php
}

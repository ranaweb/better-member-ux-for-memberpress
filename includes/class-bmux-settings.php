<?php
/**
 * Plugin settings.
 *
 * @package BetterMemberUXForMemberPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders the Better Member UX settings page.
 */
class BMUX_Settings {

	const OPTION_NAME = 'bmux_options';

	/**
	 * Register WordPress hooks.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Return translated defaults.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'enabled'                 => 1,
			'enable_account'          => 1,
			'rename_home'             => 1,
			'home_label'              => __( 'Profile', 'better-member-ux-for-memberpress' ),
			'add_dashboard'           => 1,
			'dashboard_label'         => __( 'Dashboard', 'better-member-ux-for-memberpress' ),
			'dashboard_url'           => '/dashboard/',
			'enable_login'            => 1,
			'enable_password_reset'   => 1,
			'login_heading'           => __( 'Member Login', 'better-member-ux-for-memberpress' ),
			'login_helper'            => __( 'Sign in to access your membership details and account tools.', 'better-member-ux-for-memberpress' ),
			'password_reset_helper'   => __( "Enter the username or email address connected to your account. We'll send instructions to reset your password.", 'better-member-ux-for-memberpress' ),
			'login_url'               => '/login/',
			'primary_color'           => '#006f80',
			'accent_color'            => '#008da0',
			'background_color'        => '#f6f8f9',
			'border_color'            => '#ccd7dd',
		);
	}

	/**
	 * Return saved settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_options() {
		$options = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		return wp_parse_args( $options, self::get_defaults() );
	}

	/**
	 * Add Settings > Better Member UX.
	 */
	public function add_settings_page() {
		add_options_page(
			esc_html__( 'Better Member UX for MemberPress', 'better-member-ux-for-memberpress' ),
			esc_html__( 'Better Member UX', 'better-member-ux-for-memberpress' ),
			'manage_options',
			'better-member-ux',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register the option, sections, and fields.
	 */
	public function register_settings() {
		register_setting(
			'bmux_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => self::get_defaults(),
			)
		);

		$this->add_section(
			'bmux_general',
			__( 'General', 'better-member-ux-for-memberpress' ),
			__( 'Turn the enhancement layer on or off without deactivating the plugin.', 'better-member-ux-for-memberpress' )
		);
		$this->add_field( 'enabled', __( 'Enable enhancements', 'better-member-ux-for-memberpress' ), 'checkbox', 'bmux_general', __( 'Enable Better Member UX enhancements', 'better-member-ux-for-memberpress' ) );

		$this->add_section(
			'bmux_account',
			__( 'Account Page', 'better-member-ux-for-memberpress' ),
			__( 'Control the MemberPress account navigation and profile layout.', 'better-member-ux-for-memberpress' )
		);
		$this->add_field( 'enable_account', __( 'Account enhancements', 'better-member-ux-for-memberpress' ), 'checkbox', 'bmux_account', __( 'Improve the MemberPress Account page', 'better-member-ux-for-memberpress' ) );
		$this->add_field( 'rename_home', __( 'Rename Home tab', 'better-member-ux-for-memberpress' ), 'checkbox', 'bmux_account', __( 'Replace the default Home navigation label', 'better-member-ux-for-memberpress' ) );
		$this->add_field( 'home_label', __( 'Account Home tab label', 'better-member-ux-for-memberpress' ), 'text', 'bmux_account' );
		$this->add_field( 'add_dashboard', __( 'Dashboard link', 'better-member-ux-for-memberpress' ), 'checkbox', 'bmux_account', __( 'Add a Dashboard link to account navigation', 'better-member-ux-for-memberpress' ) );
		$this->add_field( 'dashboard_label', __( 'Dashboard link label', 'better-member-ux-for-memberpress' ), 'text', 'bmux_account' );
		$this->add_field( 'dashboard_url', __( 'Dashboard URL', 'better-member-ux-for-memberpress' ), 'url', 'bmux_account', '', __( 'A relative path such as /dashboard/ or a full URL.', 'better-member-ux-for-memberpress' ) );

		$this->add_section(
			'bmux_auth',
			__( 'Login and Password Reset', 'better-member-ux-for-memberpress' ),
			__( 'Set the headings, helper text, and return link shown on authentication pages.', 'better-member-ux-for-memberpress' )
		);
		$this->add_field( 'enable_login', __( 'Login enhancements', 'better-member-ux-for-memberpress' ), 'checkbox', 'bmux_auth', __( 'Improve the MemberPress Login page', 'better-member-ux-for-memberpress' ) );
		$this->add_field( 'enable_password_reset', __( 'Password reset enhancements', 'better-member-ux-for-memberpress' ), 'checkbox', 'bmux_auth', __( 'Improve the MemberPress Password Reset page', 'better-member-ux-for-memberpress' ) );
		$this->add_field( 'login_heading', __( 'Login heading', 'better-member-ux-for-memberpress' ), 'text', 'bmux_auth' );
		$this->add_field( 'login_helper', __( 'Login helper text', 'better-member-ux-for-memberpress' ), 'textarea', 'bmux_auth' );
		$this->add_field( 'password_reset_helper', __( 'Password reset helper text', 'better-member-ux-for-memberpress' ), 'textarea', 'bmux_auth' );
		$this->add_field( 'login_url', __( 'Login URL', 'better-member-ux-for-memberpress' ), 'url', 'bmux_auth', '', __( 'Used by the return-to-login link on the password reset page.', 'better-member-ux-for-memberpress' ) );

		$this->add_section(
			'bmux_branding',
			__( 'Branding', 'better-member-ux-for-memberpress' ),
			__( 'Choose colors that fit your site. Accessible defaults are provided.', 'better-member-ux-for-memberpress' )
		);
		$this->add_field( 'primary_color', __( 'Primary color', 'better-member-ux-for-memberpress' ), 'color', 'bmux_branding' );
		$this->add_field( 'accent_color', __( 'Accent color', 'better-member-ux-for-memberpress' ), 'color', 'bmux_branding' );
		$this->add_field( 'background_color', __( 'Background color', 'better-member-ux-for-memberpress' ), 'color', 'bmux_branding' );
		$this->add_field( 'border_color', __( 'Border color', 'better-member-ux-for-memberpress' ), 'color', 'bmux_branding' );
	}

	/**
	 * Register a settings section.
	 *
	 * @param string $id          Section identifier.
	 * @param string $title       Section title.
	 * @param string $description Section description.
	 */
	private function add_section( $id, $title, $description ) {
		add_settings_section(
			$id,
			$title,
			function () use ( $description ) {
				printf( '<p class="bmux-section-description">%s</p>', esc_html( $description ) );
			},
			'better-member-ux'
		);
	}

	/**
	 * Register a settings field.
	 *
	 * @param string $key         Option key.
	 * @param string $label       Field label.
	 * @param string $type        Field type.
	 * @param string $section     Section identifier.
	 * @param string $checkbox    Checkbox text.
	 * @param string $description Optional field description.
	 */
	private function add_field( $key, $label, $type, $section, $checkbox = '', $description = '' ) {
		add_settings_field(
			'bmux_' . $key,
			$label,
			array( $this, 'render_field' ),
			'better-member-ux',
			$section,
			array(
				'key'         => $key,
				'type'        => $type,
				'checkbox'    => $checkbox,
				'description' => $description,
				'label_for'   => 'bmux_' . $key,
			)
		);
	}

	/**
	 * Render a settings field.
	 *
	 * @param array $args Field configuration.
	 */
	public function render_field( $args ) {
		$options = self::get_options();
		$key     = $args['key'];
		$type    = $args['type'];
		$value   = isset( $options[ $key ] ) ? $options[ $key ] : '';
		$name    = self::OPTION_NAME . '[' . $key . ']';
		$id      = 'bmux_' . $key;

		if ( 'checkbox' === $type ) {
			printf(
				'<label class="bmux-toggle"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s><span>%4$s</span></label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( 1, (int) $value, false ),
				esc_html( $args['checkbox'] )
			);
		} elseif ( 'textarea' === $type ) {
			printf(
				'<textarea id="%1$s" name="%2$s" rows="3" class="large-text">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( $value )
			);
		} else {
			$input_type = 'color' === $type ? 'color' : 'text';
			$class      = 'color' === $type ? 'bmux-color-field' : 'regular-text';
			printf(
				'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="%5$s"%6$s>',
				esc_attr( $input_type ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				esc_attr( $class ),
				'url' === $type ? ' inputmode="url"' : ''
			);
		}

		if ( ! empty( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
		}
	}

	/**
	 * Sanitize the complete settings array.
	 *
	 * @param mixed $input Submitted settings.
	 * @return array
	 */
	public function sanitize_options( $input ) {
		$defaults = self::get_defaults();
		$input    = is_array( $input ) ? $input : array();
		$output   = $defaults;

		$checkboxes = array( 'enabled', 'enable_account', 'rename_home', 'add_dashboard', 'enable_login', 'enable_password_reset' );
		foreach ( $checkboxes as $key ) {
			$output[ $key ] = isset( $input[ $key ] ) ? 1 : 0;
		}

		$text_fields = array( 'home_label', 'dashboard_label', 'login_heading' );
		foreach ( $text_fields as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$output[ $key ] = sanitize_text_field( $input[ $key ] );
			}
		}

		$textarea_fields = array( 'login_helper', 'password_reset_helper' );
		foreach ( $textarea_fields as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$output[ $key ] = sanitize_textarea_field( $input[ $key ] );
			}
		}

		$url_fields = array( 'dashboard_url', 'login_url' );
		foreach ( $url_fields as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$sanitized_url = esc_url_raw( trim( $input[ $key ] ) );
				$output[ $key ] = '' !== $sanitized_url ? $sanitized_url : $defaults[ $key ];
			}
		}

		$color_fields = array( 'primary_color', 'accent_color', 'background_color', 'border_color' );
		foreach ( $color_fields as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$sanitized_color = sanitize_hex_color( $input[ $key ] );
				$output[ $key ]  = $sanitized_color ? $sanitized_color : $defaults[ $key ];
			}
		}

		return $output;
	}

	/**
	 * Load settings-page-only styling.
	 *
	 * @param string $hook_suffix Current admin screen hook.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		if ( 'settings_page_better-member-ux' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'bmux-admin',
			BMUX_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			$this->asset_version( 'assets/css/admin.css' )
		);
	}

	/**
	 * Render the settings screen.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap bmux-settings-wrap">
			<h1><?php echo esc_html__( 'Better Member UX for MemberPress', 'better-member-ux-for-memberpress' ); ?></h1>
			<p class="bmux-settings-intro"><?php echo esc_html__( 'Improve MemberPress account and authentication pages without replacing templates or changing form behavior.', 'better-member-ux-for-memberpress' ); ?></p>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'bmux_settings_group' );
				do_settings_sections( 'better-member-ux' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Return a development cache-buster when appropriate.
	 *
	 * @param string $relative_path Asset path relative to the plugin directory.
	 * @return string
	 */
	private function asset_version( $relative_path ) {
		$file = BMUX_PLUGIN_DIR . $relative_path;

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && file_exists( $file ) ) {
			return (string) filemtime( $file );
		}

		return BMUX_VERSION;
	}
}

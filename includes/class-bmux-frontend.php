<?php
/**
 * Frontend asset handling.
 *
 * @package BetterMemberUXForMemberPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads the lightweight frontend enhancement layer.
 */
class BMUX_Frontend {

	/**
	 * Register WordPress hooks.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Check whether MemberPress appears active.
	 *
	 * @return bool
	 */
	private function is_memberpress_active() {
		return class_exists( 'MeprAppCtrl' ) || defined( 'MEPR_VERSION' );
	}

	/**
	 * Enqueue scoped assets and pass sanitized settings to JavaScript.
	 */
	public function enqueue_assets() {
		$options = BMUX_Settings::get_options();

		if ( empty( $options['enabled'] ) || ! $this->is_memberpress_active() ) {
			return;
		}

		wp_enqueue_style(
			'bmux-frontend',
			BMUX_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			$this->asset_version( 'assets/css/frontend.css' )
		);

		wp_enqueue_script(
			'bmux-frontend',
			BMUX_PLUGIN_URL . 'assets/js/frontend.js',
			array(),
			$this->asset_version( 'assets/js/frontend.js' ),
			true
		);

		$current_user = wp_get_current_user();
		$member_name  = $current_user instanceof WP_User && $current_user->exists() ? $current_user->display_name : '';

		wp_localize_script(
			'bmux-frontend',
			'BMUXSettings',
			array(
				'enabled'               => true,
				'enableAccount'         => ! empty( $options['enable_account'] ),
				'enableLogin'           => ! empty( $options['enable_login'] ),
				'enablePasswordReset'   => ! empty( $options['enable_password_reset'] ),
				'renameHome'            => ! empty( $options['rename_home'] ),
				'homeLabel'             => $options['home_label'],
				'addDashboard'          => ! empty( $options['add_dashboard'] ),
				'dashboardLabel'        => $options['dashboard_label'],
				'dashboardUrl'          => $options['dashboard_url'],
				'loginHeading'          => $options['login_heading'],
				'loginHelper'           => $options['login_helper'],
				'passwordResetHelper'   => $options['password_reset_helper'],
				'loginUrl'              => $options['login_url'],
				'memberName'            => $member_name,
				'strings'               => array(
					'memberAccount'      => __( 'Member Account', 'better-member-ux-for-memberpress' ),
					'manageMembership'   => __( 'Manage your membership', 'better-member-ux-for-memberpress' ),
					'profileInformation' => __( 'Profile Information', 'better-member-ux-for-memberpress' ),
					'addressDetails'     => __( 'Address Details', 'better-member-ux-for-memberpress' ),
					'rememberPassword'   => __( 'Remember your password?', 'better-member-ux-for-memberpress' ),
					'returnToLogin'      => __( 'Return to the login page.', 'better-member-ux-for-memberpress' ),
				),
			)
		);

		$colors = array(
			'primary'    => $options['primary_color'],
			'accent'     => $options['accent_color'],
			'background' => $options['background_color'],
			'border'     => $options['border_color'],
		);

		$custom_css = sprintf(
			'.bmux-account-area,.bmux-auth-area,.bmux-auth-card{--bmux-primary:%1$s;--bmux-accent:%2$s;--bmux-bg:%3$s;--bmux-border:%4$s;}',
			esc_attr( $colors['primary'] ),
			esc_attr( $colors['accent'] ),
			esc_attr( $colors['background'] ),
			esc_attr( $colors['border'] )
		);

		wp_add_inline_style( 'bmux-frontend', $custom_css );
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

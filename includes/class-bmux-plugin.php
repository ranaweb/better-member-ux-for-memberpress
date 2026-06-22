<?php
/**
 * Main plugin coordinator.
 *
 * @package BetterMemberUXForMemberPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads and initializes the plugin components.
 */
final class BMUX_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var BMUX_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Return the plugin instance.
	 *
	 * @return BMUX_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load dependencies and register hooks.
	 */
	private function __construct() {
		require_once BMUX_PLUGIN_DIR . 'includes/class-bmux-settings.php';
		require_once BMUX_PLUGIN_DIR . 'includes/class-bmux-frontend.php';

		new BMUX_Settings();
		new BMUX_Frontend();

		add_filter( 'plugin_action_links_' . plugin_basename( BMUX_PLUGIN_FILE ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Add a shortcut to the settings page on the Plugins screen.
	 *
	 * @param array $links Existing plugin action links.
	 * @return array
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'options-general.php?page=better-member-ux' ) ),
			esc_html__( 'Settings', 'better-member-ux-for-memberpress' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}
}

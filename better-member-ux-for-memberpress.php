<?php
/**
 * Plugin Name: Better Member UX for MemberPress
 * Plugin URI: https://github.com/ranaweb/better-member-ux-for-memberpress
 * Description: Improves MemberPress account, login, and password reset pages with cleaner layouts, better navigation, responsive tables, and optional dashboard linking.
 * Version: 1.0.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: ranaweb
 * Author URI: https://github.com/ranaweb
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: better-member-ux-for-memberpress
 *
 * @package BetterMemberUXForMemberPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BMUX_VERSION', '1.0.1' );
define( 'BMUX_PLUGIN_FILE', __FILE__ );
define( 'BMUX_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BMUX_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once BMUX_PLUGIN_DIR . 'includes/class-bmux-plugin.php';

BMUX_Plugin::instance();

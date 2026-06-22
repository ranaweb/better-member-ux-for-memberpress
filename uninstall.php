<?php
/**
 * Remove plugin settings on uninstall.
 *
 * @package BetterMemberUXForMemberPress
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'bmux_options' );

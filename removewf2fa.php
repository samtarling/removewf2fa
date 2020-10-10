<?php
/**
 * Plugin Name:       Remove WordFence 2FA
 * Description:       (Visually) removes all trace of the WordFence 2FA feature
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sam Tarling
 * Author URI:        https://www.samt.dev
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       removewf2fa
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once ABSPATH.'wp-admin/includes/plugin.php';

//Check to see if the Wordfence plugin is installed and active
if ( is_plugin_active( 'wordfence/wordfence.php' ) ) {

	/**
	 * Remove views added in \wordfence\modules\login-security\classes\controller\users.php
	 */
	function rwf_remove_views( $views ) {
		unset( $views['wfls-active'] );
		unset( $views['wfls-inactive'] );
		return $views;
	}

	/**
	 * Remove user columns added in \wordfence\modules\login-security\classes\controller\users.php
	 */
	function rwf_remove_cols( $columns = array() ) {
		unset( $columns['wfls_2fa_status'] );
		return $columns;
	}

	/**
	 * Remove actions added in \wordfence\modules\login-security\classes\controller\users.php
	 */
	function rwf_remove_actions( $actions ) {
		unset( $actions['wf2fa'] );
		return $actions;
	}

	/**
	 * Remove admin submenus and elements added to user profile
	 */
	function rwf_remove_items() {

		$test = \WordfenceLS\Controller_WordfenceLS::shared();
		remove_submenu_page( 'Wordfence', 'WFLS' );
		remove_action( 'edit_user_profile', array( $test, '_edit_user_profile' ), 0 );
		remove_action( 'show_user_profile', array( $test, '_edit_user_profile' ), 0 );

	}

	add_action( 'admin_menu', 'rwf_remove_items', 999 );
	add_filter( 'views_users', 'rwf_remove_views', 999 );
	add_filter( 'manage_users_columns', 'rwf_remove_cols', 999 );
	add_filter( 'user_row_actions', 'rwf_remove_actions', 999 );

} else {
    /**
     * Display a warning to the user if the plugin could not detect WordFence
     */
	function rwf_admin_notice_missing() {
		$class   = 'notice notice-error';
		$message = __( 'Oh no! Remove WordFence 2FA could not detect WordFence - check it is installed and try again!', 'removewf2fa' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
	add_action( 'admin_notices', 'rwf_admin_notice_missing' );

}

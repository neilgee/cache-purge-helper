<?php

/**
 * Plugin Name:       RunCloud OpenLiteSpeed Cache Purge Helper
 * Plugin URI:        https://wpbeaches.com
 * Description:       Adding additional hooks to trigger lscache plugin purges
 * Version:           0.1.5
 * Author:            Paul Stoute, Jordan Trask, Jeff Cleverly, Neil Gowran
 * Author URI:        https://wpbeaches.com
 * Text Domain:       cache-purge-helper
 * Domain Path:       /languages
 * Requires at least: 3.0
 * Tested up to:      5.4
 *
 * @link              https://wpinfo.net
 * @since             0.1
 * @package           cache-purge-helper
 */

/* Purge Cache Function
*
* Liitespeed-cache plugin.
*/

function rclc_purge() {
    // Purge WordPress Cache
    $called_action_hook = current_filter();
    rclc_write_log('rclc - Initiated');
    rclc_write_log('rclc - Running on '. $called_action_hook );
    rclc_write_log('rclc - Flushing WordPress Cache first');
    wp_cache_flush();
 
    // If litespeed-cache plugins is enabled, purge cache.
    rclc_write_log('rclc - Checking for litespeed-cache plugin');

    if ( is_plugin_active('litespeed-cache/litespeed-cache.php') ) {
        rclc_write_log('rclc - Litespeed-cache plugin installed, running do_action(\'litespeed_purge_all\');');
        do_action( 'litespeed_purge_all' );
    }  else {
        rclc_write_log('rclc - Litespeed-cache plugin not installed or detected');
    }

    // End of cache_purge_helper()
    rclc_write_log('rclc - End of cache_purge_helper function');
}

  // If RunCloud plugins is enabled, purge cache.
  rclc_write_log('rclc - Checking for RunCloud plugin');
	if ( class_exists('RunCloud_Hub') && is_callable( [ 'RunCloud_Hub', 'purge_cache_all_noaction' ] ) ) {
	    RunCloud_Hub::purge_cache_all_noaction();
	    rclc_write_log('rclc - Flushing RunCloud cache out');
	} else {
    rclc_write_log('rclc - No RunCloud Plugin here');
  }

/** 
 * Log to WordPress Debug Log Function
 * Log to PHP error_log if WP_DEBUG and CPH_DEBUG are set!
 */

function rclc_write_log ( $log )  {
    if ( WP_DEBUG === true  ) {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}



/**
 * WordPress core hooks.
 */

rclc_write_log('rclc - Loading WordPress core hooks');
add_action( 'upgrader_process_complete', 'rclc_purge', 10, 0 ); // After a plugin, theme or core has been updated.
add_action( 'activated_plugin', 'rclc_purge', 10, 0); // After a plugin has been activated
add_action( 'deactivated_plugin', 'rclc_purge', 10, 0); // After a plugin has been deactivated
add_action( 'switch_theme', 'rclc_purge', 10, 0); // After a theme has been changed

/**
 * Page builder hooks.
 */

// Beaver Builder
if ( defined( 'FL_BUILDER_VERSION' ) ) {
    rclc_write_log('rclc - Beaver Builder Hooks enabled');
    add_action( 'fl_builder_cache_cleared', 'rclc_purge', 10, 3 );
    add_action( 'fl_builder_after_save_layout', 'rclc_purge', 10, 3 );
    add_action( 'fl_builder_after_save_user_template', 'rclc_purge', 10, 3 );
}

// Elementor
if ( defined( 'ELEMENTOR_VERSION' ) ) {
    rclc_write_log('rclc - Elementor hooks enabled');
    add_action( 'elementor/core/files/clear_cache', 'rclc_purge', 10, 3 ); 
    add_action( 'update_option__elementor_global_css', 'rclc_purge', 10, 3 );
    add_action( 'delete_option__elementor_global_css', 'rclc_purge', 10, 3 );
}

// Oxygen
if ( defined( 'CT_VERSION' ) ) {
    rclc_write_log('rclc - Oxygen hooks enabled');
    add_action( 'wp_ajax_oxygen_vsb_cache_generated','rclc_purge', 99 );
    add_action( 'update_option__oxygen_vsb_universal_css_url','rclc_purge', 99 );
    add_action( 'update_option__oxygen_vsb_css_files_state','rclc_purge', 99 );
}

/**
 * Optimization and caching plugin hooks.
 */

// Autoptimizer
if ( defined( 'AUTOPTIMIZE_PLUGIN_DIR' ) ) {
    rclc_write_log('rclc - Autoptimize hooks enabled');
    add_action( 'autoptimize_action_cachepurged','rclc_purge', 10, 3 ); // Need to document this.
}

// WP Optimize Hooks
if ( defined ('WPO_VERSION') ){
    rclc_write_log('rclc - WP Optimize hooks enabled');
    add_filter('wpo_purge_all_cache_on_update', '__return_true');
}

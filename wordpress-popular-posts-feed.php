<?php
/**
 * The plugin bootstrap file.
 *
 * @link              https://cabrerahector.com/
 * @since             1.0.0
 * @package           cabrerahector\Popular_Posts_Feed
 *
 * @wordpress-plugin
 * Plugin Name:       Popular Posts Feed
 * Plugin URI:        https://cabrerahector.com/
 * Description:       Adds a popular posts feed to your site.
 * Version:           1.0.0
 * Author:            Hector Cabrera
 * Author URI:        https://cabrerahector.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace cabrerahector\Popular_Posts_Feed;

// File is being accessed directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Initializes the plugin.
 *
 * @since   1.0.0
 */
function init() {

    if ( class_exists('WPP_Query', false) ) {
        add_action( 'init', __NAMESPACE__ . '\register_feed' );
    }
    else {
        add_action( 'admin_notices', __NAMESPACE__ . '\admin_notice' );
    }

}

/**
 * Registers the popular feed.
 *
 * @since   1.0.0
 */
function register_feed() {
    add_feed( 'popular-posts', __NAMESPACE__ . '\generate_feed' );
}

/**
 * Generates the feed.
 *
 * @since   1.0.0
 */
function generate_feed() {
    add_filter( 'pre_option_rss_use_excerpt', '__return_zero' );
    require plugin_dir_path( __FILE__ ) . 'feed.php';
}

/**
 * Generates an admin notice warning the user that
 * WordPress Popular Posts must be active in order
 * to use the plugin.
 *
 * @since   1.0.0
 */
function admin_notice() {
    $class = 'notice notice-error';
    $message = __( 'Popular Posts Feed requires WordPress Popular Posts in order to work.', 'wordpress-popular-posts-feed' );

    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\init' );

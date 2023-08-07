<?php
/**
 * Cosmo Theme Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Cosmo Theme
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_COSMO_THEME_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'cosmo-theme-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_COSMO_THEME_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

// function my_theme_scripts() {
//     wp_enqueue_script( 'my-ajax-script', get_stylesheet_directory_uri() . '/js/my-ajax-script.js', array('jquery'), '1.0', true );
//     wp_localize_script( 'my-ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
// }
// add_action( 'wp_enqueue_scripts', 'my_theme_scripts' );

function my_theme_scripts() {
    wp_enqueue_script( 'my-ajax-script', get_stylesheet_directory_uri() . '/js/my-ajax-script.js', array('jquery'), '1.0', true );
    
    global $post;  // get global post object
    
    wp_localize_script( 'my-ajax-script', 'my_ajax_object', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'post_id'  => $post->ID  // pass post_id
    ) );
}
add_action( 'wp_enqueue_scripts', 'my_theme_scripts' );

function get_single_match_data() {
    // Check if post_id is set
    if(!isset($_POST['post_id'])) {
        wp_send_json_error('Post ID not set');
    }

    $post_id = intval($_POST['post_id']);

    // Retrieve match data using get_post_meta
    $match_data = array(
        'home' => get_post_meta($post_id, 'home_team_name', true),
        'away' => get_post_meta($post_id, 'away_team_name', true),
        'home_score' => get_post_meta($post_id, 'home_team_score', true),
        'away_score' => get_post_meta($post_id, 'away_team_score', true),
        'home_logo' => get_post_meta($post_id, 'home_team_logo', true),
        'away_logo' => get_post_meta($post_id, 'away_team_logo', true),
        'tournament_name' => get_post_meta($post_id, 'tournament_name', true),
        'elapsed_time' => get_post_meta($post_id, 'elapsed_time', true),
        'match_time' => get_post_meta($post_id, 'match_time', true),
        'animation_url' => get_post_meta($post_id, 'anim_url', true),
        'status' => get_post_meta($post_id, 'status', true),
        'video_data_urls' => json_decode(get_post_meta($post_id, 'video_data_urls', true), true),

		// 'video_data_urls' => json_decode(get_post_meta($post_id, 'video_data_urls', true), true),
		// 'video_data_urls' => get_post_meta($post_id, 'video_data_urls', true),
    );

    // And finally, return this data as a JSON response:
    wp_send_json_success($match_data);
}

// Handle AJAX requests
add_action('wp_ajax_get_single_match_data', 'get_single_match_data'); // For logged in users
add_action('wp_ajax_nopriv_get_single_match_data', 'get_single_match_data'); // For logged out users
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
        'match_id' => get_post_meta($post_id, 'match_id', true),
        'commentator' => get_post_meta($post_id, 'commentator', true),
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

function get_all_match_dates() {
    global $wpdb;
    
    $dates = $wpdb->get_col("SELECT DISTINCT DATE(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'match_time' ORDER BY meta_value DESC");
    
    return $dates;
}

function get_matches_by_tournament($date) {
    $args = array(
        'post_type' => 'match',  // or whatever your custom post type is called
        'posts_per_page' => -1,  // get all matches
        'meta_key' => 'tournament_name',  // we want to sort by tournament name
        'orderby' => 'meta_value',  // sort by the meta key value
        'order' => 'ASC',  // ascending order
        'meta_query' => array(
            array(
                'key' => 'match_time',
                'value' => array($date . ' 00:00:00', $date . ' 23:59:59'),
                'compare' => 'BETWEEN'
            )
        )
    );

    $query = new WP_Query($args);
    
    $matches = array();
    if($query->have_posts()) {
        while($query->have_posts()) {
            $query->the_post();
            
            $matches[] = array(
                'tournament_name' => get_post_meta(get_the_ID(), 'tournament_name', true),
                'home' => get_post_meta(get_the_ID(), 'home_team_name', true),
                'away' => get_post_meta(get_the_ID(), 'away_team_name', true),
                'home_score' => get_post_meta(get_the_ID(), 'home_team_score', true),
                'away_score' => get_post_meta(get_the_ID(), 'away_team_score', true),
                'home_logo' => get_post_meta(get_the_ID(), 'home_team_logo', true),
                'away_logo' => get_post_meta(get_the_ID(), 'away_team_logo', true),
                // ... any other data you need
            );
        }
    }
    wp_reset_postdata();
    
    return $matches;
}
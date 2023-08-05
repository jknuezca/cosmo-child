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

function match_post_exists($match_id) {
    $args = array(
        'post_type' => 'match', // Custom Post Type (slug)
        'meta_key' => 'match_id',
        'meta_value' => $match_id,
    );
    
    $query = new WP_Query($args);
    return $query->have_posts();
}

function create_match_post($match_data) {
    $match_id = $match_data['match_id'];

    if (!match_post_exists($match_id)) {
        $post_title = $match_data['home_team_name'] . ' vs. ' . $match_data['away_team_name'];

        $post_data = array(
            'post_title'   => $post_title,
            'post_content' => '',
            'post_type'    => 'match',
            'post_status'  => 'publish',
        );

        $post_id = wp_insert_post($post_data);

        update_post_meta($post_id, 'tournament_name', $match_data['tournament_name']);
        update_post_meta($post_id, 'home_team_name', $match_data['home_team_name']);
        update_post_meta($post_id, 'away_team_name', $match_data['away_team_name']);
        update_post_meta($post_id, 'home_team_score', $match_data['home_team_score']);
        update_post_meta($post_id, 'away_team_score', $match_data['away_team_score']);
        update_post_meta($post_id, 'home_team_logo', $match_data['home_team_logo']);
        update_post_meta($post_id, 'away_team_logo', $match_data['away_team_logo']);
        update_post_meta($post_id, 'elapsed_time', $match_data['elapsed_time']);
        update_post_meta($post_id, 'match_time', $match_data['match_time']);
        if (isset($match_data['anim_url'])) {
            update_post_meta($post_id, 'anim_url', $match_data['anim_url']);
        }
        update_post_meta($post_id, 'match_id', $match_id);

        return $post_id;
    } else {
        return 0;
    }
}


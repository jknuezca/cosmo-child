<?php
// Include WordPress functions to be able to use WP functions
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );

// Include the necessary files using the correct file paths
require_once get_stylesheet_directory() . '/api-functions.php';
require_once get_stylesheet_directory() . '/match-updater.php';

// Define a function that gets the match data and returns the HTML
function get_match_html() {
    ob_start();
    update_matches();
    return ob_get_clean();
}

// Call the function and echo its return value
echo get_match_html();
?>

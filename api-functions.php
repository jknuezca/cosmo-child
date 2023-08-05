<?php

function fetch_api_data() {
    // Set the API endpoint URL
    $api_url = 'https://686sports.com/matchListWeb';

    // Make the API request
    $response = wp_remote_get($api_url);

    // Check for errors
    if (is_wp_error($response)) {
        return false; // Return false to indicate an error occurred
    }

    // Retrieve the response body
    $response_body = wp_remote_retrieve_body($response);

    // Decode the JSON response
    $data = json_decode($response_body, true);

    // Check if decoding was successful and the API response contains data
    if (!$data || !is_array($data) || empty($data)) {
        return false; // Return false to indicate an empty or invalid API response
    }

    return $data; // Return the decoded API data
}

?>

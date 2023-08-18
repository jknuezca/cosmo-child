<?php

    function fetch_api_data() {
        $api_url = 'https://686sports.com/matchListWeb';

        $response = wp_remote_get($api_url);

        // Check if there was an error with the HTTP request
        if (is_wp_error($response)) {
            error_log('API fetch error: ' . $response->get_error_message());
            return false;
        }

        // Retrieve and validate the response body
        $response_body = wp_remote_retrieve_body($response);
        if ($response_body === '') {
            error_log('API fetch error: Empty response.');
            return false;
        }

        // Decode and validate the JSON data
        $data = json_decode($response_body, true);
        if ($data === null || !is_array($data) || empty($data)) {
            error_log('API fetch error: Invalid JSON or empty data.');
            return false;
        }

        return $data;
    }

?>

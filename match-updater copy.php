<?php
// match-updater.php
// Check if a post with the same match data already exists
function match_post_exists($match_id) {
    $args = array(
        'post_type' => 'match',
        'meta_key' => 'match_id',
        'meta_value' => $match_id,
    );

    $query = new WP_Query($args);
    $exists = $query->have_posts();
    
    // Debugging: Print out the match ID and whether it exists
    echo 'Checking existence of match with ID ' . $match_id . ': ';
    echo $exists ? 'Exists' : 'Does not exist';
    echo '<br>';

    return $exists;
}

// Create posts programmatically
function create_match_post($match_data) {
    $match_id = $match_data['match_id'];

    if (!match_post_exists($match_id)) {
        // Set post title
        $post_title = $match_data['home_team_name'] . ' vs. ' . $match_data['away_team_name'];

        // Prepare post data
        $post_data = array(
            'post_title'   => $post_title, // Set the post title with the concatenated team names
            'post_content' => '', // Leave content empty or customize as needed
            'post_type'    => 'match', // Custom Post Type (slug)
            'post_status'  => 'publish', // Set the status of the post (publish, draft, etc.)
        );

        // Insert the post into the database
        $post_id = wp_insert_post($post_data);

        // Set post meta for additional information (e.g., match details)
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
        // match_id as the unique identifier
        update_post_meta($post_id, 'match_id', $match_id);

        // Return the post ID for reference
        return $post_id;

    } else {
        // If a matching post already exists, return 0 or false to indicate no new post was created
        return 0;
    }
}

function update_matches() {
    $data = fetch_api_data();

    if ($data) {
        // Arrays to store live and upcoming matches
        $live_matches = [];
        $upcoming_matches = [];

        // Separate live and upcoming matches based on status
        foreach ($data as $match) {
            $status = $match['status'];
            if ($status == '2') {
                $live_matches[] = $match;
            } elseif ($status == '1') {
                $upcoming_matches[] = $match;
            }
        }

        // Output the live matches
        echo '<h2>Live Matches</h2>';
        foreach ($live_matches as $match) {
            // Extract the desired data field(s) from the current match
            $home = $match['home_team_name'];
            $away = $match['away_team_name'];
            $home_score = $match['home_team_score'];
            $away_score = $match['away_team_score'];
            $home_logo = $match['home_team_logo'];
            $away_logo = $match['away_team_logo'];
            
            $t_name = $match['tournament_name'];
            $e_time = $match['elapsed_time'];

            // Get default logo URL from assets folder
            $default_logo_url = get_stylesheet_directory_uri() . '/assets/images/football-min.png';

            // Check if the logo is empty, and use the default logo URL
            $home_logo_url = !empty($home_logo) ? $home_logo : $default_logo_url;
            $away_logo_url = !empty($away_logo) ? $away_logo : $default_logo_url;

            // Calculate elapsed time in hours
            $elapsed_hours = floor($e_time / 60);
            // $elapsed_minutes = $e_time % 60;

            $post_id = create_match_post($match);
            // New code: Check if the post was created successfully
        if ($post_id === 0) {
            echo 'Failed to create match post.';
        } else {
            echo 'Match post created with ID: ' . $post_id;
        }
            $permalink = get_permalink($post_id);
            
            // Display the live match information as you did in 'front-page.php'
            // (Keep the existing code you have for displaying live matches here)
            ?>
            <div class="main-match">
                <div class="tournament-name text-center">
                    <p><?php echo $t_name; ?></p>
                    
                </div>
                <div class="match-content">
                    <!-- Home -->
                    <div class="match-left-option text-center">
                        <img src="<?php echo $home_logo; ?>" alt="home-logo">
                        <p><?php echo $home; ?></p>
                    </div>

                    <!-- Score -->
                    <div class="match-mid-option text-center">
                        <?php
                        // Check if the match is upcoming (elapsed_time is empty)
                        if (empty($e_time)) {
                            // Display the match time in the "Score" section
                            $m_time = $match['match_time'];
                        ?>
                            <p><?php echo $m_time; ?></p>
                        <?php
                        } else {
                            // Match has started, display the actual scores
                        ?>
                            <div class="home-score">
                                <p><?php echo $home_score; ?></p>
                            </div>
                            <span>:</span>
                            <div class="away-score">
                                <p><?php echo $away_score; ?></p>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="elapsed-time">
                            <p><?php echo $e_time; ?>'</p>
                            <p><?php echo $elapsed_hours; ?>H</p> <!-- Display elapsed time in hours -->
                        </div>
                    </div>

                    <!-- Away -->
                    <div class="match-right-option text-center">
                        <img src="<?php echo $away_logo; ?>" alt="away-logo">
                        <p><?php echo $away; ?></p>
                    </div>
                </div>
                <div class="view-match-btn text-center">
                    <a href="<?php echo $permalink; ?>">View Match Details</a>
                </div>
            </div>
        <?php
                }

        // Output the upcoming matches
        echo '<h2>Upcoming Matches</h2>';
        foreach ($upcoming_matches as $match) {
            // Extract the desired data field(s) from the current match
            $home = $match['home_team_name'];
            $away = $match['away_team_name'];
            $home_score = $match['home_team_score'];
            $away_score = $match['away_team_score'];
            $home_logo = $match['home_team_logo'];
            $away_logo = $match['away_team_logo'];
            
            // Display the upcoming match information as you did in 'front-page.php'
            // (Keep the existing code you have for displaying upcoming matches here)
        }
    } else {
        echo '<div>Error: Failed to fetch API data.</div>';
    }
}
?>

<?php
// Check if a post with the same match data already exists
function get_post_id_by_match_id($match_id) {
    $args = array(
        'post_type' => 'match',
        'meta_key' => 'match_id',
        'meta_value' => $match_id,
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        return $query->posts[0]->ID; // Return the ID of the first (and likely only) match post
    } else {
        return false; // Return false if no matching post was found
    }
}

// Create posts programmatically
function create_match_post($match_data) {
    $match_id = $match_data['match_id'];

    // Check if a post with the same match_id already exists
    $existing_post_id = get_post_id_by_match_id($match_id);

    // Set post title
    $post_title = $match_data['home_team_name'] . ' vs. ' . $match_data['away_team_name'];

    // Prepare post data
    $post_data = array(
        'post_title'   => $post_title, // Set the post title with the concatenated team names
        'post_content' => '', // Leave content empty or customize as needed
        'post_type'    => 'match', // Custom Post Type (slug)
        'post_status'  => 'publish', // Set status of the post (publish, draft, etc.)
    );

    // If a post with this match_id already exists, update it
    if ($existing_post_id) {
        $post_data['ID'] = $existing_post_id; // Setting the ID causes wp_insert_post to update the existing post
    }

    // Insert the post into the database (or update the existing post)
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
    update_post_meta($post_id, 'status', $match_data['status']);

    // match_id as the unique identifier
    update_post_meta($post_id, 'match_id', $match_id);

    if (isset($match_data['commentator'])) {
        update_post_meta($post_id, 'commentator', $match_data['commentator']);
    }
    if (isset($match_data['anim_url'])) {
        update_post_meta($post_id, 'anim_url', $match_data['anim_url']);
    }

    // Check if video_data_urls is set and is an array
    if (isset($match_data['video_data_urls']) && is_array($match_data['video_data_urls'])) {
        // Store data as serialized array (or JSON, depending on your needs)
        update_post_meta($post_id, 'video_data_urls', json_encode($match_data['video_data_urls']));
    }

    // Return the post ID for reference
    return $post_id;
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
        if (!empty($live_matches)) {
            echo '
            <div class="live-container">
                <div class="live-title">
                    <h2>Live Matches</h2>
                </div>';
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
            // $live_bg = get_stylesheet_directory_uri() . '/assets/images/live-bg.webp';

            // Use the null coalescing operator to handle logo URLs
            $home_logo_url = $home_logo ?: $default_logo_url;
            $away_logo_url = $away_logo ?: $default_logo_url;

            // Calculate elapsed time in hours
            $elapsed_hours = floor($e_time / 60);
            // $elapsed_minutes = $e_time % 60;

            $post_id = create_match_post($match);
            $permalink = get_permalink($post_id);
            
            ?>
            <div class="main-match live-match">
                <div class="live-overlay"></div>
                <a href="<?php echo $permalink; ?>">
                <div class="tournament-name text-center">
                    <p><?php echo $t_name; ?></p>
                    
                </div>
                <div class="match-content">
                    <!-- Home -->
                    <div class="match-left-option text-center">
                        <img 
                            src="<?php echo $home_logo_url; ?>" 
                            alt="<?php echo $home; ?>"
                            width="70"
                            height="70"
                            loading="lazy"
                        >
                        <p><?php echo $home; ?></p>
                    </div>

                    <!-- Score -->
                    <div class="match-mid-option text-center">
                        <?php
                        // Check if the match is upcoming (elapsed_time is empty)
                        if (empty($e_time)) {
                            // Display the match time in the "Score" section
                            $m_time = $match['match_time'];
                            $m_time = date('H:i', strtotime($m_time));
                        ?>
                            <p><?php echo $m_time; ?></p>
                        <?php
                        } else {
                            // Match has started, display the actual scores
                        ?>
                            <div class="score-box">
                                <div class="home-score">
                                    <p><?php echo $home_score; ?></p>
                                </div>
                                <span>:</span>
                                <div class="away-score">
                                    <p><?php echo $away_score; ?></p>
                                </div>
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
                        <img 
                            src="<?php echo $away_logo_url; ?>" 
                            alt="<?php echo $away; ?>"
                            width="70"
                            height="70"
                            loading="lazy"
                        >
                        <p><?php echo $away; ?></p>
                    </div>
                </div>
            </a>
            </div>
    <?php
            }
        }
    ?>
        </div> <!-- End of live matches --> 

    <?php
        // Output the upcoming matches
        echo '
        <div class="upcoming-container">
            <div class="upcoming-title">
                <h2>Upcoming Matches</h2>
            </div>';
        foreach ($upcoming_matches as $match) {
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

            // Use the null coalescing operator to handle logo URLs
            $home_logo_url = $home_logo ?: $default_logo_url;
            $away_logo_url = $away_logo ?: $default_logo_url;

            // Format match time to display only the time in "00:00" format
            $m_time = $match['match_time'];
            $m_time = date('H:i', strtotime($m_time));
            $m_date = date('m-d', strtotime($m_time));
        ?>
            <div class="main-match upcoming-match">
                <div class="upcoming-overlay"></div>
                <div class="tournament-name text-center">
                    <p><?php echo $t_name; ?></p>
                </div>
                <div class="match-content">
                    <!-- Home Team -->
                    <div class="match-left-option text-center">
                        <img 
                            src="<?php echo $home_logo_url; ?>" 
                            alt="<?php echo $home; ?>"
                            width="55"
                            height="55"
                            loading="lazy"
                        >
                        <p><?php echo $home; ?></p>
                    </div>
                    <!-- Score -->
                    <div class="match-mid-option text-center">
                        <p class="up-time"><?php echo $m_time; ?></p>
                        <p class="up-date"><?php echo $m_date; ?></p>
                    </div>
                    <!-- Away Team -->
                    <div class="match-right-option text-center">
                        <img 
                            src="<?php echo $away_logo_url; ?>" 
                            alt="<?php echo $away; ?>"
                            width="55"
                            height="55"
                            loading="lazy"
                        >
                        <p><?php echo $away; ?></p>
                    </div>
                </div>
            </div>
    <?php
        }
    ?>
        </div> <!-- End of upcoming matches --> 
<?php
    } else {
        echo '<div>Error: Failed to fetch API data.</div>';
    }
}
?>
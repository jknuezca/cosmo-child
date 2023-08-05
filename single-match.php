<?php
    get_header();

    // Enqueue the script and localize data
    wp_enqueue_script('my-ajax-script', get_template_directory_uri() . '/js/my-ajax-script.js', array('jquery'), '1.0', true);
    wp_localize_script('my-ajax-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'post_id' => get_the_ID()));

if (have_posts()) {
    while (have_posts()) {
        the_post();

        // Retrieve match data using get_post_meta
        $home = get_post_meta(get_the_ID(), 'home_team_name', true);
        $away = get_post_meta(get_the_ID(), 'away_team_name', true);
        $home_score = get_post_meta(get_the_ID(), 'home_team_score', true);
        $away_score = get_post_meta(get_the_ID(), 'away_team_score', true);
        $home_logo = get_post_meta(get_the_ID(), 'home_team_logo', true);
        $away_logo = get_post_meta(get_the_ID(), 'away_team_logo', true);
        $t_name = get_post_meta(get_the_ID(), 'tournament_name', true);
        $e_time = get_post_meta(get_the_ID(), 'elapsed_time', true);
        $m_time = get_post_meta(get_the_ID(), 'match_time', true);
        $anim_url = get_post_meta(get_the_ID(), 'anim_url', true);

        //Stream
        $video_url = get_post_meta(get_the_ID(), 'video_data_urls', true);
        $video_url = json_decode($video_url, true);  // Convert the JSON string back to PHP array

        // Check if video_data_urls is set and is an array
        if (isset($video_url) && is_array($video_url)) {
            // Get the first iframe URL
            $video_iframe_url = $video_url[0]['iframe'];
        }
        
        $default_logo_url = get_stylesheet_directory_uri() . '/assets/images/football-min.png';
        // $live_bg = '/wp-content/themes/force-child/assets/images/live-bg.webp';        

        $home_logo_url = $home_logo ?: $default_logo_url;
        $away_logo_url = $away_logo ?: $default_logo_url;

        $elapsed_hours = floor($e_time / 60);
?>
        <!-- Output the match content -->
        <main class="main-layout">
            <section class="single-match-container">
                <!--Tournament Name -->
                <div class="match-details">
                    <h1>Watch Live <?php echo $home; ?> vs. <?php echo $away; ?></h1>
                </div>

                <!-- Stream Content -->
                <!-- <div class="match-stream" id="match-stream" >
                    <iframe src="<?php //echo esc_url($video_iframe_url); ?>" width="900" height="507"></iframe>
                </div> -->
                <!-- Stream Content -->
                <div class="match-stream owl-carousel" id="match-stream" >
                    <?php
                    if (isset($video_url) && is_array($video_url)) {
                        foreach ($video_url as $video) {
                            if (isset($video['iframe'])) {
                                echo '<div class="item"><iframe src="' . esc_url($video['iframe']) . '" width="900" height="507"></iframe></div>';
                            }
                        }
                    }
                    ?>
                </div>
            </section>

            <div class="team-details">
                <h2>Team Scores</h2>
            </div>
            <section class="team-container">
                <!--Tournament Name -->
                <div class="team-details text-center">
                    <h3><?php echo $t_name; ?></h3>
                </div>
                <!-- Single Match Content -->
                <div class="single-match-content">
                    <!-- Home Team-->
                    <div class="single-match-left text-center">
                        <div class="home-logo">
                            <img src="<?php echo $home_logo_url; ?>" alt="home-logo">
                        </div>
                        <div class="home-name">
                            <p><?php echo $home; ?></p>
                        </div>
                    </div>

                    <!-- Score -->
                    <div class="single-match-mid text-center">
                        <?php
                        // Check if the match is upcoming (elapsed_time is empty)
                        if (empty($e_time)) {
                            // Display the match time in the "Score" section
                            $m_time = $match['match_time'];
                        ?>
                            <p><?php echo $m_time; ?></p>
                        <?php
                        } else {
                            // The match has started, display the actual scores
                        ?>
                            <div class="actual-scores">
                                <div class="home-score">
                                    <p id="home-score">
                                        <?php echo $home_score; ?>
                                    </p>
                                </div>
                                <span>:</span>
                                <div class="away-score">
                                    <p id="away-score">
                                        <?php echo $away_score; ?>
                                    </p>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="elapsed-time">
                            <p id="elapsed-time"><?php echo $e_time; ?>'</p>
                            <p id="elapsed-hours"><?php echo $elapsed_hours; ?>H</p> <!-- Display elapsed time in hours -->
                        </div>
                    </div>

                    <!-- Away Team -->
                    <div class="single-match-right text-center">
                        <div class="away-logo">
                            <img src="<?php echo $away_logo_url; ?>" alt="away-logo">
                        </div>
                        <div class="away-name">
                            <p><?php echo $away; ?></p>
                        </div>
                    </div>
                </div>
            </section>
            <section class="match-simulation">
                <h2>Match Simulation</h2>
                <iframe src="<?php echo $anim_url; ?>" frameborder="0" width="600" height="338"></iframe>
            </section>
        </main>

        <?php
    }
}

    get_footer();

?>

<script>
jQuery(document).ready(function($) {
    $(".owl-carousel").owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        items: 1
    });
});
</script>
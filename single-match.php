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
        $match_id = get_post_meta(get_the_ID(), 'match_id', true);
        $commentator = get_post_meta(get_the_ID(), 'commentator', true);
        // $match_status = get_post_meta(get_the_ID(), 'status', true);

        //Stream
        $video_url = get_post_meta(get_the_ID(), 'video_data_urls', true);
        $video_url = json_decode($video_url, true);  // Convert the JSON string back to PHP array
        
        $default_logo_url = get_stylesheet_directory_uri() . '/assets/images/football-min.png';
        // $live_bg = '/wp-content/themes/force-child/assets/images/live-bg.webp';        

        $home_logo_url = $home_logo ?: $default_logo_url;
        $away_logo_url = $away_logo ?: $default_logo_url;

        $elapsed_hours = floor($e_time / 60);

        $m_date = date('m/d/y', strtotime($m_time));

        $match_time = date('H:i', strtotime($m_time));
        $match_date = date('m-d', strtotime($m_time));
?>
        <!-- Output the match content -->
        <main class="main-layout">
            <section class="team-container" style="margin-top: 1rem;">
                <div class="tournament-details">
                    <h3><?php echo $t_name; ?></h3>
                </div>
                <!-- Single Match Content -->
                <div class="single-match-content">
                    <!-- Home Team-->
                    <div class="single-match-left text-center">
                        <div class="home-logo">
                            <img src="<?php echo $home_logo_url; ?>" alt="<?php echo $away; ?>">
                        </div>
                        <div class="home-name">
                            <p><?php echo $home; ?></p>
                        </div>
                    </div>

                    <!-- Score -->
                    <div class="single-match-mid text-center">
                        <div class="elapsed-time">
                            <!-- <p id="elapsed-time"><?php //echo $e_time; ?>'</p>
                            <p id="elapsed-hours"><?php //echo $elapsed_hours; ?>H</p> -->
                            <p class="up-time"><?php echo $match_time; ?></p>
                            <p class="up-date"><?php echo $match_date; ?></p>
                        </div>
                    </div>

                    <!-- Away Team -->
                    <div class="single-match-right text-center">
                        <div class="away-logo">
                            <img src="<?php echo $away_logo_url; ?>" alt="<?php echo $away; ?>">
                        </div>
                        <div class="away-name">
                            <p><?php echo $away; ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="single-match-container">
                <!--Tournament Name -->
                <div class="match-details">
                    <h1>Watch <?php echo $home; ?> vs. <?php echo $away; ?> Live | <?php echo $m_date; ?></h1>
                </div>
                <div class="stream-container">
                    <div class="stream-box">
                        <!-- Stream Content Iframe -->
                        <div class="match-stream" id="match-stream">
                            <iframe src="<?php echo esc_url($video_url[0]['iframe']); ?>" width="100%" height="475"></iframe>
                        </div>

                        <!-- Video Buttons -->
                        <?php if (isset($video_url) && is_array($video_url)): ?>
                            <div class="video-buttons">
                            <?php if (!empty($commentator)): ?>
                                <div class="commentator-info">
                                    <p>🎙️<?php echo esc_html($commentator); ?></p>
                                </div>
                            <?php endif; ?>
                                <?php foreach ($video_url as $key => $video): ?>
                                    <button class="video-button" data-url="<?php echo esc_attr($video['iframe']); ?>">
                                        HD <?php echo $key + 1; ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="c-box">
                        <h3>Chat</h3>
                    </div>
                </div>
            </section>

            <section class="simulation-container">
                <div class="match-simulation">
                    <h2>Match Simulation</h2>
                    <iframe src="<?php echo $anim_url; ?>" frameborder="0" width="100%" height="442"></iframe>
                </div>
                <div class="aside-container">
                    <div class="top-box" >
                        <h2>Top Houses</h2>
                        <div class="tb-container">
                            Lorem ipsum dolor sit amet.
                        </div>
                    </div>
                    <div class="stat-box">
                        <h3>Match Statistics</h3>
                        <div class="stat-container">
                            Lorem ipsum dolor sit amet.
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <?php
    }
}

    get_footer();

?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const iframe = document.querySelector('#match-stream iframe');
        const container = document.querySelector('.video-buttons');
        const buttons = document.querySelectorAll('.video-button');

        // Set the first button as active initially
        if (buttons.length) {
            buttons[0].classList.add('active');
        }

        container.addEventListener('click', event => {
            const button = event.target;
            if (button.classList.contains('video-button')) {
                iframe.src = button.getAttribute('data-url');
                buttons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            }
            
        });
    });
</script>
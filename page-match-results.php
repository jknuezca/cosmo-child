<?php
    $all_dates = get_all_match_dates(); 
    $today = date("Y-m-d");

    if (in_array($today, $all_dates) && !in_array($today, array_slice($all_dates, 0, 6))) {
        array_pop($all_dates);
        array_unshift($all_dates, $today);
    }

    $dates_to_display = array_reverse(array_slice($all_dates, 0, 6));
    
    get_header();
?>

<main id="match-results" class="main-layout">
    <section id="results-hero">
        <div class="results-main" role="main">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="results-content" style="padding-bottom: 1rem;">
                    <h1><?php the_field('results_title'); ?></h1>
                </div>
            <?php endwhile;  ?>
        </div>
    </section>

    <div class="match-tabs-container">
        <div class="tabs">
            <?php
                $activeDate = end($dates_to_display);  

                // If today's date is in $dates_to_display, then set $activeDate to today's date.
                if (in_array(date("Y-m-d"), $dates_to_display)) {
                    $activeDate = date("Y-m-d");
                }
                
                foreach ($dates_to_display as $index => $date): 
                    $activeClass = $date == $activeDate ? 'active' : '';
                    
                    //Display "Today" for the current date
                    $displayText = $date == date("Y-m-d") ? 'Today' : $date; 
            ?>
                <button class="tablink <?php echo $activeClass; ?>" onclick="openMatchDate(event, '<?php echo $date; ?>')"><?php echo $displayText; ?></button>
            <?php endforeach; ?>
        </div>

        <?php 
        // Display match details
        foreach ($dates_to_display as $date): ?>
            <?php 
                $displayStyle = $date == $activeDate ? 'block' : 'none';
            ?>
            <div id="<?php echo $date; ?>" class="tabcontent" style="display: <?php echo $displayStyle; ?>">
                <?php 
                // Fetch matches for this date sorted by tournament
                $matches = get_matches_by_tournament($date);
                
                // Group matches by tournament
                $grouped_matches = [];
                foreach ($matches as $match) {
                    $tournament_name = $match['tournament_name'];
                    if (!isset($grouped_matches[$tournament_name])) {
                        $grouped_matches[$tournament_name] = [];
                    }
                    $grouped_matches[$tournament_name][] = $match;
                }
                
                if (empty($grouped_matches)) {
                    echo "<p>No match results</p>";
                } else {
                    // Display matches grouped by tournament
                    foreach ($grouped_matches as $tournament => $tournament_matches): ?>

                        <table class="match-results-table">
                            <thead>
                                <tr>
                                    <th colspan="4"><?php echo $tournament; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tournament_matches as $match): 
                                    $default_logo_url = get_stylesheet_directory_uri() . '/assets/images/football-min.png';

                                    $match['home_logo_url'] = $match['home_logo'] ?: $default_logo_url;
                                    $match['away_logo_url'] = $match['away_logo'] ?: $default_logo_url;
                                ?>
                                    <tr data-permalink="<?php echo get_permalink($match['post_id']); ?>">
                                        <td class="football-match__teams table-column--main"> 
                                            <div class="football-team_name home_name">
                                                <span class="team-name__long">
                                                    <?php echo $match['home']; ?>
                                                    <img 
                                                        src="<?php echo $match['home_logo_url']; ?>" 
                                                        alt="<?php echo $match['home']; ?>"
                                                        width="30"
                                                        height="30"
                                                        loading="lazy"
                                                    >
                                                </span>
                                            </div>
                                            <div class="football-team__score"><?php echo $match['home_score']; ?></div>
                                            <span>-</span>
                                            <div class="football-team__score"><?php echo $match['away_score']; ?></div>
                                            <div class="football-team_name away_name">
                                                <span class="team-name__long">
                                                    <img 
                                                        src="<?php echo $match['away_logo_url']; ?>" 
                                                        alt="<?php echo $match['away']; ?>"
                                                        width="30"
                                                        height="30"
                                                        loading="lazy"
                                                    >
                                                    <?php echo $match['away']; ?>
                                                </span>
                                            </div>
                                            
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    <?php endforeach; 
                }
                ?>

            </div>
        <?php endforeach;?>
    </div>
</main>

<?php 
    get_footer();
?>
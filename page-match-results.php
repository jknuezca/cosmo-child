<?php

    $all_dates = get_all_match_dates(); 
    $dates_to_display = array_slice($all_dates, 0, 6);

    // If today's date isn't in those 6 dates, replace the last date with today's date.
    if (!in_array(date("Y-m-d"), $dates_to_display)) {
        array_pop($dates_to_display);  // Remove the last date
        array_unshift($dates_to_display, date("Y-m-d")); // Add current date to the beginning
    }

    $dates_to_display = array_reverse($dates_to_display);
    
    get_header();

?>

<main id="match-results" class="main-layout">
    <div class="match-tabs-container">
        <div class="tabs">
            <?php
                // Get the first date (should be current date)
                // $activeDate = reset($dates_to_display);  
                $activeDate = date("Y-m-d");
                foreach ($dates_to_display as $index => $date): 
                    $activeClass = $date == $activeDate ? 'active' : '';
            ?>
                <button class="tablink <?php echo $activeClass; ?>" onclick="openMatchDate(event, '<?php echo $date; ?>')"><?php echo $date; ?></button>
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
                                <!-- <tr>
                                    <th>Home Team</th>
                                    <th>Home Score</th>
                                    <th>Away Score</th>
                                    <th>Away Team</th>
                                </tr> -->
                            </thead>
                            <tbody>
                                <?php foreach ($tournament_matches as $match): ?>
                                    <tr>
                                        <td><?php echo $match['home']; ?></td>
                                        <td><?php echo $match['home_score']; ?></td>
                                        <td><?php echo $match['away_score']; ?></td>
                                        <td><?php echo $match['away']; ?></td>
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

<script>
    function openMatchDate(evt, date) {
        hideAllElementsByClassName("tabcontent");
        deactivateAllElementsByClassName("tablink");
        
        displayElementById(date);
        activateCurrentElement(evt);
    }

    function hideAllElementsByClassName(className) {
        const elements = document.getElementsByClassName(className);
        for (const element of elements) {
            element.style.display = "none";
        }
    }

    function deactivateAllElementsByClassName(className) {
        const elements = document.getElementsByClassName(className);
        for (const element of elements) {
            element.classList.remove("active");
        }
    }

    function displayElementById(id) {
        document.getElementById(id).style.display = "block";
    }

    function activateCurrentElement(evt) {
        evt.currentTarget.classList.add("active");
    }
</script>
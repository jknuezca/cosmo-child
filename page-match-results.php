<?php
    //Fetch all unique dates from matches
    $all_dates = get_all_match_dates();
    
    get_header();

?>
<div class="match-tabs-container">
    <div class="tabs">
        <?php 
            // Display tab buttons
            foreach ($all_dates as $date) {
                // Create button for the tab
                echo '<button class="tablink" onclick="openMatchDate(event, \'' . $date . '\')">' . $date . '</button>';
            } 
        ?>
    </div>

    <?php 
    // Display match details
    foreach ($all_dates as $date): ?>
        <div id="<?php echo $date; ?>" class="tabcontent">
            <?php 
            // Fetch matches for this date sorted by tournament.
            $matches = get_matches_by_date_sorted_by_tournament($date);
            
            // Group matches by tournament
            $grouped_matches = [];
            foreach ($matches as $match) {
                $tournament_name = $match['tournament_name'];
                if (!isset($grouped_matches[$tournament_name])) {
                    $grouped_matches[$tournament_name] = [];
                }
                $grouped_matches[$tournament_name][] = $match;
            }
            
            // Now display matches grouped by tournament
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

            <?php endforeach; ?>

        </div>
    <?php endforeach; ?>
</div>

<?php 
    get_footer();
?>

<script>
function openMatchDate(evt, date) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(date).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>

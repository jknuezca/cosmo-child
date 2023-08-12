<?php
// Assuming you have a way to fetch all unique dates from your matches.
    $dates = get_all_match_dates();
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
            <table class="match-results-table">
                <thead>
                    <tr>
                        <th>Tournament</th>
                        <th>Home Team</th>
                        <th>Away Team</th>
                        <th>Home Score</th>
                        <th>Away Score</th>
                        <!-- Add more columns as needed -->
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Fetch matches for this date and sort them by tournament.
                    $matches = get_matches_by_date_sorted_by_tournament($date);
                    foreach ($matches as $match): 
                    ?>
                        <tr>
                            <td><?php echo $match['tournament_name']; ?></td>
                            <td><?php echo $match['home']; ?></td>
                            <td><?php echo $match['away']; ?></td>
                            <td><?php echo $match['home_score']; ?></td>
                            <td><?php echo $match['away_score']; ?></td>
                            <!-- Add more data as needed -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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

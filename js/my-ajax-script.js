jQuery(document).ready(function($) {

    function update_match_data() {
        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'get_single_match_data',
                post_id: my_ajax_object.post_id // pass the current post ID to the AJAX handler
            },
            success: function(response) {
                // Update page's HTML based on the received response
                if(response.success) {
                    var match_data = response.data;

                    // console.log(match_data);

                    // Use received match_data to update your page
                    // For example, if you have an element with ID 'home-team-name'

                    $('#elapsed-time').text(match_data.elapsed_time + "'");
                    $('#elapsed-hours').text(Math.floor(match_data.elapsed_time / 60) + "H");
                    $('#home-score').text(match_data.home_team_score);
                    $('#away-score').text(match_data.away_team_score);
                    $('.match-simulation iframe').attr('src', response.anim_url);
                    
                    // Check match status and add or remove the "Match Ended" overlay
                    if (match_data.status !== '2') {
                        // If match ended and the overlay doesn't exist, add it
                        if ($('#match-stream .match-ended').length == 0) {
                            $('#match-stream').append('<div class="match-ended"><p>Match Ended</p></div>');
                        }
                    } else {
                        // If match is live, remove the overlay if it exists
                        $('#match-stream .match-ended').remove();
                    }

                    // console.log(response);

                } else {
                    console.error('AJAX error: ', response);
                }
            },
            error: function(errorThrown) {
                console.error('AJAX error: ', errorThrown);
            }
        });
    }

    // Call update_match_data immediately when the page loads
    update_match_data();

    // Then call it every 30 seconds
    setInterval(update_match_data, 30000); // Repeat every 30 seconds
});

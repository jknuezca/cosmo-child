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

                    console.log(match_data);
                    console.log("Match status: ", match_data.status);

                    // Convert match_time to a JavaScript Date object
                    var matchStartTime = new Date(match_data.match_time);
                    
                    // Assuming a match lasts 120 minutes (90 + 30 extra).
                    // 120 minutes * 60 seconds/minute * 1000 milliseconds/second
                    // var matchDuration = 120 * 60 * 1000;
                    var matchDuration = 2 * 60 * 60 * 1000;

                    // Calculate match end time
                    var matchEndTime = new Date(matchStartTime.getTime() + matchDuration);
                    var currentTime = new Date();
                    
                    // Use received match_data to update your page
                    // For example, if you have an element with ID 'home-team-name'

                    $('#elapsed-time').text(match_data.elapsed_time + "'");
                    $('#elapsed-hours').text(Math.floor(match_data.elapsed_time / 60) + "H");
                    $('#home-score').text(match_data.home_team_score);
                    $('#away-score').text(match_data.away_team_score);
                    $('.match-simulation iframe').attr('src', response.anim_url);
                    
                    // if (match_data.status !== '2' || !match_data.status || !match_data.video_data_urls || match_data.video_data_urls.length === 0)
                    if (match_data.status !== '2' || !match_data.status || currentTime > matchEndTime) {
                        if ($('#match-stream .match-ended').length == 0) {
                            $('#match-stream').append('<div class="match-ended"><p>Match Ended</p></div>');
                        }
                    } else {
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

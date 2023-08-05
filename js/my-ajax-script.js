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

                    // Check if video_data_urls array is not empty and has an iframe field
                    // if(match_data.video_data_urls && match_data.video_data_urls.length > 0) {
                    //     $('.match-stream iframe').attr('src', match_data.video_data_urls[1].iframe);
                    // } else {
                    //     console.log('No video URLs available');
                    // }

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

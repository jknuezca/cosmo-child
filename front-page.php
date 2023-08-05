<?php
/**
 * Template Name: Home Page
 */

get_header();

?>

<main class="main-layout">
    <section class="match-list-container">
        <div id="match-list">
            <!-- Match items will be dynamically inserted here -->
        </div>
    </section>
</main>

<?php get_footer(); ?>

<script>
    // Function called every 30 seconds
    function fetchMatchData() {
        // Create an XMLHttpRequest object
        var xhr = new XMLHttpRequest();
        
        // Define the type of request, the URL, and whether the request should be asynchronous
        xhr.open("GET", "<?php echo get_stylesheet_directory_uri(); ?>/fetch-match-data.php", true);
        
        // Function to handle the response
        xhr.onreadystatechange = function() {
            // Check if the request is complete
            if (xhr.readyState === 4) {
                // Check if the request was successful
                if (xhr.status === 200) {
                    // Update the match list container with the new HTML
                    document.getElementById("match-list").innerHTML = xhr.responseText;
                }
            }
        };
        
        // Send the request
        xhr.send();
    }

    // Fetch and update matches initially
    fetchMatchData();

    // Fetch and update matches every 30 seconds
    setInterval(fetchMatchData, 30000);
</script>


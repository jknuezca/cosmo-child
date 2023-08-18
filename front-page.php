<?php
/**
 * Template Name: Home Page
 */

get_header();

?>

<main class="main-layout">
    <section id="hero">
        <div class="hero-main" role="main">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="hero-content">
                    <h1><?php the_field('hero_title'); ?></h1>
                    <p><?php the_field('hero_description'); ?></p>
                </div>
                <div class="hero-image">
                    <img src="<?php the_field('hero_image'); ?>" alt="" />
                </div>
            <?php endwhile;  ?>
        </div>
    </section>

    <section class="match-list-container">
        <div id="match-list">
            <!-- Match items will be dynamically inserted here -->
        </div>
    </section>

    <section class="main-content">
        <?php echo the_content(); ?>
    </section>
</main>

<?php get_footer(); ?>

<script>
    async function fetchMatchData() {
        try {
            const response = await fetch("<?php echo get_stylesheet_directory_uri(); ?>/fetch-match-data.php", {
                method: 'GET'
            });
            if (response.ok) {
                const data = await response.text();
                document.getElementById("match-list").innerHTML = data;
            } else {
                console.error('Failed to fetch match data:', response.statusText);
            }
        } catch (error) {
            console.error('Error occurred while fetching match data:', error);
        }
    }

    // Fetch and update matches initially
    fetchMatchData();

    // Fetch and update matches every 30 seconds
    setInterval(fetchMatchData, 30000);
</script>


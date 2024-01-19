<?php
get_header(); 
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        while (have_posts()) :
            the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

            </article>

        <?php endwhile; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer(); // Appel du pied de page du thème WordPress
?>

<style>

.content-area {
/*     max-width: 800px; */
    margin: 20px auto;
    padding: 20px;
}

.entry-title {
    font-size: 28px;
    margin-bottom: 10px;
}

.entry-content {
    font-size: 16px;
    line-height: 1.6;
}

</style>

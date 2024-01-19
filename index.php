<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <h1 class='Titre_page_article'>Les actualités</h1>

        <p class='texte_important'>Les outils indispensables de l’étudiant</p>

        <p class='texte'>Chez Scholastique, nous croyons que la qualité du matériel informatique ne devrait pas être un luxe inaccessible pour les étudiants. Ainsi, notre vaste catalogue offre une multitude de choix pour répondre aux préférences individuelles, qu'il s'agisse de performances exceptionnelles pour les projets intensifs ou de solutions économiques mais fiables.</p>

        <p class='texte'>Scholastique s'efforce de créer un environnement où chaque étudiant peut trouver l'outil parfait, garantissant ainsi une expérience d'apprentissage sans compromis. Bien plus qu'une plateforme, Scholastique ouvre la voie à une nouvelle ère d'équipement étudiant, rendant la qualité accessible à tous les budgets.</p>

        <div class="article-grid">

            <?php
            $count = 0;
            if (have_posts()) :
                while (have_posts()) : the_post();
            ?>
                <div class="article-card">
					<?php the_post_thumbnail();  ?>
					<div class='card_contenu'>
                    	<h3 class='titre_article'><?php the_title(); ?></h3>
                    	<p class='texte_article'><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                    	<a href="<?php the_permalink(); ?>" class="button">Voir l'article</a>
					</div>
                </div>

            <?php
                $count++;
                if ($count % 3 == 0) :
                    echo '</div><div class="article-grid">';
                endif;
                endwhile;
            else :
                echo '<p>Aucun article trouvé.</p>';
            endif;
            ?>

        </div>

        <?php
        the_posts_pagination(array(
            'prev_text' => 'Précédent',
            'next_text' => 'Suivant',
        ));
        ?>

    </main>
</div>

<?php get_footer(); ?>
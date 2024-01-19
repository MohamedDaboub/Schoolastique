<?php
/*
Template Name: Boutique Personnalisée
*/
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <div class="custom-shop-container">

            <div class="custom-shop-title">
                <h1  class='Titre_page_article'>Tous les produits</h1>
            </div>

            <div class="custom-shop-filters">
                <form method="get">
                    <select name="category-filter">
                        <option value="">Toutes les catégories</option>
                        <?php
                        $categories = get_terms('categorie');
                        foreach ($categories as $category) {
                            $selected = isset($_GET['category-filter']) && $_GET['category-filter'] === $category->slug ? 'selected' : '';
                            echo '<option value="' . $category->slug . '" ' . $selected . '>' . $category->name . '</option>';
                        }
                        ?>
                    </select>

                    <select name="popularity-filter">
                        <option value="">Par popularité</option>
                        <option value="popular" <?php echo isset($_GET['popularity-filter']) && $_GET['popularity-filter'] === 'popular' ? 'selected' : ''; ?>>Populaires</option>
                        <option value="unpopular" <?php echo isset($_GET['popularity-filter']) && $_GET['popularity-filter'] === 'unpopular' ? 'selected' : ''; ?>>Moins populaires</option>
                    </select>

                    <input type="number" name="price-filter" placeholder="Prix maximum" value="<?php echo isset($_GET['price-filter']) ? esc_attr($_GET['price-filter']) : ''; ?>">
                    
                    <input class="filtres_active" type="submit" value="Filtrer">
                </form>
            </div>

            <div class="custom-shop-products">
                <?php
                $args = array(
                    'post_type' => 'produit',
                    'posts_per_page' => 9,
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                );
                if (isset($_GET['category-filter']) && !empty($_GET['category-filter'])) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'categorie',
                            'field'    => 'slug',
                            'terms'    => sanitize_text_field($_GET['category-filter']),
                        ),
                    );
                }

                if (isset($_GET['popularity-filter']) && !empty($_GET['popularity-filter'])) {
                }

                if (isset($_GET['price-filter']) && !empty($_GET['price-filter'])) {
                    $args['meta_query'] = array(
                        array(
                            'key'     => '_regular_price',
                            'value'   => sanitize_text_field($_GET['price-filter']),
                            'type'    => 'NUMERIC',
                            'compare' => '<=',
                        ),
                    );
                }

                $custom_query = new WP_Query($args);

                if ($custom_query->have_posts()) :
                    while ($custom_query->have_posts()) : $custom_query->the_post();
                        $regular_price = get_post_meta(get_the_ID(), '_regular_price', true);
                        $sale_price = get_post_meta(get_the_ID(), '_sale_price', true);

                        echo '<div class="product-card">';
                        echo '<div class="product-image">' . get_the_post_thumbnail() . '</div>';
                        echo '<h2 class="titre_produit">' . get_the_title() . '</h2>';
                        echo '<p class="texte_produit">' . wp_trim_words(get_the_excerpt(), 20) . '</p>';

                        echo '<div class="product-prices">';
                        if (!empty($sale_price)) {
                            echo '<p class="sale-price">Prix en promo : ' . esc_html($sale_price) . '</p>';
                        } else {
                            echo '<p class="regular-price">Prix : ' . esc_html($regular_price) . '</p>';
                        }
                        echo '</div>';

                        $product_id = get_the_ID();
                        echo '<a href="' . esc_url(home_url('/seul-produit/?product_id=' . $product_id)) . '" class="product-button">Voir le produit</a>';

                        echo '</div>';
                    endwhile;

                    wp_reset_postdata();
                else :
                    echo '<p>Aucun produit trouvé.</p>';
                endif;
                ?>
            </div> 

        </div>
        <div class="pagination">
            <?php
            echo paginate_links(array(
                'total' => $custom_query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
            ));
            ?>
        </div>

    </main>
</div>

<?php
get_footer();
?>
<style>
.custom-shop-title {
    text-align: center;
    margin-bottom: 20px;
}
.custom-shop-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
	.filtres_active{
		background:#A6000D!important;
	}

.custom-shop-filters select,
.custom-shop-filters input {
    width: 250px; 
    margin-right: 10px;
    box-sizing: border-box;
	height:50px;
}

.custom-shop-products {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
	margin:50px 0px
}

.product-card {
    border: 1px solid #ddd;
    box-sizing: border-box;
}

.product-prices {
    margin-top: 10px;
	padding-left:16px;
	font-size:20px;
}
.product-button{
	       background-color: #A6000D;
        color: white;
        padding: 10px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 20px;
	   margin: 20px;
        float : right;		
}

.pagination {
    text-align: center;
    margin-top: 20px;
}
	.Titre_page_article{
    text-align: center;
    font-size: 40px;
    font-weight: bold;
}
.texte_important{
    font-size: 20px;
    font-weight: bold;
	padding : 25px 0px 0px 0px;
}
.texte{
    font-size: 20px;
}
	.titre_produit {
	padding: 10px 0px
    font-weight: bold;
		font-size:35px;
		padding-left: 16px;
}
	.texte_produit{
		font-size:20px;
		padding-left: 16px;
	}
@media screen and (max-width: 768px) {
    .custom-shop-filters select,
    .custom-shop-filters input {
        width: 100%;
        margin-right: 0;
    }

    .custom-shop-products {
        grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
    }
}

@media screen and (max-width: 480px) {
    .custom-shop-title {
        font-size: 30px;
    }

    .custom-shop-filters select,
    .custom-shop-filters input {
        height: 40px; 
    }

    .product-prices {
        font-size: 18px; 
    }

    .product-button {
        font-size: 18px;
        margin: 10px; 
    }

    .Titre_page_article {
        font-size: 30px; 
    }

    .texte_important {
        font-size: 16px;
    }

    .texte,
    .titre_produit,
    .texte_produit {
        font-size: 16px;
    }
}
</style>
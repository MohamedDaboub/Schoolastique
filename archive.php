<?php
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <div class="custom-shop-container">

            <div class="custom-shop-title">
                <h1 class='Titre_page_article'><?php single_cat_title(); ?></h1>
            </div>

            <div class="custom-shop-products">
                <?php
                if (have_posts()) :
                    while (have_posts()) : the_post();
                        $regular_price = get_post_meta(get_the_ID(), '_regular_price', true);
                        $sale_price = get_post_meta(get_the_ID(), '_sale_price', true);

                        echo '<div class="product-card">';
                        echo '<img class="product-image">' . get_the_post_thumbnail();
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
                    echo '<p>Aucun produit trouvé dans cette catégorie.</p>';
                endif;
                ?>
            </div>

        </div>

    </main>
</div>

<?php
get_footer();
?>
<style scoped>
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

    .custom-shop-filters select,
    .custom-shop-filters input {
        width: 250px;
        margin-right: 10px;
        box-sizing: border-box;
        height: 50px;
    }

    .custom-shop-products {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin: 50px 0px;
    }

    .product-card {
        border: 1px solid #ddd;
        box-sizing: border-box;
    }

    .product-prices {
        margin-top: 10px;
        padding-left: 16px;
        font-size: 20px;
    }

    .product-button {
        background-color: #A6000D;
        color: white;
        padding: 10px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 20px;
        margin: 20px;
        float: right;
    }

    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .Titre_page_article {
        text-align: center;
        font-size: 40px;
        font-weight: bold;
    }

    .texte_important {
        font-size: 20px;
        font-weight: bold;
        padding: 25px 0px 0px 0px;
    }

    .texte {
        font-size: 20px;
    }

    .titre_produit {
        padding: 10px 0px;
        font-weight: bold;
        font-size: 35px;
        padding-left: 16px;
    }

    .texte_produit {
        font-size: 20px;
        padding-left: 16px;
    }
	img {
		height:45%
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
	

    /* Media Queries pour mobiles */
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

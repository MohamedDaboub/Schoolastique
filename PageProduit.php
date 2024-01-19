<?php
/*
Template Name: Seul Produit
*/
get_header();
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="single-product-container">
            <?php
            $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
            if ($product_id > 0) {
                $product_title = get_the_title($product_id);
                $product_description = get_post_field('post_content', $product_id);
                $regular_price = get_post_meta($product_id, '_regular_price', true);
                $sale_price = get_post_meta($product_id, '_sale_price', true);
                $gallery_images = get_post_meta($product_id, '_product_gallery', true);
                $gallery_images_array = explode(',', $gallery_images);
                $amazon_link = get_post_meta($product_id, '_amazon_link', true);
                echo '<div class="single-product-details">';
                if (!empty($gallery_images_array)) {
                    echo '<div class="product-gallery">';
                    foreach ($gallery_images_array as $image_url) {
                        echo '<img src="' . esc_url($image_url) . '" alt="Product Image">';
                    }
                    echo '</div>';
                }
                echo '<div class="product-info">';
                echo '<h1>' . esc_html($product_title) . '</h1>';
                echo '<div class="product-prices">';
                if (!empty($sale_price)) {
                    echo '<p class="regular-price">Prix normal : <del>' . esc_html($regular_price) . '€</del></p>';
                    echo '<p class="sale-price">Prix en promo : ' . esc_html($sale_price) . '€</p>';
                } else {
                    echo '<p class="regular-price">Prix : ' . esc_html($regular_price) . '€</p>';
                }
                echo '</div>';
                if (!empty($amazon_link)) {
                    echo '<a href="' . esc_url($amazon_link) . '" class="amazon-button" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 448 512"><path fill="#ffffff" d="M257.2 162.7c-48.7 1.8-169.5 15.5-169.5 117.5 0 109.5 138.3 114 183.5 43.2 6.5 10.2 35.4 37.5 45.3 46.8l56.8-56S341 288.9 341 261.4V114.3C341 89 316.5 32 228.7 32 140.7 32 94 87 94 136.3l73.5 6.8c16.3-49.5 54.2-49.5 54.2-49.5 40.7-.1 35.5 29.8 35.5 69.1zm0 86.8c0 80-84.2 68-84.2 17.2 0-47.2 50.5-56.7 84.2-57.8v40.6zm136 163.5c-7.7 10-70 67-174.5 67S34.2 408.5 9.7 379c-6.8-7.7 1-11.3 5.5-8.3C88.5 415.2 203 488.5 387.7 401c7.5-3.7 13.3 2 5.5 12zm39.8 2.2c-6.5 15.8-16 26.8-21.2 31-5.5 4.5-9.5 2.7-6.5-3.8s19.3-46.5 12.7-55c-6.5-8.3-37-4.3-48-3.2-10.8 1-13 2-14-.3-2.3-5.7 21.7-15.5 37.5-17.5 15.7-1.8 41-.8 46 5.7 3.7 5.1 0 27.1-6.5 43.1z"/></svg>
                    Acheter sur Amazon
                    </a>';
                }
                echo '</div>';
                echo '</div>'; 
                echo '<div class="product-description">' . apply_filters('the_content', $product_description) . '</div>';
                $terms = get_the_terms($product_id, 'categorie');
                if (!empty($terms) && is_array($terms)) {
                    $category_slug = $terms[0]->slug;
                    $args = array(
                        'post_type' => 'produit',
                        'posts_per_page' => 4,
                        'post__not_in' => array($product_id),
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'categorie',
                                'field' => 'slug',
                                'terms' => $category_slug,
                            ),
                        ),
                    );
        $related_products = new WP_Query($args);
    if ($related_products->have_posts()) {
        echo '<div class="related-products-container">';
        echo '<h2 class="related-products-titre">Autres produits qui pourraient vous intéresser</h2>';
        echo '<div class="related-products">';
        while ($related_products->have_posts()) {
            $related_products->the_post();
            $related_product_id = get_the_ID();
            $related_product_title = get_the_title();
            $related_product_permalink = get_permalink($related_product_id);
            $related_product_regular_price = get_post_meta($related_product_id, '_regular_price', true);
            $related_product_sale_price = get_post_meta($related_product_id, '_sale_price', true);
            echo '<div class="card_cat">';
            echo '<div class="card_cat-product">';
			echo '<a href="' . esc_url(home_url("/seul-produit/?product_id=$related_product_id")) .'">';
            echo '<img class="card_img" src="' . esc_url(get_the_post_thumbnail_url($related_product_id, 'medium')) . '" alt="' . esc_attr($related_product_title) . '">';
			echo '<div class="card_contenu">';
			echo '<h3>' . esc_html($related_product_title) . '</h3>';
            echo '<p class="related-product-price">';
            if (!empty($related_product_sale_price)) {
                echo '<span class="sale-price">' . esc_html($related_product_sale_price) . '€</span>';
            } else {
                echo '<span class="regular-price">' . esc_html($related_product_regular_price) . '€</span>';
            }
            echo '</p>';
			echo '</div>';
			echo '</a>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        wp_reset_postdata();
    }
                }
            } else {
                echo '<p>Aucun produit spécifié.</p>';
            }
            ?>
        </div>
    </main>
</div>
<?php
get_footer();
?>
<style>
.single-product-details {
    display: grid;
    grid-template-columns: 1fr 1fr; 
    gap: 20px;
}
.product-gallery {
    grid-column: 1 / 2;
    grid-row: 1 / span 2;
}
.product-gallery img {
    width: 100%;
    height: auto;
    display: block;
    margin: 20px 20px;
}
.product-info {
    grid-column: 2 / 3;
    grid-row: 1 / span 1; 
}
.product-info h1 {
    margin-bottom: 10px;
}
.product-prices {
    margin-top: 20px;
	font-size:25px;
}
.regular-price, .sale-price {
    margin-bottom: 10px;
	font-size:25px;
}
.amazon-button {
    display: flex;
    align-items: center;
    background-color: #A6000D;
    color: #ffffff;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    margin-right: 10px;
	width:230px!important;
}

svg {
    margin-right: 10px;
    margin-top: 0;
}
.amazon-button:hover {
    background-color: #Af000D;
    color: #ffffff;
}
.back-to-shop {
    display: inline-block;
    background-color: #333;
    color: #ffffff;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 10px;
}
.product-description {
    grid-column: 1 / -1; 
    margin-top: 20px;
	font-size:20px;
}
.related-products-container {
    margin-top: 40px;
}
.related-products-titre{
    margin-bottom: 20px;
    text-align: center;
    font-size: 30px;
    font-weight: bold;
}
.related-products {
    display: flex;
    flex-wrap: wrap;
}
.related-product {
    flex: 1;
    margin-right: 20px;
}
.related-product h3 {
    margin-bottom: 10px;
}
@media (max-width: 768px) {
    .single-product-details {
        grid-template-columns: 1fr; 
    }
    .product-gallery, .product-info, .product-description {
        grid-column: auto;
        grid-row: auto;
    }
    .amazon-button, .back-to-shop {
        width: 100%;
        margin-right: 0;
    }
    .related-product {
        margin-right: 0;
        margin-bottom: 20px;
    }
}

.card_cat{
    display: grid;
    grid-template-columns: 280px 2fr; 
    gap: 20px;
	margin: 30px auto;
}
.card_cat-product{
	background: #fff;
	border-radius:8px;
	color: #fff;
	box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.75);
}
	.card_contenu{
		padding:10px;
		margin-left:16px;
	}
	.related-product-price{
		color:#000;
		margin-top:10px;
	}
	.card_img{
		border-radius:8px 8px 0px 0px;
	}
</style>
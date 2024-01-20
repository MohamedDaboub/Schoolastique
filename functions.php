<?php
/**
 * Schoolastique Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Schoolastique
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_SCHOOLASTIQUE_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'schoolastique-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_SCHOOLASTIQUE_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );
function init_my_custom() {
    // Enregistrement du type de publication personnalisé 'produit'
    register_post_type(
        'produit',
        array(
            'label' => 'Produit',
            'labels' => array(
                'name' => 'Produits',
                'singular_name' => 'Produit',
                'all_items' => 'Tous les Produits',
                'add_new_item' => 'Ajouter un Produit',
                'edit_item' => 'Éditer le Produit',
                'new_item' => 'Nouveau Produit',
                'view_item' => 'Voir le Produit',
                'search_items' => 'Rechercher parmi les Produits',
                'not_found' => 'Pas de Produit trouvé',
                'not_found_in_trash'=> 'Pas de Produit dans la corbeille'
            ),
            'public' => true,
            'capability_type' => 'post',
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'custom-fields'
            ),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-cart',
            'has_archive' => true
        )
    );

    // Enregistrement de la taxonomie 'categorie' pour le type de publication personnalisé 'produit'
    register_taxonomy(
        'categorie',
        'produit',
        array(
            'label' => 'Catégorie',
            'labels' => array(
                'singular_name' => 'Catégorie',
                'search_items' => 'Rechercher des catégories',
                'popular_items' => 'Catégories populaires',
                'all_items' => 'Toutes les catégories',
                'edit_item' => 'Éditer la catégorie',
                'update_item' => 'Mettre à jour la catégorie',
                'add_new_item' => 'Ajouter une nouvelle catégorie',
                'new_item_name' => 'Nouveau nom de catégorie',
                'menu_name' => 'Catégories'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true
        )
    );

    // Ajouter des métaboxes pour la page d'ajout/édition de produits
    add_action('add_meta_boxes', 'add_product_metaboxes');

    // Enregistrement des données de métaboxes
    add_action('save_post', 'save_product_metaboxes');
}

function add_product_metaboxes() {
    add_meta_box('product_image_metabox', 'Image du Produit', 'product_image_metabox_callback', 'produit', 'normal', 'high');
    add_meta_box('product_gallery_metabox', 'Galerie du Produit', 'product_gallery_metabox_callback', 'produit', 'normal', 'high');
    add_meta_box('product_price_metabox', 'Prix du Produit', 'product_price_metabox_callback', 'produit', 'normal', 'high');
    add_meta_box('amazon_link_metabox', 'Lien Amazon', 'amazon_link_metabox_callback', 'produit', 'normal', 'high');
    add_meta_box('product_home_display_metabox', 'Affichage sur la page d\'accueil', 'product_home_display_metabox_callback', 'produit', 'side', 'high');
}

function product_image_metabox_callback($post) {
    // Affichez ici le contenu de la métabox pour l'image principale du produit
    $product_image = get_post_meta($post->ID, '_product_image', true);
    ?>
    <label for="product_image">Image du Produit</label>
    <input type="text" id="product_image" name="product_image" value="<?php echo esc_attr($product_image); ?>" />
    <button id="upload_image_button" class="button">Télécharger une image</button>
    <script>
        jQuery(document).ready(function($) {
            var custom_uploader;
            $('#upload_image_button').click(function(e) {
                e.preventDefault();
                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choisir une image',
                    button: {
                        text: 'Choisir'
                    },
                    multiple: false
                });
                custom_uploader.on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#product_image').val(attachment.url);
                });
                custom_uploader.open();
            });
        });
    </script>
    <?php
}

function product_gallery_metabox_callback($post) {
    // Affichez ici le contenu de la métabox pour la galerie du produit
    $product_gallery = get_post_meta($post->ID, '_product_gallery', true);
    ?>
    <label for="product_gallery">Galerie du Produit</label>
    <input type="text" id="product_gallery" name="product_gallery" value="<?php echo esc_attr($product_gallery); ?>" />
    <button id="upload_gallery_button" class="button">Télécharger une galerie</button>
    <script>
        jQuery(document).ready(function($) {
            var gallery_uploader;
            $('#upload_gallery_button').click(function(e) {
                e.preventDefault();
                if (gallery_uploader) {
                    gallery_uploader.open();
                    return;
                }
                gallery_uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choisir une galerie',
                    button: {
                        text: 'Choisir'
                    },
                    multiple: true // Permet la sélection multiple
                });
                gallery_uploader.on('select', function() {
                    var attachments = gallery_uploader.state().get('selection').map(function(attachment) {
                        return attachment.toJSON();
                    });
                    var gallery_urls = attachments.map(function(attachment) {
                        return attachment.url;
                    });
                    $('#product_gallery').val(gallery_urls.join(','));
                });
                gallery_uploader.open();
            });
        });
    </script>
    <?php
}

function product_price_metabox_callback($post) {
    // Affichez ici le contenu de la métabox pour les prix du produit (régulier et promo)
    $regular_price = get_post_meta($post->ID, '_regular_price', true);
    $sale_price = get_post_meta($post->ID, '_sale_price', true);
    ?>
    <label for="regular_price">Prix Régulier</label>
    <input type="text" id="regular_price" name="regular_price" value="<?php echo esc_attr($regular_price); ?>" /><br>

    <label for="sale_price">Prix en Promotion</label>
    <input type="text" id="sale_price" name="sale_price" value="<?php echo esc_attr($sale_price); ?>" />
    <?php
}

function amazon_link_metabox_callback($post) {
    $amazon_link = get_post_meta($post->ID, '_amazon_link', true);
    ?>
    <label for="amazon_link">Lien Amazon</label>
    <input type="text" id="amazon_link" name="amazon_link" value="<?php echo esc_attr($amazon_link); ?>" />
    <?php
}

function product_home_display_metabox_callback($post) {
    $display_on_home = get_post_meta($post->ID, '_display_on_home', true);

    // Vérifie si l'article est nouvellement créé (ID non défini)
    $is_new_post = empty($post->ID);
    if ($is_new_post) {
        // Si c'est un nouvel article, cochez automatiquement la case à cocher
        $display_on_home = 'on';
    }
    ?>
    <label for="display_on_home">Afficher sur la page d'accueil</label>
    <input type="checkbox" id="display_on_home" name="display_on_home" <?php checked($display_on_home, 'on'); ?> />
    <?php
}

function save_product_metaboxes($post_id) {
    // Enregistrez ici les données de vos métaboxes lors de la sauvegarde du produit
    if (isset($_POST['product_image'])) {
        update_post_meta($post_id, '_product_image', sanitize_text_field($_POST['product_image']));
    }

    if (isset($_POST['product_gallery'])) {
        update_post_meta($post_id, '_product_gallery', sanitize_text_field($_POST['product_gallery']));
    }

    if (isset($_POST['regular_price'])) {
        update_post_meta($post_id, '_regular_price', sanitize_text_field($_POST['regular_price']));
    }

    if (isset($_POST['sale_price'])) {
        update_post_meta($post_id, '_sale_price', sanitize_text_field($_POST['sale_price']));
    }

    if (isset($_POST['amazon_link'])) {
        update_post_meta($post_id, '_amazon_link', sanitize_text_field($_POST['amazon_link']));
    }

    // Enregistrez l'état de la case à cocher pour l'affichage sur la page d'accueil
    $display_on_home = isset($_POST['display_on_home']) ? 'on' : 'off';
    update_post_meta($post_id, '_display_on_home', $display_on_home);

    // Mettez à jour les produits sélectionnés
    if ($display_on_home === 'on') {
        $selected_products = get_option('selected_products', array());
        $selected_products[] = $post_id;
        update_option('selected_products', array_unique($selected_products));
    } else {
        $selected_products = get_option('selected_products', array());
        $selected_products = array_diff($selected_products, array($post_id));
        update_option('selected_products', $selected_products);
    }
}

function enqueue_custom_scripts() {
    wp_enqueue_script('custom-metabox', get_template_directory_uri() . '/product_metabox.js', array('jquery'), null, true);
}

add_action('init', 'init_my_custom');

// Ajout du shortcode
function custom_product_cards_shortcode($atts) {
    ob_start();

    // Récupérez les produits sélectionnés avec les cases à cocher
    $selected_products = get_option('selected_products', array());

    // Query pour récupérer les produits
    $args = array(
        'post_type' => 'produit',
        'post__in' => $selected_products,
        'posts_per_page' => 4,
    );

    $products_query = new WP_Query($args);

    // Affichez les cartes
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();

            // Personnalisez le contenu de la carte ici
            ?>
            <div class="product-card">
                <h3><?php the_title(); ?></h3>
                <!-- Ajoutez le contenu de la carte en fonction de vos besoins -->
            </div>
            <?php
        }
    } else {
        echo 'Aucun produit sélectionné.';
    }

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('custom_product_cards', 'custom_product_cards_shortcode');
// Ajouter une colonne à la liste des articles
function add_display_on_home_column($columns) {
    $columns['display_on_home'] = 'Afficher sur la page d\'accueil';
    return $columns;
}
add_filter('manage_produit_posts_columns', 'add_display_on_home_column');

// Afficher le contenu de la colonne
function display_on_home_column_content($column, $post_id) {
    if ($column == 'display_on_home') {
        $display_on_home = get_post_meta($post_id, '_display_on_home', true);
        echo '<input type="checkbox" disabled ' . checked($display_on_home, 'on', false) . '>';
    }
}
add_action('manage_produit_posts_custom_column', 'display_on_home_column_content', 10, 2);

// Rendre la colonne triable
function make_display_on_home_column_sortable($columns) {
    $columns['display_on_home'] = 'display_on_home';
    return $columns;
}
add_filter('manage_edit-produit_sortable_columns', 'make_display_on_home_column_sortable');

// Trier les articles par la colonne display_on_home
function display_on_home_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ('display_on_home' == $orderby) {
        $query->set('meta_key', '_display_on_home');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'display_on_home_column_orderby');

// Activer Quick Edit pour la colonne
function enable_quick_edit_for_display_on_home_column($columns) {
    $columns['display_on_home'] = 'display_on_home';
    return $columns;
}
add_filter('manage_edit-produit_columns', 'enable_quick_edit_for_display_on_home_column');

// Ajouter la case à cocher à l'affichage rapide
function add_display_on_home_quick_edit_field($column_name, $post_type) {
    if ($column_name != 'display_on_home') {
        return;
    }

    echo '<fieldset class="inline-edit-col-right"><div class="inline-edit-col">';

    // Récupérer la valeur actuelle
    $display_on_home = get_post_meta(get_the_ID(), '_display_on_home', true);

    // Afficher la case à cocher
    ?>
    <label class="inline-edit-group">
        <span class="title">Afficher sur la page d'accueil</span>
        <input type="checkbox" name="_display_on_home" <?php checked($display_on_home, 'on'); ?>>
    </label>
    <?php

    echo '</div></fieldset>';
}
add_action('quick_edit_custom_box', 'add_display_on_home_quick_edit_field', 10, 2);

// Enregistrer la valeur lors de l'enregistrement rapide
function save_display_on_home_quick_edit_field($post_id, $post) {
    if ($post->post_type != 'produit' || !current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_REQUEST['_display_on_home'])) {
        update_post_meta($post_id, '_display_on_home', sanitize_text_field($_REQUEST['_display_on_home']));
    }
}
add_action('save_post', 'save_display_on_home_quick_edit_field', 10, 2);


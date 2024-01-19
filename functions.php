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

    add_action('add_meta_boxes', 'add_product_metaboxes');

    add_action('save_post', 'save_product_metaboxes');
}

function add_product_metaboxes() {
    add_meta_box('product_image_metabox', 'Image du Produit', 'product_image_metabox_callback', 'produit', 'normal', 'high');
    add_meta_box('product_gallery_metabox', 'Galerie du Produit', 'product_gallery_metabox_callback', 'produit', 'normal', 'high');
    add_meta_box('product_price_metabox', 'Prix du Produit', 'product_price_metabox_callback', 'produit', 'normal', 'high');
    add_meta_box('amazon_link_metabox', 'Lien Amazon', 'amazon_link_metabox_callback', 'produit', 'normal', 'high');
}

function product_image_metabox_callback($post) {
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
                    multiple: true 
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
    <button id="upload_amazon_button" class="button">Télécharger un lien Amazon</button>
    <script>
        jQuery(document).ready(function($) {
            var amazon_uploader;
            $('#upload_amazon_button').click(function(e) {
                e.preventDefault();
                if (amazon_uploader) {
                    amazon_uploader.open();
                    return;
                }
                amazon_uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choisir un lien Amazon',
                    button: {
                        text: 'Choisir'
                    },
                    multiple: false
                });
                amazon_uploader.on('select', function() {
                    var attachment = amazon_uploader.state().get('selection').first().toJSON();
                    $('#amazon_link').val(attachment.url);
                });
                amazon_uploader.open();
            });
        });
    </script>
    <?php
}

function save_product_metaboxes($post_id) {

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
}

function enqueue_custom_scripts() {
    wp_enqueue_script('custom-metabox', get_template_directory_uri() . '/path/to/product_metabox.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_custom_scripts');

add_action('init', 'init_my_custom');
<?php

add_action('wp_ajax_load_subcategories_by_parent', 'custom_load_subcategories_by_parent');
add_action('wp_ajax_nopriv_load_subcategories_by_parent', 'custom_load_subcategories_by_parent');

function custom_load_subcategories_by_parent()
{
    $parent_slug = isset($_POST['parent_slug']) ? sanitize_text_field(wp_unslash($_POST['parent_slug'])) : '';

    if (empty($parent_slug)) {
        wp_send_json_error([
            'message' => 'Missing parent category.',
        ]);
    }

    $html = custom_render_subcategory_collection($parent_slug);

    wp_send_json_success([
        'html' => $html,
    ]);
}

function custom_render_subcategory_collection($parent_slug = '')
{
    if (empty($parent_slug)) {
        return '<div class="subcategory-collection__empty">No category found.</div>';
    }

    $parent_term = get_term_by('slug', $parent_slug, 'product_cat');

    if (!$parent_term || is_wp_error($parent_term)) {
        return '<div class="subcategory-collection__empty">Parent category not found.</div>';
    }

    $children = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => $parent_term->term_id,
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
    ]);

    if (empty($children) || is_wp_error($children)) {
        return '<div class="subcategory-collection__empty">No subcategories found.</div>';
    }

    ob_start();
?>
    <div class="subcategory-collection__grid">
        <?php foreach ($children as $child) : ?>
            <?php
            $thumbnail_id = get_term_meta($child->term_id, 'thumbnail_id', true);
            $thumbnail_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
            ?>
            <a class="subcategory-collection__item" href="<?php echo esc_url(get_term_link($child)); ?>">
                <img class="subcategory-collection__thumnail" src="<?php echo esc_url($thumbnail_url) ?>" />
                <span class="subcategory-collection__title"><?php echo esc_html($child->name); ?></span>

                <?php if (!empty($child->description)) : ?>
                    <span class="subcategory-collection__desc"><?php echo esc_html($child->description); ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php
    return ob_get_clean();
}

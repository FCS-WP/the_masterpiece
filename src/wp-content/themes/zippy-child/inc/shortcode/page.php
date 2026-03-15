<?php

add_shortcode('page_breadcrumb_shortcode', 'get_page_breadcrumn_shortcode');
function get_page_breadcrumn_shortcode()
{
    if (is_front_page()) {
        return '';
    }
    $separator = ' / ';
    $home_text = 'Home';
    $breadcrumb = '<nav class="custom-breadcrumb">';

    $breadcrumb .= '<a href="' . esc_url(home_url('/')) . '">' . esc_html($home_text) . '</a>';

    if (is_page()) {
        global $post;

        if ($post->post_parent) {
            $ancestors = array_reverse(get_post_ancestors($post->ID));

            foreach ($ancestors as $ancestor) {
                $breadcrumb .= $separator . '<a href="' . esc_url(get_permalink($ancestor)) . '">' . esc_html(get_the_title($ancestor)) . '</a>';
            }
        }

        $breadcrumb .= $separator . '<span>' . esc_html(get_the_title()) . '</span>';
    } elseif (is_single()) {
        $categories = get_the_category();

        if (!empty($categories)) {
            $breadcrumb .= $separator . '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
        }

        $breadcrumb .= $separator . '<span>' . esc_html(get_the_title()) . '</span>';
    } elseif (is_category()) {
        $breadcrumb .= $separator . '<span>' . single_cat_title('', false) . '</span>';
    } elseif (is_search()) {
        $breadcrumb .= $separator . '<span>Search Results</span>';
    } elseif (is_404()) {
        $breadcrumb .= $separator . '<span>404 Not Found</span>';
    }

    $breadcrumb .= '</nav>';

    return $breadcrumb;
}

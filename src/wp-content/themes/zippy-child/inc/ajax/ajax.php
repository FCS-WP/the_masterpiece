<?php

/* Handle Render Sub Category Information */
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


/* Handle Contact Form */
function handle_contact_form_submission()
{
    if (
        !isset($_POST['contact_form_nonce']) ||
        !wp_verify_nonce(wp_unslash($_POST['contact_form_nonce']), 'contact_form_action')
    ) {
        wp_send_json_error(['message' => 'Security check failed.']);
    }

    $name = isset($_POST['contact_name']) ? sanitize_text_field(wp_unslash($_POST['contact_name'])) : '';
    $email = isset($_POST['contact_email']) ? sanitize_email(wp_unslash($_POST['contact_email'])) : '';
    $number = isset($_POST['contact_number']) ? sanitize_text_field(wp_unslash($_POST['contact_number'])) : '';
    $message = isset($_POST['contact_message']) ? sanitize_textarea_field(wp_unslash($_POST['contact_message'])) : '';
    $enquiry_type = isset($_POST['enquiry_type']) ? sanitize_text_field(wp_unslash($_POST['enquiry_type'])) : '';

    $allowed_enquiry_types = [
        'General Enquiry',
        'Product Enquiry',
        'Bespoke / Custom Request',
        'Professional Consultation',
        'Shipping / Returns',
        'Certification / Authentication',
    ];

    if (empty($name) || empty($email) || empty($message) || empty($enquiry_type)) {
        wp_send_json_error(['message' => 'Please fill in all required fields.']);
    }

    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Invalid email address.']);
    }

    if (!in_array($enquiry_type, $allowed_enquiry_types, true)) {
        wp_send_json_error(['message' => 'Invalid enquiry type.']);
    }

    $uploaded_image_url = '';

    if (!empty($_FILES['reference_image']['name'])) {
        $max_file_size = 5 * 1024 * 1024;

        if ((int) $_FILES['reference_image']['size'] > $max_file_size) {
            wp_send_json_error(['message' => 'Reference image must not exceed 5MB.']);
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';

        $file = $_FILES['reference_image'];
        $file['name'] = sanitize_file_name($file['name']);

        $allowed_mimes = [
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png'          => 'image/png',
            'gif'          => 'image/gif',
            'webp'         => 'image/webp',
        ];

        $filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $allowed_mimes);

        if (!$filetype['ext'] || !$filetype['type']) {
            wp_send_json_error(['message' => 'Only JPG, PNG, GIF, and WEBP images are allowed.']);
        }

        $upload_overrides = [
            'test_form' => false,
            'mimes'     => $allowed_mimes,
        ];

        $movefile = wp_handle_upload($file, $upload_overrides);

        if (isset($movefile['error'])) {
            wp_send_json_error(['message' => $movefile['error']]);
        }

        $uploaded_image_url = esc_url_raw($movefile['url']);
    }

    $to = get_option('admin_email');
    $subject = 'Contact Form: ' . $name;
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $name . ' <' . $email . '>',
    ];

    $email_message = '<p><strong>Name:</strong> ' . esc_html($name) . '</p>';
    $email_message .= '<p><strong>Email:</strong> ' . esc_html($email) . '</p>';
    $email_message .= '<p><strong>Phone:</strong> ' . esc_html($number) . '</p>';
    $email_message .= '<p><strong>Enquiry Type:</strong> ' . esc_html($enquiry_type) . '</p>';
    $email_message .= '<p><strong>Message:</strong></p><p>' . nl2br(esc_html($message)) . '</p>';

    if (!empty($uploaded_image_url)) {
        $email_message .= '<p><strong>Reference Image:</strong> <a href="' . esc_url($uploaded_image_url) . '" target="_blank">View Image</a></p>';
    }

    $sent = wp_mail($to, $subject, $email_message, $headers);

    if ($sent) {
        wp_send_json_success(['message' => 'Thank you! Your message has been sent successfully.']);
    } else {
        wp_send_json_error(['message' => 'Sorry, there was an error sending your message. Please try again.']);  
    }
}

add_action('wp_ajax_contact_form_submit', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_contact_form_submit', 'handle_contact_form_submission');
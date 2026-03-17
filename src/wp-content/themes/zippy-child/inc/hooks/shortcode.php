<?php 
/* Contact Form Short Code */
function contact_form_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'title' => 'Contact Us',
        'submit_text' => 'Send Message',
    ), $atts);

    ob_start();
?>
    <div class="contact-form-wrapper">
    <?php if (!empty($atts['title'])) : ?>
        <h2 class="contact-form-title"><?php echo esc_html($atts['title']); ?></h2>
    <?php endif; ?>

    <form class="contact-form" id="contact-form" method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('contact_form_action', 'contact_form_nonce'); ?>

        <div class="form-row">
            <div class="form-group">
                <label for="contact_name">Your Name <span class="required">*</span></label>
                <input type="text" id="contact_name" name="contact_name" required placeholder="Your Name">
            </div>

            <div class="form-group">
                <label for="contact_email">Your Email <span class="required">*</span></label>
                <input type="email" id="contact_email" name="contact_email" required placeholder="your.email@example.com">
            </div>
        </div>

        <div class="form-group">
            <label for="contact_number">Phone <span class="optional">(optional)</span></label>
            <input type="text" id="contact_number" name="contact_number" placeholder="Contact Number..">
        </div>

        <div class="form-group">
            <label for="enquiry_type">Enquiry Type <span class="required">*</span></label>
            <select id="enquiry_type" name="enquiry_type" required>
                <option value="">Select enquiry type</option>
                <option value="General Enquiry">General Enquiry</option>
                <option value="Product Enquiry">Product Enquiry</option>
                <option value="Bespoke / Custom Request">Bespoke / Custom Request</option>
                <option value="Professional Consultation">Professional Consultation</option>
                <option value="Shipping / Returns">Shipping / Returns</option>
                <option value="Certification / Authentication">Certification / Authentication</option>
            </select>
        </div>

        <div class="form-group">
            <label for="contact_message">Your Message <span class="required">*</span></label>
            <textarea id="contact_message" name="contact_message" rows="6" required placeholder="Your message here..."></textarea>
        </div>

        <div class="form-group">
            <label for="reference_image">Reference Image <span class="optional">(optional upload)</span></label>
            <input type="file" id="reference_image" name="reference_image" accept="image/*">
        </div>

        <div class="form-submit">
            <button type="submit" class="btn-primary submit-button">
                <?php echo esc_html($atts['submit_text']); ?>
            </button>
        </div>

        <div class="form-response" style="display: none;"></div>
    </form>
</div>
<?php
    return ob_get_clean();
}
add_shortcode('contact_form', 'contact_form_shortcode');
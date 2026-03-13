<?php

add_shortcode('sub_query_category_shortcode', 'custom_subcategory_by_parent');
function custom_subcategory_by_parent()
{
    ob_start();
?>
    <div class="subcategory-collection">
            
    </div>
<?php
    return ob_get_clean();
}

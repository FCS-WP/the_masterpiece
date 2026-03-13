/* Add arrow into products loop */
var hp_products = $(".hp-product .box-text");

hp_products.append(
  '<span class="right-arrow"><img src="/wp-content/uploads/2026/03/white-right-arrow.png"></span>',
);

/* Toggle Parent Category */
var sub_category = $(".subcategory-collection");

$(document).on("click", ".js-parent-category", function (e) {
  sub_category.find(".parent-category").remove();
  e.preventDefault();
  var parentCategory = "";
  if ($(this).hasClass("js-jewellery-category")) {
    parentCategory = "jewellery";
    getSubCategory(parentCategory);
    sub_category.append(
      `<div class ="parent-category">${parentCategory}</div>`,
    );
  } else if ($(this).hasClass("js-beads-category")) {
    parentCategory = "beads";
    sub_category.append(
      `<div class ="parent-category">${parentCategory}</div>`,
    );
  } else if ($(this).hasClass("js-jadeite-carvings-category")) {
    parentCategory = "jadeite-carvings";
    sub_category.append(
      `<div class ="parent-category">${parentCategory}</div>`,
    );
  }
});

function getSubCategory(parent_category) {
  $.ajax({
    url: "/wp-admin/admin-ajax.php",
    type: "POST",
    dataType: "json",
    data: {
      action: "load_subcategories_by_parent",
      parent_slug: parent_category,
    },
    success: function (response) {
      sub_category.append(response.data.html);
    },
    error: function () {
      console.log(1234);
    },
  });
}

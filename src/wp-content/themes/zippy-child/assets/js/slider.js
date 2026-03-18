/* Init Mobile Slider for Subcategory in Homepage */
function initSubcategorySlick() {
  if ($(window).width() <= 767) {
    if (!$(".subcategory-collection .subcategory-collection__grid").hasClass("slick-initialized")) {
      $(".subcategory-collection .subcategory-collection__grid").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        dots: true,
      });
    }
  } else {
    if ($(".subcategory-collection .subcategory-collection__grid").hasClass("slick-initialized")) {
      $(".subcategory-collection .subcategory-collection__grid").slick("unslick");
    }
  }
}

$(document).ready(function () {
  initSubcategorySlick();
});

$(window).on("resize", function () {
  initSubcategorySlick();
});

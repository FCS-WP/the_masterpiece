jQuery(document).ready(function ($) {
  $("#contact-form").on("submit", function (e) {
    e.preventDefault();

    const $form = $(this);
    const $response = $form.find(".form-response");
    const $submitBtn = $form.find(".submit-button");

    $submitBtn.addClass("loading");
    $submitBtn.append(
      `<span class="loader"></span>`
    );
    $submitBtn.prop("disabled", true);
    $response.hide().removeClass("success error");
    const formData = new FormData(this);
    formData.append("action", "contact_form_submit");
    $.ajax({
      url: "/wp-admin/admin-ajax.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $submitBtn.removeClass("loading");
        $submitBtn.find(".loader").remove();
        $submitBtn.prop("disabled", false);

        if (response.success) {
          $response.addClass("success").text(response.data.message).fadeIn();
          $form[0].reset();
        } else {
          $response.addClass("error").text(response.data.message).fadeIn();
        }
      },
      error: function () {
        $submitBtn.removeClass("loading");
        $submitBtn.find(".loader").remove();
        $submitBtn.prop("disabled", false);
        $response
          .addClass("error")
          .text("An error occurred. Please try again.")
          .fadeIn();
      },
    });
  });
});
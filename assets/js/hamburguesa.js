$(document).ready(function () {
    // Hamburguesa (mantenemos la funcionalidad en jQuery)
    $("#hamburguesa-navbar").click(function () {
      $(this).toggleClass("active");
      $("#navbar").toggleClass("active");
    });
  });
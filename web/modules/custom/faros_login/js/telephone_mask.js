(function (Drupal) {
  Drupal.behaviors.telephoneMask = {
    attach: function (context, settings) {
      var elements = context.querySelectorAll('.telephone-mask:not(.telephone-mask-processed)');
      elements.forEach(function (element) {
        element.classList.add('telephone-mask-processed');
        element.addEventListener('input', function () {
          var x = element.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
          element.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
      });
    }
  };
})(Drupal);

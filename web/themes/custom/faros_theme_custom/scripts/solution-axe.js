/**
 * @file
 * Slick Carousel Functions.
 *
 */

(function ($, Drupal) {
  "use strict";

  Drupal.behaviors.solutionAxe = {
    attach: function () {
      $('.btn-axe').on('click', function (e) {
        $('.result-axe').removeClass('d-block').addClass('d-none');
        $('.card-axe').removeClass('active');

        var id = $(this).attr('id');
        $('#result-' + id).removeClass('d-none').addClass('d-block').addClass('active');
        $('#' + id).parent('.card-axe').addClass('active');
      });
    },
  };
})(jQuery, Drupal);

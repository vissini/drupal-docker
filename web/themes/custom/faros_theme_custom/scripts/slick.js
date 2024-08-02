/**
 * @file
 * Slick Carousel Functions.
 *
 */

(function ($, Drupal) {
    "use strict";

    Drupal.behaviors.slickCarouselHome = {
      attach: function () {
        $('.slick-carousel-home').not('.slick-initialized').slick({
          slidesToShow: 4,
          slidesToScroll: 1,
          infinite: false,
          dots: false,
          centerMode: false,
          centerPadding: '40px',
          focusOnSelect: true,
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 600,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 480,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
      },
    }

    Drupal.behaviors.slickCarouselClients = {
      attach: function () {
        $('.slick-carousel-clients').not('.slick-initialized').slick({
          slidesToShow: 6,
          slidesToScroll: 1,
          infinite: true,
          dots: false,
          centerMode: false,
          centerPadding: '20px',
          focusOnSelect: true,
          autoplay: true,
          autoplaySpeed: 2000,
          arrows: false,
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 4
              }
            },
            {
              breakpoint: 600,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 480,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
      },
    }

    Drupal.behaviors.slickCarouselTestimonials = {
      attach: function () {
        $('.slick-carousel-testimonials').not('.slick-initialized').slick({
          prevArrow: '#prev',
          nextArrow: '#next',
          slidesToShow: 2,
          slidesToScroll: 1,
          infinite: true,
          dots: false,
          centerMode: false,
          centerPadding: '20px',
          focusOnSelect: true,
          autoplay: true,
          autoplaySpeed: 4000,
          arrows: true,
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 600,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        }).on('setPosition', function (event, slick) {
          setEqualHeight('.card-testimonial');
        });

        function setEqualHeight(selector) {
          var maxHeight = 0;
          $(selector).each(function () {
            var height = $(this).height();
            if (height > maxHeight) {
              maxHeight = height;
            }
          });
          $(selector).height(maxHeight);
        }
      },
    }

    Drupal.behaviors.slickCarouselBlog = {
      attach: function () {
        $('.slick-carousel-blog-row').not('.slick-initialized').slick({
          prevArrow: '#prev-home-carousel',
          nextArrow: '#next-home-carousel',
          slidesToShow: 4,
          slidesToScroll: 1,
          infinite: true,
          dots: false,
          centerMode: false,
          centerPadding: '20px',
          focusOnSelect: true,
          autoplay: true,
          autoplaySpeed: 4000,
          arrows: true,
          adaptiveHeight: true,
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 600,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        }).on('setPosition', function (event, slick) {
          setEqualHeight('.card-news');
        });

        function setEqualHeight(selector) {
          var maxHeight = 0;
          $(selector).each(function () {
            var height = $(this).height();
            if (height > maxHeight) {
              maxHeight = height;
            }
          });
          $(selector).height(maxHeight);
        }
      },
    }
  })(jQuery, Drupal);

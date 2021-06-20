import checkboxValidation from './helpers/checkboxValidation';
import registration from './helpers/registration';
import checkEmailUniqueness from './helpers/checkEmailUniqueness';
import './base';

$(function ($) {
  "use strict";

  $('.checkbox-text, .checkbox-analog').on('click', function (e) {
    const checkbox = $(this).siblings("input");

    if (checkbox.is(':checked')) {
      checkbox.prop('checked', true);
      checkbox.next().find('span').hide();
    } else {
      checkbox.prop('checked', false);
      checkbox.next().find('span').show();
    }
    console.log(checkbox.is(':checked'));
  })

  $('#landing-registration_email, #landing-registration_email_bottom').on('change', checkEmailUniqueness);

  $('.landing-registration').find('input').on('change', checkboxValidation)
  //формы регистрации и авторизации
  $('.landing-registration').on('submit', registration);

  $('.slider-ul').slick({
    infinite: true,
    speed: 300,
    slidesToShow: 5,
    slidesToScroll: 1,
    prevArrow: '<button class="slider-button-back"><img src="/bundles/images/dest/sliderarrow.svg" alt="previous slied"></button>',
    nextArrow: '<button class="slider-button-next"><img src="/bundles/images/dest/sliderarrow.svg" alt="next slide"></button>',
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 4,
          arrows: true
        }
      },
      {
        breakpoint: 970,
        settings: {
          slidesToShow: 2,
          arrows: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          fade: true,
          arrows: false
        }
      }
    ]
  });

});

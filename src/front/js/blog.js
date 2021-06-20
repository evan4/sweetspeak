import checkboxValidation from './helpers/checkboxValidation';
import registration from './helpers/registration';
import checkEmailUniqueness from './helpers/checkEmailUniqueness';

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

  $('.landing-registration').find('input').on('change', checkboxValidation)
  //формы регистрации и авторизации
  $('.landing-registration').on('submit', registration);

});

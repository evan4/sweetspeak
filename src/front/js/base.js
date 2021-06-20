import stickybits from './libs/stickybits.min';
import registration from './helpers/registration';
import checkEmailUniqueness from './helpers/checkEmailUniqueness';
import checkboxValidation from './helpers/checkboxValidation';

$(function ($) {
  "use strict";

  // sticky menu

  stickybits('.header', { useStickyClasses: true });

  $.ajaxSetup({
    dataType: "json",
  });

  //show pass
  $('.show-pass').on('click', function () {
    if ($(this).prev().attr('type') == 'password') {
      $(this).prev().attr('type', 'text');
    } else {
      $(this).prev().attr('type', 'password');
    }
  });

  $('.theme-change ').on('click', function (e) {
    e.preventDefault();

    const theme = $(this).data('theme');
    $.post( "/changeTheme", {theme})
    .done(function( data ) {
      location.reload();
    });

  })

  $('.subcategory').selectize({
    create: false
  });

  $('.burger').on('click', function (e) {
    $(this).toggleClass('burger_active');

    $('.header-menu-modile').slideToggle().find('li')
      .toggleClass('header-menu__item_mobile')
  })

  $(document).on('click', '.header-menu__item_mobile', function (e) {
    $('.header-menu-sub').slideUp();
    const current = $(this).find('ul');
    if (!current.is(':visible')) {
      current.slideDown();
    }

  })

  const btnProfile = $('.header__profile');
  
  if (btnProfile.attr('href') !== '/dashboard' ) {
    btnProfile.magnificPopup({
      type: 'inline',
      midClick: true,
      closeOnBgClick: false
    })
  }

    
  $('.guest-registry').on('click', function (e) {
    e.preventDefault();
    const link = $(this).attr('href');
    
    $('#registry-form').children('div').hide()
    .end().find(link).show()
    
    $.magnificPopup.open({
      items: {
        src:'#registry-form',
        type: 'inline',
      },
      midClick: true,
      closeOnBgClick: false
    })

  });
  
  $('#landing-registration_email').on('change', checkEmailUniqueness);

  // переключение форм в модальном окне
  $('.modal__toggle').on('click', function (e) {
    e.preventDefault();
    
    const link = $(this).attr('href');
    console.log(link)
    $('#registry-form').children('div').hide();
    $('#registry-form').find(link).show()

    $('.error').empty();
  })

  $('#form-registration').find('input').on('change', checkboxValidation)

  // проверка уникальности email
  $('#registration_email, #recovery_email').on('change', checkEmailUniqueness);

  //формы регистрации и авторизации
  $('#form-registration, #form-auth, #form-recovery').on('submit', registration);

  //форма Подписки на уведомления
  $('.footer-form').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: '/api/subscribe',
      method: 'POST',
      data: $(this).serializeArray(),
    })
      .done((res) => {
        if (res.success) {
          if (res.res !== 'email exists') {
            toastr.info('Вы подписаны на рассылку');
          } else {
            toastr.warning('Вы уже подписаны');
          }

          $('.footer-form')[0].reset();
        } else {
          toastr.error('Произошла ошибка. Попробуйте еще раз');
        }
      })
      .fail( (error) => {
        toastr.error('Произошла ошибка. Попробуйте еще раз');
      });
  })

});

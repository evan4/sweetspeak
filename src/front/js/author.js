$(function ($) {
  "use strict";

  $('#message-user').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: '/api/messages/create',
      data: $(this).serializeArray(),
      method: 'POST',
      dataType: "json",
      beforeSend:  () => {
        $(this).find("button[type=submit]")
          .addClass('button__loading')
          .prop('disabled', true)
      }
    })
      .done((res) => {
        if (res.success) {
          $.magnificPopup.close(); // Close popup that is currently opened (shorthand)
          toastr.info('Ваше сообщение отпралено');
          $(this)[0].reset();
        } else {
          toastr.error('Произошла ошибка. Попробуйте еще раз');
        }
        $(this).find("button[type=submit]")
          .removeClass('button__loading')
          .prop('disabled', false)
      })
      .fail( (error) => {
        $(this).find("button[type=submit]")
          .removeClass('button__loading')
          .prop('disabled', false)
        toastr.error('Произошла ошибка. Попробуйте еще раз');
      });
  })

  $('#complain-user').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: '/sendComplaint',
      data: $(this).serializeArray(),
      method: 'POST',
      beforeSend:  () => {
        $(this).find("button[type=submit]")
          .addClass('button__loading')
          .prop('disabled', true)
      }
    })
      .done((res) => {
        if (res.success) {
          $.magnificPopup.close(); // Close popup that is currently opened (shorthand)
          toastr.info('Ваше сообщение отпралено');
          $(this)[0].reset();
        } else {
          toastr.error('Произошла ошибка. Попробуйте еще раз');
        }
        $(this).find("button[type=submit]")
          .removeClass('button__loading')
          .prop('disabled', false)
      })
      .fail( (error) => {
        $(this).find("button[type=submit]")
          .removeClass('button__loading')
          .prop('disabled', false)
        toastr.error('Произошла ошибка. Попробуйте еще раз');
      });
  })

  $('#friend').on('click', function (e) {
    e.preventDefault();

    const data = {
      user_id : +$(this).data('id')
    }
    
    $.ajax({
      url: '/friend_request',
      data,
      method: 'POST',
      beforeSend:  () => {
        $(this)
          .addClass('button__loading')
          .prop('disabled', true)
      }
    })
      .done((res) => {
        if (res.res && res.res.success) {
          toastr.info('Ваш запрос в друзья отпралено');
        } else if(res.msg) {
          toastr.warning(res.msg);
        } else {
          toastr.error('Произошла ошибка. Попробуйте еще раз');
        }
        $(this)
          .removeClass('button__loading')
          .prop('disabled', false)
      })
      .fail( (error) => {
        $(this)
          .removeClass('button__loading')
          .prop('disabled', false)
        toastr.error('Произошла ошибка. Попробуйте еще раз');
      });

  })


  $('.aside-gallery').magnificPopup({
    delegate: 'a', // child items selector, by clicking on it popup will open
    gallery: {
      enabled: true
    },
    type: 'image',
  });

  $('.profile__button').magnificPopup({
    type: 'inline',
    midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
  });

});

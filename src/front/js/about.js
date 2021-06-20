$(function ($) {
  "use strict";

  $('#feedback').on('submit', function (e) {
    e.preventDefault();
    console.log($(this).serializeArray());

    $.ajax({
      url: '/proposal',
      method: "POST",
      data: $(this).serializeArray(),
      beforeSend:  () => {
        $(this).find("button[type=submit]")
        .addClass('button__loading')
        .prop('disabled', true)
      }
    })
      .done((res) => {
        console.log(res);
        if (res.success) {
          
          toastr.info('Ваше сообщение отправлено');

          $('.form')[0].reset();
        } else {
          toastr.error('Произошла ошибка. Попробуйте еще раз');
        }
        $(this).find("button[type=submit]")
        .removeClass('button__loading')
        .prop('disabled', false)
      })
      .fail( (error) => {
        toastr.error('Произошла ошибка. Попробуйте еще раз');
        $(this).find("button[type=submit]")
        .removeClass('button__loading')
        .prop('disabled', false)
      });

  })

});

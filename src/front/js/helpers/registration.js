export default function registration(e) {

  e.preventDefault();

  const error = $('.error');
  const url = $(this).attr('action');
  console.log(url);
  error.empty();

  let checkbox = true;

  if (url === '/singup' &&
    $(this).find('[type=checkbox]').is(':checked')) {
    checkbox = true;
  }

  if (checkbox) {
    $.ajax({
      url,
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
          if (url == '/auth') {
            window.location.href = '/';
          }else{
            toastr.info('Проверьте вашу почту!');
            $.magnificPopup.close();
          }
          // if (url === '/auth') {
          //   window.location.href = '/';
          // } else {
          //   toastr.info('Проверьте вашу почту!');
          //   $.magnificPopup.close();
          // }

          $(this)[0].reset();
        } else {
          for (const key in res) {
            if (res.hasOwnProperty(key)) {
              const element = res[key];
              error.append(`<li>${element}</li>`)
            }
          }
        }
        $(this).find("button[type=submit]")
        .removeClass('button__loading')
        .prop('disabled', false)
      })
      .fail((error) => {
        $(this).find("button[type=submit]")
        .removeClass('button__loading')
        .prop('disabled', false)
        $('.error').append(`<li>Произошла ошибка. Попробуйте еще раз</li>`)
      });
  }
}

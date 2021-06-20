import checkReg from './checkReg';
import errorFieldCheck from './errorFieldCheck';

export default function checkEmailUniqueness() {
  const emailReg = /^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/;
  const emailTooltip = "Введите корректный email";

  if ($(this).next('span').length > 0) {
    $(this).next('span').remove();
  }
  
  if ($(this).val() === '') {
    return;
  }

  let fValid = true;
  fValid = fValid && checkReg($(this), emailReg, emailTooltip);

  if (fValid) {
    $.ajax({
      url: '/checkEmailUniqueness',
      method: "POST",
      data: {
        email: $(this).val()
      },
      beforeSend: () => {
        $(this).prop('disabled', true);
      }
    })
      .done((res) => {
        
        if ($(this).attr('id') === 'recovery_email') {

          if(!res.email_unique){
            errorFieldCheck($(this), 'Этот email не зарегестрирован')
          }
          
        }else if(res.email_unique) {
          errorFieldCheck($(this), res.email_unique)
        }

        $(this).prop('disabled', false);
      })
      .fail((error) => {
        $(this).prop('disabled', false);
      });
  } else {
    errorFieldCheck($(this), 'Введите корректный email')
  }
}
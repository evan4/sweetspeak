import errorFieldCheck from './errorFieldCheck';

export default function checkboxValidation() {
  const checkbox = $(this).closest('form').find('input[type=checkbox]');
    
  if (!checkbox.is(':checked')) {
    errorFieldCheck(checkbox.closest('label'), 'Примите условия')
  } else {
    $(this).closest('label')
    .next('span').remove();
  }

}

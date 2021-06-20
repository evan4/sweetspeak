import errorFieldCheck from './errorFieldCheck';

export default function checkReg(o, regexp, tooltip) {
  if (!(regexp.test(o.val())) || o.val() === $(o).attr('placeholder')) {
    errorFieldCheck(o, tooltip);
  } else {
    $(o).removeClass("has-error").siblings("span").remove();
    return true;
  }
};

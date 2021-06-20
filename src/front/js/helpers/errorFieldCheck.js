export default function errorFieldCheck(o, tooltip) {
  if ($(o).next(".control-label").length === 0) {
    $("<span class='control-label'>" + tooltip + "</span>").insertAfter(o);
  }
  return false;
};
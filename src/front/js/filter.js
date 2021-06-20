$(function ($) {
  "use strict";
  // filter

  $('.form__input_sort').on('click', function(e){
    e.stopPropagation();
    const sort = (e.target.getAttribute('aria-sort') === 'ascending') ? 'descending' :'ascending';
    
    console.log($(this));
    $(this).attr('aria-sort', sort)
      .next().val(sort);
    
  });
});

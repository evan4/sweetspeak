$(function ($) {
  "use strict";

  const gallery = $('.aside-gallery-instagram');
  let template = '';

  const loading = `<div id="loading" style="height:250px; width: 100%; position: relative;">
    <svg  version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" style="top: 25%;left: 25%;"
    xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" 
    viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
      <path d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
        <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
      </path>
    </svg>
  </div>`;

  const array_of_subscribers = [
    'подписчик', 'подписчика', 'подписчиков',
  ];

  $.ajax({
    url: '/instagram',
    dataType: "json",
    method: "POST",
    beforeSend: () => {
      gallery.html(loading);
    }
  })
    .done((res) => {
      const { photos, total, url, followers } = res;
      $('#numInstagramFollowers').text(num_word(followers, array_of_subscribers))
      photos.forEach(item => {
        template += `<a href="${item.url}" rel="nofollow" target="_blank" class="aside-gallery__item"><img width="75px" heigth="75px" src="${item.image}" alt="${item.caption}""></a>`
      })

      if (total > 8) {
        template += `<a href="${url}" rel="nofollow" target="_blank" class="aside-gallery__item">${total}+</a>`
      }
      gallery.html(template)

    })
    .fail( (error) => {
      gallery.html('');
    });

  // склонкние слов
  function num_word(value, words, show = true) {
    let num = value % 100;

    if (num > 19) {
      num = num % 10;
    }

    let out = (show) ? value + ' ' : '';
    switch (num) {
      case 1:
        out += words[0];
        break;
      case 2:
      case 3:
      case 4:
        out += words[1];
        break;
      default:
        out += words[2]; 
        break;
    }

    return out;
  }

});

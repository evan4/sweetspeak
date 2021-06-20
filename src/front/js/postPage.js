import registration from './helpers/registration';
import checkEmailUniqueness from './helpers/checkEmailUniqueness';
import checkboxValidation from './helpers/checkboxValidation';

$(function ($) {
  "use strict";

  $.ajaxSetup({
    dataType: "json",
  });

  
  // голосование за статью

  $('.big-image-links__link_favour, .big-image-links__link_oppose').on('click', function(e) {

    let vote = 1;
    let id = +$(this).data('id');
    
    if (e.target.className.split(' ')[1] === 'big-image-links__link_oppose') {
      vote = -1;
    }

    $.ajax({
      url: `/changeVote?id=${id}&vote=${vote}`,
      cache: false
    })
      .done((res) => {

        if (res.success) {
          if (vote > 0) {
            $('.likes').text(res.likes);
          } else {
            $('.dislikes').text(res.dislikes);
          }

        }

      })
      .fail(function (error) {

      });
  });

  $('.big-image-links__link_share').on('click', (e) => {
    $('.share').toggle()
  })

  $('.share').on('click', 'span', (e) => {
    e.preventDefault();

    switch (e.target.className.split(' ')[1]) {
      case 'socials__link_telegram':
        Share.telegram()
        break;
      case 'socials__link_facebook':
        Share.facebook()
        break;
      case 'socials__link_twitter':
        Share.twitter()
        break;
      case 'socials__link_vk':
        Share.vkontakte()
      default:
        Share.instagram()
        break;
    }
  });

  const url_current = $(location).attr('href');
  const title = document.title;

  const Share = {
    vkontakte: function () {
      let url = 'http://vk.com/share.php?';
      url += 'url=' + encodeURIComponent(url_current);
      url += '&title=' + encodeURIComponent(title);
      url += '&noparse=true';
      Share.popup(url);
    },
    facebook: function () {
      let url = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url_current);
      Share.popup(url);
    },
    twitter: function () {
      let url = 'https://twitter.com/intent/tweet?';
      url += 'url=' + encodeURIComponent(url_current);
      url += '&text=' + encodeURIComponent(title);
      Share.popup(url);
    },
    instagram: function () {
      let url = 'https://www.instagram.com/?url=' + encodeURIComponent(url_current);
      Share.popup(url);
    },
    telegram: function () {
      let url = 'https://t.me/share/url?';
      url += 'url=' + encodeURIComponent(url_current);
      url += '&text=' + encodeURIComponent(title);
      Share.popup(url);
    },

    popup: function (url) {
      window.open(url, '', 'toolbar=0,status=0,width=626,height=436');
    }
  };

  const imageList = $('.form-comment-files');
  const photos = [];

  imageList.on('click', 'a', function (e) {
    e.preventDefault();
    const parent = $(this).closest('li');
    const index = parent.index()
    photos.splice(index, 1);
    imageList.find('li').eq(index).remove()
    console.log(photos);
  })

  $('#files').on('change', function (e) {
    const files = this.files;
    for (let index = 0; index < files.length; index++) {
      const element = files[index];
      const name = element.name;
      imageList.append(`
        <li class="form-comment-files__item">
          <a href="#" class="form-comment-files__link">${name}</a>
        </li>
      `)
      var fileReader = new FileReader();

      fileReader.onload = function (fileLoadedEvent) {
        var srcData = fileLoadedEvent.target.result; // <--- data: base64

        photos.push(srcData);
        console.log(photos);
      }
      fileReader.readAsDataURL(element);

      console.log(element);
    }

  })

  const forbittent = $('.form-comment-auth');
  $('.form-comment').on('submit', function (e) {
    e.preventDefault();
    let data = $(this).serializeArray();

    forbittent.hide();

    if (photos.length) {
      data.push({ name: "photos", value: photos });
    }

    $.ajax({
      url: '/api/comments/create',
      method: "POST",
      data,
      beforeSend:  () => {
        $(this).find("button[type=submit]")
          .addClass('button__loading')
          .prop('disabled', true)
      }
    })
      .done((res) => {
        console.log('res' + res);
        if (res.success) {
          toastr.info('Ваш комментарий сохранен и ожидает модерации');
          $(this)[0].reset();
        } else if (res.error === 'Доступ запрещен') {
          forbittent.show().find('.form-comment-auth__forbitten')
            .text('Комментарии могут оставлять только зарегестрированные пользователи')
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

  // проверка уникальности email
  $('#email-form').on('change', checkEmailUniqueness);

  $('#singup').find('input').on('change', checkboxValidation);

  //формы регистрации и авторизации
  $('#singup').on('submit', registration);

  // article's menu
  let index = 0;
  $('.article__text h2').each(function () {
    if(!/&nbsp;/.test($(this).text()) && $(this).text().trim() != ''){
      $(this).attr('id', `section${++index}`)
    }
    
  });

  $(".article-menu__wrap").on('click', function (e) {
    e.preventDefault();
    $('html,body').animate({ scrollTop: $(this.hash).offset().top - 50 }, 500);
  });

  $('.answer-comment').on('click', function () {
    $(this).next().toggle();
  })
});

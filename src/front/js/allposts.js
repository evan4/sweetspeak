$(function ($) {
  "use strict";

  $.ajaxSetup({
    dataType: "json",
  });

  // асинхронная загрузка статей
  const loading = `<div id="loading">
    <svg  version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" 
    viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
      <path d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
        <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
      </path>
    </svg>
  </div>`;

  $(".top-menu__link").on('click', function (e) {
    e.preventDefault();

    const currentTab = $(this);

    const tabUrl = currentTab.data("url");

    currentTab.closest('ul')
      .find('span').removeClass("top-menu__link_active");
    
    $(this).addClass("top-menu__link_active");

    // load tab content
    loadTabContent(tabUrl, currentTab);

  });

  
  function loadTabContent(tabUrl, currentTab) {
    
    const tab = currentTab.attr("id").split('.')[1].split('-');
    
    const tabId = tab[0];

    const parent = currentTab.closest('ul').next();
    const heightUl = parent.height()

    parent.html(loading);
    $(parent).css("minHeight", heightUl)
    let url = '/getPosts?'+tabUrl;
    
    if(tab.length === 1){
      url += '&parent=1';
    }

    switch (tabId) {
      case 'dating':
      case 'clothes':
      case 'interesting':
        url += '&limit=3';
        break;
      case 'relations':
      case 'education':
        url += '&limit=2';
        break;
      case 'parties':
      case 'fetish':
        url += '&limit=4';
        break;
      default:
        url += '&limit=4';
        break;
    }
    
    $.ajax({
      url,
      cache: false
    })
      .done((res) => {
        $("#preloader").hide();
        let template;

        switch (tabId) {
          case 'dating':
            template = datingTemplate(res);
            break;
          case 'relations':
            template = relationTemplate(res);
            break;
          case 'interesting':
            template = interestingTemplate(res);
            break;
          case 'fetish':
            template = fetishTemplate(res);
            break;
          case 'education':
            template = educationTemplate(res);
            break;
          case 'clothes':
            template = clothesTemplate(res);
            break;
          case 'parties':
            template = partiesTemplate(res);
            break; 
          default:
            template = datingTemplate(res);
            break;
        }

        parent
          .html(template);
      })
      .fail( (error) => {
        
      });
  }

  function checkArrayIsNotEmpty(items) {
    return  (typeof items !== 'undefined' && items.length > 0);
  }

  function datingTemplate(items) {
    let template = '';
    if(checkArrayIsNotEmpty(items)){
      template = items.map(item => `<li class="similars__item">
          <div class="similars-image" style="background-image: url(${item['photo']['medium']});">
            <div class="similars-image-top">
              <span class="similars-image-top__text">${item['category']}</span>
            </div>
            <ul class="similars-list">
              <li class="similars-list__item similars-list__item_visibility">${item['see']}<i></i></li>
              <li class="similars-list__item similars-list__item_heart">${item['likes']}<i></i></li>
              <li class="similars-list__item similars-list__item_comments">${item['comments_count']}<i></i></li>
            </ul>
          </div>
          <a href="${item['url']}" class="similars__caption">${item['title']}</a>
          <div class="similars__text">${item['snippet']}</div>
          <ul class="similars-details">
            <li class="similars-details__item">
              <a href="${item['author_url']}">${item['author']}</a>
            </li>
            <li class="similars-details__item similars-details__item_history"><i></i> ${item['date']}</li>
          </ul>
        </li>`);
    }

    return template;
  }

  function relationTemplate(items) {
    let template = '';
    if(checkArrayIsNotEmpty(items)){
      template = items.map(item => `<div class="similars-image" style="background-image: url(${item['photo']['large']});">
            <div class="similars-image-top similars-image-top_tiny">
              <span class="similars-image-top__text similars-image-top__text_blue">${item['category']}</span>
            </div>
            <a href="${item['url']}" class="big-image__caption big-image__caption_center">${item['title']}</a>
            <div class="big-image-bottom big-image-bottom_small big-image-bottom_center">
              <ul class="big-image-list big-image-list_center">
                <li class="big-image-list__item big-image-list__item_oval
               big-image-list__item_visibility">${item['see']}<i></i></li>
                <li class="big-image-list__item big-image-list__item_oval big-image-list__item_heart">${item['likes']}<i></i></li>
                <li class="big-image-list__item big-image-list__item_oval big-image-list__item_comments big-image-list__item_small">${item['comments_count']}<i></i>
                </li>
                <li>
                  <ul class="big-image-list big-image-list_sub">
                    <li class="big-image-list__item big-image-list__item_author">
                      <a href="${item['author_url']}">${item['author']}</a>
                    </li>
                    <li class="big-image-list__item big-image-list__item_date"> ${item['date']}</li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>`);
    }

    return template;
  }

  function interestingTemplate(items) {
    let template = '';
    
    if(checkArrayIsNotEmpty(items)){

      const first = items.shift();
      
      template = `<div class="similars-image" style="background-image:url(${first['photo']['large']})">
        <div class="similars-image-top similars-image-top_tiny">
          <span class="similars-image-top__text similars-image-top__text_pink">${first['category']}</span></div>
            <ul class="similars-list similars-list_big">
              <li class="similars-list__item similars-list__item_visibility">${first['see']}<i></i></li>
              <li class="similars-list__item similars-list__item_heart">${first['likes']}<i></i></li>
              <li class="similars-list__item similars-list__item_comments">${first['comments_count']}<i></i></li>
            </ul>
          <a href="${first['url']}" class="big-image__caption big-image__caption_center">${first['title']}</a>
          <div class="big-image-bottom big-image-bottom_small">
            <ul class="big-image-list big-image-list-left">
            <li class="big-image-list__item big-image-list__item_author">
              <a href="${first['author_url']}">${first['author']}</a>
            </li>
            <li class="big-image-list__item big-image-list__item_date">3 ${first['date']}</li>
          </ul>
        </div>
      </div>`;
      template += '<div class="similars-image similars-image_center">';

      template += items.map((item, i) => `<div class="similars-image-block ${i > 0 ?? 'similars-image-block_first'}">
        <div class="similars-image-header">
        <ul class="similars-list similars-list_center">
          <li class="similars-list__item similars-list__item_center
        similars-list__item_visibility">${item['see']}<i></i></li>
          <li class="similars-list__item similars-list__item_center similars-list__item_heart">${item['likes']}<i></i></li>
          <li class="similars-list__item  similars-list__item_center similars-list__item_comments">${item['comments_count']}<i></i></li>
        </ul>
        <div class="similars-image-top similars-image-top_tiny similars-image-top_black">
          <span class="similars-image-top__text similars-image-top__text_pink">${item['subcategory']}</span>
        </div>
      </div>
      <a href="${item['url']}" class="similars-image__caption">${item['title']}</a>
      <div class="similars-image__text">${item['snippet']}</div>
      <p class="similars-image__author">Автор: <a href="${item['author_url']}">${item['author']}</a></p>
    </div>`);
    template += '</div>';

    }

    return template;
  }

  function fetishTemplate(items) {
    let template = '';

    if(checkArrayIsNotEmpty(items)){
      return items.map(item => `<div class="similars-image" style="background-image:url(${item['photo']['large']})">
        <div class="similars-image-top similars-image-top_tiny">
          <span class="similars-image-top__text similars-image-top__text_blue">${item['category']}</span>
        </div>
        <a href="${item['url']}" class="big-image__caption big-image__caption_four">${item['title']}</a>
        <div class="big-image-bottom big-image-bottom_small big-image-bottom_center">
        <ul class="big-image-list big-image-list_center">
          <li class="big-image-list__item big-image-list__item_visibility">${item['see']}<i></i></li>
          <li class="big-image-list__item big-image-list__item_heart">${item['likes']}<i></i></li>
          <li class="big-image-list__item big-image-list__item_comments big-image-list__item_small">${item['comments_count']}<i></i></li>
          <li>
            <ul class="big-image-list big-image-list_sub big-image-list_once">
            <li class="big-image-list__item big-image-list__item_author">
              <a href="${item['author_url']}">${item['author']}</a>
            </li>
          </ul>
          </li>
        </ul>
      </div>
    </div>`);
    }

  return template;
  }

  function educationTemplate(items) {
    let template = '';

    if(checkArrayIsNotEmpty(items)){
      template = items.map((item) => {
          return `<li class="similars__item">
            <div class="similars-image" style="background-image:url(${item['photo']['medium']})">
              <ul class="similars-list similars-list_empty">
                <li class="similars-list__item similars-list__item_visibility">${item['see']}<i></i></li>
                <li class="similars-list__item similars-list__item_heart">${item['likes']}<i></i></li>
                <li class="similars-list__item similars-list__item_comments">${item['comments_count']}<i></i></li>
              </ul>
            </div>
          </li>
          <li class="similars__item">
            <div class="similars-image similars-image_zero">
              <div class="similars-image-top similars-image-top_black">
                <span class="similars-image-top__text similars-image-top__text_green">${item['category']}</span>
              </div>
            </div>
            <a href="${item['url']}" class="similars__caption similars__caption_bigger">${item['title']}</a>
            <div class="similars__text">${item['snippet']}</div>
            <ul class="similars-details">
              <li class="similars-details__item">
                <a href="${item['author_url']}">${item['author']}</a>
              </li>
            </ul>
          </li>`;
      });
    }
    
    return template;
  }
  
  function clothesTemplate(items) {
    let template = '';

    if(checkArrayIsNotEmpty(items)){
      template = items.map(item => `<div class="similars-image" 
        style="background-image:url(${item['photo']['medium']})">
        <div class="similars-image-top similars-image-top_average">
          <span class="similars-image-top__text similars-image-top__text_green">${item['category']}</span>
        </div>
        <a href="${item['url']}" class="big-image__caption big-image__caption_center">${item['title']}</a>
        <div class="big-image-bottom big-image-bottom_small big-image-bottom_center">
        <ul class="big-image-list big-image-list_accessories">
          <li class="big-image-list__item big-image-list__item_visibility">${item['see']}<i></i></li>
          <li class="big-image-list__item big-image-list__item_heart">${item['likes']}<i></i></li>
          <li class="big-image-list__item big-image-list__item_comments big-image-list__item_small">${item['comments_count']}<i></i></li>
          <li>
            <ul class="big-image-list big-image-list_sub big-image-list_fullwidth">
              <li class="big-image-list__item big-image-list__item_author">
                <a href="${item['author_url']}">${item['author']}</a>
              </li>
              <li class="big-image-list__item big-image-list__item_date">${item['date']}</li>
            </ul>
          </li>
        </ul>
      </div>
    </div>`);
    }

    return template;
  }

  function partiesTemplate(items) {
    
    let template = '';

    if(checkArrayIsNotEmpty(items)){
      template = items.map(item => `<li class="similars__item">
      <div class="similars-image" style="background-image:url(${item['photo']['large']})">
        <div class="similars-image-top">
          <span class="similars-image-top__text">${item['category']}</span>
        </div>
        <ul class="similars-list">
          <li class="similars-list__item similars-list__item_visibility">${item['see']}<i></i></li>
          <li class="similars-list__item similars-list__item_heart">${item['likes']}<i></i></li>
          <li class="similars-list__item similars-list__item_comments">${item['comments_count']}<i></i>
          </li>
        </ul>
      </div>
      <a href="${item['url']}" class="similars__caption">${item['title']}</a>
      <div class="similars__text">${item['snippet']}</div>
      <ul class="similars-details">
        <li class="similars-details__item">
          <a href="${item['author_url']}">${item['author']}</a>
        </li>
        <li class="similars-details__item similars-details__item_history"><i></i>${item['date']}</li>
      </ul>
      </li>`)
    }

    return template;
  }
  
});

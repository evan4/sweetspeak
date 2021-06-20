import './libs/ckeditor.js';
import checkReg from './helpers/checkReg';
import errorFieldCheck from './helpers/errorFieldCheck';

const $subcategories_article = $('#subcategories-article').selectize({
  hideSelected:true,
  persist:false,
  valueField: 'id',
  labelField: 'title',
  searchField: 'title',
  options: [],
  create: false,
  placeholder: 'Выберете подкатегорию',
});

const subcategories_article  = $subcategories_article[0].selectize;

const $categories_article = $('#categories-article').selectize({
    hideSelected:true,
    persist:false,
    valueField: 'id',
    labelField: 'title',
    searchField: 'title',
    options: convertDataForSelect(categories['categories']),
    create: false,
    placeholder: 'Выберете категорию',
    onInitialize: () => {
     
    },
    onChange: (value) => {
        if (!value.length) return;
        
        subcategories_article.clear()
        subcategories_article.clearOptions();
        const id = categories['all'][value-1];
        let subcategories = categories['subcategories'][id.slug];
        subcategories_article.load((callback) => {
          callback(convertDataForSelect(subcategories))
        });
      }
});
const categories_article  = $subcategories_article[0].selectize;

function convertDataForSelect(categories_obj) {
  let categories_array = [];
  for (const key in categories_obj) {
    if (Object.hasOwnProperty.call(categories_obj, key)) {
      const element = categories_obj[key];
      const id = categories['all'].findIndex(item => item.slug === element) + 1;
      categories_array.push({
        id,
        title: key,
      })
    }
  }
  return categories_array
}

function checkSlugUniqueness() {

  if ($(this).next('span').length > 0) {
    $(this).next('span').remove();
  }
  
  if ($(this).val() === '') {
    return;
  }

  const slugReg = /^[a-z0-9-]/;
  const slugooltip = "Допустимы латинские символы, цифры, знаки -.-";
  
  const len = $(this).val().length;

  if(len > 19){
      let fValid = true;
      fValid = fValid && checkReg($(this), slugReg, slugooltip);
      
    if(fValid){
      $.ajax({
          url: '/api/checkSlugUniqueness',
          data: {
            slug: $(this).val()
          },
          method: 'POST',
          beforeSend:  () => {
            $(this).prop('disabled', true);
          }
        })
          .done((res) => {
            if (res.error) {
              errorFieldCheck($(this), res.error)
            } 

            $(this).prop('disabled', false);
          })
          .fail( (error) => {
            $(this).prop('disabled', false);
            toastr.error('Произошла ошибка. Попробуйте еще раз');
          });
      }

    }else{
      errorFieldCheck(slug, 'Адрес должен быть не менее 20 символов')
    }
}

$(function ($) {
    "use strict";
    ClassicEditor
			.create( document.querySelector( '#editor' ), {
        toolbar: {
					items: [
						'heading',
						'|',
						'bold',
						'italic',
						'underline',
						'horizontalLine',
						'link',
						'bulletedList',
						'numberedList',
						'|',
						'indent',
						'outdent',
						'alignment',
						'|',
						'imageUpload',
						'blockQuote',
						'insertTable',
						'mediaEmbed',
						'undo',
						'redo'
					]
				},
        language: 'ru',
        mediaEmbed: {
          'previewsInData' : true
        },
				table: {
					contentToolbar: [
						'tableColumn',
						'tableRow',
						'mergeTableCells'
					]
				},
				
			} )
			.then( editor => {
				window.editor = editor;
			} )
			.catch( error => {
				console.error( 'Oops, something went wrong!' );
				console.error( error );
			} );

    $('#photo').on('change', function (e) {
      const files = this.files;
      for (let index = 0; index < files.length; index++) {
        const element = files[index];
        const name = element.name;
        $('.article-flie-name').text(name)
        var fileReader = new FileReader();
  
        fileReader.onload = function (fileLoadedEvent) {
          var srcData = fileLoadedEvent.target.result; // <--- data: base64
  
        }
        fileReader.readAsDataURL(element);
  
      }
  
    })

    $('#article-slug').on('change', checkSlugUniqueness);

    //форма добавить статьи
    $('#article-create').on('submit', function (e) {
        e.preventDefault();

        const title = $('#article-title').val();
        const slug = $('#article-slug').val();
        const snippet = $('#article-snippet').val();
        const category = $('#subcategories-article').val();
        const editorData = editor.getData();
        const photo = $('#photo').prop('files')[0];
        
        let formData = new FormData();
        formData.append("title", title);
        formData.append("slug", slug);
        formData.append("categories_id", category);
        formData.append("snippet", snippet);
        formData.append("content", editorData);
        formData.append("photo", photo);
        
        $.ajax({
          url: '/api/articles/create',
          data: formData,
          processData: false,
          contentType: false,
          method: 'POST',
          beforeSend:  () => {
            $(this).find("button[type=submit]")
              .addClass('button__loading')
              .prop('disabled', true)
          }
        })
          .done((res) => {
            if (res.success) {
              toastr.info('Ваше сообщение отпралено');
              $(this)[0].reset();
              categories_article.clearOptions()
              subcategories_article.clear()
              subcategories_article.clearOptions();
              $('.article-flie-name').text('');
              editor.setData('');
            }else if(res.error){
              toastr.error(res.error);
            } else {
              toastr.error('Произошла ошибка. Попробуйте еще раз');
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
});

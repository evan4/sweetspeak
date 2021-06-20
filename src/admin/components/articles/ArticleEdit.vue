<template>
  <div>
    <h1>Редактирование статьи</h1>
    <div v-if="!loading">
      <p v-if="comments_count > 0">
        {{comments_count}}.
        <a
          :href="`${formData.subcategory}&id=${formData.id}#comments-block`"
          target="_blank"
        >Читать комментарии к статье</a>
      </p>

      <b-form @submit.prevent="onSubmit">
        <b-form-group>
          <b-img :src="photo" fluid :alt="formData.title" v-if="photo"></b-img>
        </b-form-group>
        <b-form-group>
          <b-form-file v-model="formData.photo" placeholder="Выберете файл"></b-form-file>
        </b-form-group>
        <b-form-group id="input-title" label="Заголовок" label-for="title">
          <b-form-input
            id="title"
            v-model="formData.title"
            minlength="5"
            maxlength="255"
            class="form__input"
            required
            placeholder="Введите заголовок"
          ></b-form-input>
        </b-form-group>

        <b-form-group id="input-slug" label="Адрес" label-for="slug">
          <small>Это поле генерируется автоматически, если вы его не заполнили</small>     
          <b-form-input
            id="slug"
            v-model="slug"
            class="form__input"
            maxlength="255"  autocomplete="off"
            placeholder="Введите адрес"
          ></b-form-input>
          <span class="text-danger" v-show="slug">{{tipSlugUniqueness}}</span>
        </b-form-group>

        <b-form-group id="input-snippet" label="Анонс статьи" label-for="snippet">
          <b-form-input
            id="snippet"
            v-model="formData.snippet"
            class="form__input"
            required
            placeholder="Введите анонс статьи"
          ></b-form-input>
        </b-form-group>

        <b-form-group id="input-content" label="Текст" label-for="content">
          <small>Для формирования меню статьи, необходимо выделить нужные аункты как заголовок 2</small>
          <ckeditor :editor="editor" v-model="formData.content" :config="editorConfig" required></ckeditor>
        </b-form-group>

        <b-row>
          <b-col md="6">
            <b-form-group id="input-category" label="Категория" label-for="category">
              <v-select :options="categories" class="form__select"
                  label="text"
                  :reduce="categories => categories.value"
                  @input="changeCategory"
                  required
                  v-model="formData.category"></v-select>
            </b-form-group>
          </b-col>
          <b-col md="6">
            <b-form-group id="input-subcategory" label="Подкатегория" label-for="subcategory">
              <v-select :options="subcategories" class="form__select"
                  label="text"
                  :reduce="subcategories => subcategories.value"
                  required
                  v-model="formData.subcategory"></v-select>
            </b-form-group>
          </b-col>
        </b-row>
         <b-form-group id="input-titleseo" label="titleseo" label-for="titleseo">
          <b-form-input
            id="titleseo"
            v-model="formData.titleseo"
            class="form__input"
            minlength="5"
            maxlength="255"
            placeholder="Введите title"
          ></b-form-input>
          <small class="form-text text-muted"
              >генерируется автоматически, если поле не заполнено.</small>
        </b-form-group>
        <b-form-group id="input-description" label="description" label-for="description">
          <b-form-textarea
              id="input-description"
              class="form__textarea"
              v-model="formData.description"
          ></b-form-textarea>
          <small class="form-text text-muted"
            >генерируется автоматически, если поле не заполнено.</small>
        </b-form-group>
        <b-button
          type="submit"
          :disabled="submit"
          class="button button_red form-comment__button button_submit"
        >
          <b-spinner small type="grow" v-show="submit"></b-spinner>
          <span>Сохранить</span>
        </b-button>
      </b-form>
    </div>
  </div>
</template> 
<script>
import Vue from "vue";
import { mapGetters } from 'vuex';

import CKEditor from '@ckeditor/ckeditor5-vue2';
Vue.use( CKEditor );
import { num_word } from "../../helper";

export default {
  data() {
    return {
      editor: ClassicEditor,
      editorConfig: {
         toolbar: {
					items: [
						'heading',
						'|',
						'bold',
						'italic',
						'link',
						'bulletedList',
						'numberedList',
						'|',
						'indent',
						'outdent',
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
      },
      errors: false,
      slug: '',
      tipSlugUniqueness: '',
      formData: null,
      allCategories: [],
      categories: [],
      subcategories: [],
      subs: [],
      comments_count: 0,
      role: "user",
      submit: false,
      photo: null,
      loading: true
    };
  },
  components: {
     ckeditor: CKEditor.component
  },
  created() {
    this.role = Vue.prototype.$role;
    
     Vue.http
      .get('articles/getArticle', {params: {id: this.currentArticleId}} )
      .then(response => {
        return response.json();
      })
      .then(
        response => {
          const {
            id,
            title,
            slug,
            titleseo,
            description,
            categories_id,
            snippet,
            content,
            photo,
            moderated,
            comments_count,
            author_id
          } = response;
          
          this.slug = slug;
          this.photo = `/bundles/articles/300_${photo}`;
          this.comments_count = num_word(comments_count, [
            "комментарий",
            "комментария",
            "комментариев"
          ]);

          this.formData = {
            id,
            title,
            slug,
            titleseo,
            description,
            category: categories_id,
            subcategory: 0,
            snippet,
            content,
            photoOld: photo,
            photo: null,
            author_id
          };

          Vue.http
            .post("categories")
            .then(response => {
              return response.json();
            })
            .then(
              response => {
                const { all, categories, subcategories } = response;
                this.subs = subcategories;
                this.allCategories = all;
                let parents = [];
                const category_info = all.find(item => +item.id == +this.formData.category)
                
                const parentInfo = all.find(item => +item.id == +category_info.parent)
                
                for (const key in categories) {
                  if (categories.hasOwnProperty(key)) {
                    parents.push({ value: categories[key], text: key });
                    
                    if (parentInfo.name === key) {
                      this.formData.category = categories[key];

                      let subs = [];
                      const sub = subcategories[categories[key]];

                      for (const key in sub) {
                        if (sub.hasOwnProperty(key)) {
                          
                          if (category_info.name === key) {
                            this.formData.subcategory = sub[key];
                          }
                          subs.push({ value: sub[key], text: key });
                        }
                      }

                      this.subcategories = subs;
                    }
                  }
                }
                this.categories = parents;
                this.loading = false;
              },
              response => {}
            );

          
        },
        response => {}
      );

  },
  computed: {
    ...mapGetters( 'articles', [ 'currentArticleId' ] ),
  },
  methods: {
    changeCategory(value) {
      const sub = this.subs[value];
      this.formData.subcategory = sub[Object.keys(sub)[0]];

      this.subcategories = [];
      for (var prop in sub) {
        if (sub.hasOwnProperty(prop)) {
          this.subcategories.push({
            value: sub[prop],
            text: prop
          });
        }
      }
    },
    onSubmit() {
      const {
        id,
        title,
        titleseo,
        description,
        subcategory,
        snippet,
        content,
        photo,
        photoOld,
        moderated,
        author_id
      } = this.formData;

      let category = this.allCategories.find(item => item.slug === subcategory);
      this.submit = true;
      let formData = new FormData();
      formData.append("id", id);
      formData.append("title", title);
      formData.append("slug", this.slug);
      formData.append("titleseo", titleseo);
      formData.append("description", description);
      formData.append("categories_id", category["id"]);
      formData.append("snippet", snippet);
      formData.append("content", content);
      formData.append("moderated", moderated);
      formData.append("author_id", author_id);
      if (photo) {
        formData.append("photo", photo);
        formData.append("photoOld", photoOld);
      }

      this.$store.dispatch("articles/updateArticle", formData).then(
        result => {
          this.submit = false;
        },
        error => {
          this.submit = false;
        }
      );
    }
  },
   watch: {
    slug: function(newSlug, oldSlug) {
      if(newSlug === this.formData.slug){
        return;
      }
      console.log(newSlug)
      if(newSlug.length > 19){
        let formData = new FormData();
        formData.append("slug", this.slug);

         Vue.http
          .post("checkSlugUniqueness", formData)
          .then(response => {
            return response.json();
          })
          .then(
            response => {
             if (response.error) {
               this.tipSlugUniqueness = response.error
             }else{
               this.tipSlugUniqueness = '';
             }
            },
            response => {}
          );

      }else{
        this.tipSlugUniqueness = 'Адрес должен быть не менее 20 символов'
      }
    }
  },
  destroyed() {
    this.errors = [];
    this.formData = {
      id: null,
      title: "",
      content: ""
    };
  }
};
</script>

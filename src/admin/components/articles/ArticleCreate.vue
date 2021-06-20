<template>
  <div>
    <h1>Создание статьи</h1>

    <b-form @submit.prevent="onSubmit">
      <b-form-group>
        <b-form-file
          v-model="formData.photo"
          :state="Boolean(formData.photo)"
          name="photo"
          placeholder="Выберете файл"
          required
        ></b-form-file>
      </b-form-group>

      <b-form-group id="input-title" label="Заголовок" label-for="title">
        <b-form-input
          id="title"
          v-model="formData.title"
          class="form__input"
          minlength="5"
          maxlength="255"
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
            <b-form-select
              id="category"
              v-model="formData.category"
              :options="categories"
              class="form__input"
              @change="changeCategory"
              required
            ></b-form-select>
          </b-form-group>
        </b-col>
        <b-col md="6">
          <b-form-group id="input-subcategory" label="Подкатегория" label-for="subcategory">
            <b-form-select
              id="subcategory"
              v-model="formData.subcategory"
              :options="subcategories"
              class="form__input"
              required
            ></b-form-select>
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
        <span>Создать</span>
      </b-button>
    </b-form>
  </div>
</template> 
<script>
import Vue from "vue";
import CKEditor from '@ckeditor/ckeditor5-vue2';

Vue.use( CKEditor );

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
      },
      errors: false,
      formData: {
        title: "",
        titleseo: "",
        description: "",
        category: [],
        subcategory: [],
        snippet: "",
        content: "",
        photo: null
      },
      slug: '',
      submit: false,
      tipSlugUniqueness: '',
      allCategories: [],
      categories: [],
      subcategories: [],
      subs: []
    };
  },
  components: {
    ckeditor: CKEditor.component
  },
  created() {
    Vue.http
      .post("categories", {})
      .then(response => {
        return response.json();
      })
      .then(
        response => {
          
          const { all, categories, subcategories } = response;
          this.subs = subcategories;
          this.allCategories = all;
          this.formData.category = categories[Object.keys(categories)[0]];

          let parents = [];
          for (const key in categories) {
            if (categories.hasOwnProperty(key)) {
              parents.push({ value: categories[key], text: key });
            }
          }
          this.categories = parents;
          let subs = [];
          const sub = subcategories;

          this.changeCategory(this.formData.category);
        },
        response => {}
      );
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
      const { id, title, titleseo, description, subcategory, snippet, content, photo } = this.formData;

      let category = this.allCategories.find(item => item.slug === subcategory);

      let formData = new FormData();
      formData.append("title", title);
      formData.append("slug", this.slug);
      formData.append("titleseo", titleseo);
      formData.append("description", description);
      formData.append("categories_id", category["id"]);
      formData.append("snippet", snippet);
      formData.append("content", content);

      formData.append("photo", photo);

      this.$store.dispatch("articles/createArticle", formData);
    }
  },
  watch: {
    slug: function(newSlug, oldSlug) {
      
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
      title: "",
      content: ""
    };
  }
};
</script>
<template>
  <div>
    <h1>Создание шаблона</h1>

    <b-form @submit.prevent="onSubmit">

      <b-form-group id="input-title" label="Заголовок" label-for="title">
        <b-form-input
          id="title"
          v-model="formData.title"
          class="form__input"
          required
          placeholder="Введите заголовок"
        ></b-form-input>
      </b-form-group>

      <b-form-group id="input-content" label="Текст" label-for="content">
        <ckeditor :editor="editor" v-model="formData.body" :config="editorConfig" required></ckeditor>
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
        body: "",
      },
      submit: false,
    };
  },
  components: {
    ckeditor: CKEditor.component
  },
  created() {

  },
  methods: {
    onSubmit() {
      const { title, body } = this.formData;

      let formData = new FormData();
      formData.append("title", title);
      formData.append("body", body);

      this.$store.dispatch("mailings/createTemplate", formData);
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
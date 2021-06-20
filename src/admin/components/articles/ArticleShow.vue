<template>
  <div>
      <div class="top-main">
      <h1 class="h1">{{formData.title}}</h1>
      <router-link class="flex-center btn button button_red form-comment__button button_submit btn-secondary" 
      v-if="role === 'admin' || role === 'superadmin' "
      title="Редактировать статью"
      to="/articles/edit">Редактировать</router-link>
    </div>

    <p v-if="comments_count > 0">
      {{comments_count}}.
      <a
        :href="`${formData.subcategory}&id=${formData.id}#comments-block`"
        target="_blank"
      >Читать комментарии к статье</a>
    </p>
    
    <b-img :src="photo" fluid :alt="formData.title" v-if="photo"></b-img>
    
    <div v-html="formData.content" v-if="formData.content"></div>
    <p>Категория {{formData.category}}</p>
    <p>Подкатегория {{formData.subcategory}}</p>
    <p>Статус статьи</p>
    <v-select :options="statuses" class="form__select"
            label="text"
            @input="changeStatus"
            :reduce="statuses => statuses.value"
            v-model="formData.moderated"></v-select>

  </div>
</template> 
<script>
import Vue from "vue";

import { num_word } from "../../helper";

export default {
  data() {
    return {
      formData: null,
      allCategories: [],
      categories: [],
      subcategories: [],
      subs: [],
      comments_count: 0,
      role: "user",
      statuses: [
        {
          value: "0",
          text: "На модерации"
        },
        {
          value: "1",
          text: "Активно"
        },
        {
          value: "2",
          text: "Скрыто"
        },
      ],
      photo: null
    };
  },
  created() {
    this.role = Vue.prototype.$role;
    this.$store.dispatch( 'articles/currentArticleId', +this.$route.params.id)
    const {
      id,
      title,
      category,
      subcategory,
      photo,
      moderated,
      comments_count,
      author_id
    } = this.$store.getters["articles/currentArticle"](+this.$route.params.id);
    
    this.photo = `/bundles/articles/300_${photo}`;
    this.comments_count = num_word(comments_count, [
      "комментарий",
      "комментария",
      "комментариев"
    ]);
     this.formData = {
        id,
        title,
        category,
        subcategory,
        content: '',
        photoOld: photo,
        photo: null,
        author_id,
        moderated
      };

     Vue.http
      .get('articles/getArticle', {params: {id: +this.$route.params.id}} )
      .then(response => {
        return response.json();
      })
      .then(
        response => {
          this.formData.content = response.content
        },
        response => {}
      )
  },
  methods: {

      changeStatus(){
          console.log(this.formData)
        this.$store.dispatch("articles/updateStatus", this.formData);
      }
  },
};
</script>

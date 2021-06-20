<template>
  <div>
    <div class="header-top">
      <h1>Комментарий</h1>
    </div>
    <div class="mb-2">{{comment.body}}</div>
    <p>Автор {{comment.author}}</p>
    <p>Создан - {{dateFormat(comment.created_at)}}</p>
    <p>Статус {{comment.status == '1' ? 'Одобрен' : 'Ожидает модерации'}}</p>
    <div v-if="comment.photos.length">
      <p>Вложения к комментарию</p>
      <b-img-lazy
        v-bind="mainProps"
        v-for="(item,index) in comment.photos"
        :key="item.id"
        :src="getImageUrl(comment, index)"
        alt="Image 1"
      ></b-img-lazy>
    </div>

    <b-button
      type="button"
      :disabled="submit"
      class="button button_red form-comment__button button_submit"
      @click="approve(comment.id)"
      v-if="comment.status == '0'"
    >
      <b-spinner small type="grow" v-show="submit"></b-spinner>
      <span>Одобрить</span>
    </b-button>
    <div v-if="answers.length">
      <h3>{{answers_count}}</h3>
       <ul v-for="answer in answers" :key="answer.id" class="comments comments_sub">
          <li class="comments__item">
            <div class="comments-text">
              <p class="comments__author">
                <a target="_blabk" :href="`/Author/index&id=${answer.author_id}`">{{answer.author}}</a> <span>ответил:</span></p>
              <p class="article__text">{{answer.body}}</p>
              <p class="comments__date">
                <time itemprop="datePublished">{{dateFormat(answer.created_at)}}</time>  
              </p>
              <p>Статус {{answer.status == '1' ? 'Одобрен' : 'Ожидает модерации'}}</p>
               <b-button
                type="button"
                :disabled="submit"
                class="button button_red form-comment__button button_submit"
                @click="approve(answer.id)"
                v-if="answer.status == '0'">
                <b-spinner small type="grow" v-show="submit"></b-spinner>
                <span>Одобрить</span>
              </b-button>
              <div v-if="answer.photos.length">
                <p>Вложения к комментарию</p>
                <b-img-lazy
                  v-bind="mainProps"
                  v-for="(item,index) in answer.photos"
                  :key="item.id"
                  :src="getImageUrl(answer, index)"
                  alt="Image 1"
                ></b-img-lazy>
              </div>
            </div>
          </li>
       </ul>
    </div>
  </div>
</template>
<script>
import Vue from "vue";
import { num_word } from "../../helper";

export default {
  data() {
    return {
      role: "user",
      comment: [],
      answers: [],
      answers_count: 0,
      mainProps: {
        center: true,
        fluidGrow: true,
        blank: true,
        blankColor: "#bbb",
        width: 600,
        class: "my-5"
      },
      submit: false
    };
  },
  created() {
    this.role = Vue.prototype.$role;
    this.comment = this.$store.getters["comments/currentComment"](
      +this.$route.params.id
    );
  
    if(+this.comment.parent === 0){
      
      let formData = new FormData();
      formData.append("id", this.comment.id);
      Vue.http
        .post("comments/getAnswers", formData)
        .then(response => {
          return response.json();
        })
        .then(
          response => {
            this.answers = response.data;
             this.answers_count = num_word(this.answers.length, [
              "ответ",
              "ответа",
              "ответов"
            ]);
          },
          response => {
            console.log(response);
          }
        );
    }
  },
  methods: {
    dateFormat(date) {
      return new Date(date).toLocaleDateString();
    },
    getImageUrl(comment, imageId) {
      const { width } = this.mainProps;
      const image = comment.photos.find(
        (item, index) => index === imageId
      );
      return "/bundles/comments/" + image.photo;
    },
    approve(id) {
      let formData = new FormData();
      formData.append("id", id);
      this.submit = true;

       this.$store
        .dispatch("comments/approveComment", formData)
        .then(
          result => {
            this.loading = false;
          },
          error => {
            console.log(error);
            this.loading = false;
          }
        );

      Vue.http
        .post("comments/approve", formData)
        .then(response => {
          return response.json();
        })
        .then(
          response => {
            this.submit = false;
          },
          response => {
            console.log(response);
            this.submit = false;
          }
        );
    }
  },
};
</script>
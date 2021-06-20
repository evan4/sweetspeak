<template>
  <div>
    <div class="header-top">
      <h1>Переписка с {{message.author}}</h1>
    </div>
    <div class="messages">
      <ul class="list-group">
         <li :class="colorMessage(message)"
         v-for="message in messages" :key="message.id">
         <small>{{ messageOwner(message) }}</small>
         <p style="margin: 0 0 2px 0;">{{message.body}}</p>
         <small>{{ dateFormat(message.created_at) }}</small>
         </li>
      </ul>
      <small></small>
    </div>
    
     <b-button
        @click="form = !form"
        class="button button_red form-comment__button button_submit" >
        <span>Ответить</span>
      </b-button>
    <b-form @submit.prevent="onSubmit" v-if="form">

      <b-form-group id="input-body" label="Текст" label-for="input-body">
        <textarea name="body" id="input-body" class="form__textarea"
            v-model="formData.body"></textarea>
      </b-form-group>

      <b-button
        type="submit"
        :disabled="submit"
        class="button button_red form-comment__button button_submit"
      >
        <b-spinner small type="grow" v-show="submit"></b-spinner>
        <span>Отправить</span>
      </b-button>
    </b-form>
   
  </div>
</template>
<script>
import Vue from "vue";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
        message: [],
        messages: [],
        errors: false,
        form: false,
        formData: {
            body: "",
        },
        submit: false
    };
  },
  created() {
    const id =  +this.$route.params.id;
    this.message = this.$store.getters["messages/currentMessage"](
      +this.$route.params.id
    );

    let formData = new FormData();
    formData.append("id", id);
    Vue.http.post("messages/readMessage", formData)
    
    let formDataUser = new FormData();
      formDataUser.append("author_id", +this.message.author_id);
      formDataUser.append("user_id", +this.message.user_id);
      Vue.http
        .post("messages/authorMessages", formDataUser)
        .then(response => {
          return response.json();
        })
        .then(
          response => {
            console.log(response)
            this.messages = response;
          },
          response => {
            console.log(response);
          }
        );

  },
  computed: {
    ...mapGetters("users", ["userAuthorizedInfo"]),
  },
  methods: {
    colorMessage(message){
      if(this.userAuthorizedInfo.id === message.author_id){
        return 'list-group-item list-group-item-primary mb-2';
      }
      return 'list-group-item list-group-item-secondary mb-2';
    },
    messageOwner(message){
      if(this.userAuthorizedInfo.id === message.author_id){
        return 'Вы';
      }
      return message.author
    },
    dateFormat(date) {
      return new Date(date).toLocaleDateString();
    },
    onSubmit() {
      const { body } = this.formData;

      let formData = new FormData();
      formData.append("body", body);
      formData.append("user_id", this.message.author_id);

      this.$store.dispatch("messages/createMessage", formData);
    }
  },
};
</script>

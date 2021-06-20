<template>
  <div>
    <div class="top-main">
        <h1>Просмотр шаблона</h1>
        <router-link class="fa-lg flex-center" title="Редактировать профиль"
        to="/mailing/edit">
            <font-awesome-icon icon="edit" class="text-prinary" />
        </router-link>
    </div>
    <h2>{{formData.title}}</h2>
    <div v-html="formData.body"></div>
    <b-button
        type="submit"
        :disabled="sending"
        @click="getSubscribersTotal"
        class="button button_red form-comment__button button_submit"
      >
        <b-spinner small type="grow" v-show="sending"></b-spinner>
        <span>Разослать</span>
      </b-button>
    <div v-if="total">
        <p>Прогресс рассылки:</p>
        <b-progress :max="total" height="2rem" animated striped>
          <b-progress-bar :value="offset">
            <span><strong>{{ offset }} / {{ total }}</strong></span>
          </b-progress-bar>
        </b-progress>
    </div>
  </div>
</template> 
<script>
import Vue from "vue";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
        value: 0,
        total: 0,
        offset: 0,
        formData: {
          id: 0,
          title: "",
          body: "",
        },
        sending: false,
    };
  },
  created() {
    this.$store.dispatch( 'mailings/getCurrentTemplate', +this.$route.params.id);
    let { id, title, body } = this.$store.getters[
        "mailings/currentTemplate"
        ](+this.$route.params.id);
    this.formData = {
      id,
      title,
      body
    };
  },
  computed: {
    ...mapGetters( [ 'alertType', 'alertMsg', 'showAlert' ] ),
  },
  methods: {
    getSubscribersTotal(){
      this.sending = true;
       Vue.http
            .post("mailings/subscribers_total")
            .then(response => {
                return response.json();
            })
            .then(
                response => {
                  console.log(response)
                  const { total } = response;
                  if(total){
                    this.total = total;
                    this.sendEmails();
                  }else{
                    this.sending = false;
                    this.$store.dispatch('openAlert',{
                        alertType : 'danger',
                        alertMsg : 'Произошла ошибка. Попробуйте еще раз'
                      }, { root: true })
                  }
                },
                response => {
                   this.sending = false;
                   this.$store.dispatch('openAlert',{
                      alertType : 'danger',
                      alertMsg : 'Произошла ошибка. Попробуйте еще раз'
                    }, { root: true })
                }
            );
    },
    sendEmails() {
        
        let formData = new FormData();
        formData.append("id", this.formData.id);
        formData.append("offset", this.offset);
        Vue.http
            .post("mailings/sendEmail", formData)
            .then(response => {
                return response.json();
            })
            .then(
                response => {
                  const {success, email } = response;
                  console.log(email)
                 
                  if(success){

                    this.offset +=1;
                    
                  }else{
                    this.$store.dispatch('openAlert',{
                      alertType : 'danger',
                      alertMsg : 'Произошла ошибка. Попробуйте еще раз'
                    }, { root: true })
                  }
                },
                response => {
                   this.sending = false;
                   this.$store.dispatch('openAlert',{
                      alertType : 'danger',
                      alertMsg : 'Произошла ошибка. Попробуйте еще раз'
                    }, { root: true })
                }
            );

    },
    closeAlert(){
      this.$store.dispatch( 'closeAlert')
    }
  },
   watch:{
      offset: function(newPage, oldPage) {
        if(this.offset < this.total ){
          this.sendEmails();
        }else{
          this.sending = false;
          this.total = 0;
          this.value = 0;
          this.$store.dispatch('openAlert',{
            alertType : 'success',
            alertMsg : 'Рассылка новости завершена'
          }, { root: true })
        }
      }
    }
};
</script>
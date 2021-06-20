<template>
  <div>
  <b-tabs content-class="mt-3">
    <b-tab title="Основные данные" active>
      <div class="row" v-if="userAuthorizedInfo">
        <table class="table table-borderless table-striped">
          <tbody>
            <tr>
              <td>Имя</td>
              <td>{{userAuthorizedInfo.name}}</td>
            </tr>
            <tr>
              <td>Ник</td>
              <td>{{userAuthorizedInfo.nickname}}</td>
            </tr>
            <tr>
              <td>Email</td>
              <td>{{userAuthorizedInfo.email}}</td>
            </tr>
            <tr>
              <td>Роль</td>
              <td>{{userAuthorizedInfo.status}}</td>
            </tr>
            <tr>
              <td>Баланс</td>
              <td>{{userAuthorizedInfo.balance}}</td>
            </tr>
            <tr>
              <td>author_points</td>
              <td>{{userAuthorizedInfo.author_points}}</td>
            </tr>
            <tr>
              <td>Создан</td>
              <td>{{createdAt}}</td>
            </tr>
            <tr>
              <td>Статус</td>
              <td>{{userAuthorizedInfo.userstatus}}</td>
            </tr>
            <tr>
              <td>Девиз</td>
              <td>{{userAuthorizedInfo.motto}}</td>
            </tr>
            <tr>
              <td>Ищу</td>
              <td>{{target}}</td>
            </tr>
            <tr>
              <td>Обо мне</td>
              <td>{{userAuthorizedInfo.bio}}</td>
            </tr>
            <tr>
              <td>Пол</td>
              <td>{{gender}}</td>
            </tr>
            <tr>
              <td>Возраст</td>
              <td>{{userAuthorizedInfo.age}}</td>
            </tr>
            <tr>
              <td>Город</td>
              <td>{{userAuthorizedInfo.city}}</td>
            </tr>
            <tr>
              <td>instagram</td>
              <td>{{userAuthorizedInfo.instagram}}</td>
            </tr>
            <tr>
              <td>vk</td>
              <td>{{userAuthorizedInfo.vk}}</td>
            </tr>
            <tr>
              <td>twitter</td>
              <td>{{userAuthorizedInfo.twitter}}</td>
            </tr>
            <tr>
              <td>facebook</td>
              <td>{{userAuthorizedInfo.facebook}}</td>
            </tr>
            <tr>
              <td>website</td>
              <td>{{userAuthorizedInfo.website}}</td>
            </tr>
            <tr>
              <td>Количество статей</td>
              <td>{{userAuthorizedInfo.articles}}</td>
            </tr>
            <tr>
              <td>Количество комментраиев</td>
              <td>{{userAuthorizedInfo.comments}}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </b-tab>
    <b-tab title="Ваши фото">
       <b-form-file 
          v-model="userphoto"
          placeholder="Выберете файл"
          :disabled="loadFile"
        ></b-form-file>
        <div class="d-flex justify-content-center mb-3">
          <b-spinner label="Loading..."
          v-show="loadFile"></b-spinner>
        </div>
        <b-button v-if="userAuthorizedInfo && userAuthorizedInfo.photos.length > 0"
        @click="showImages = !showImages"
        variant="danger"
        class="button-simple button-simple_red form-comment__button">Остальные фото пользователя</b-button>
        <div class="row" v-if="showImages">
          <div class="photo-block"
          v-for="item in userAuthorizedInfo.photos" :key="item.id">
            <font-awesome-icon icon="trash" class="text-danger fa-2x iconphoto" 
              role="button" @click="deletePhoto(item.id, item.photo)"/>
            <b-img-lazy v-bind="mainProps"  class="img-fluid"
            :src="getImageUrl(item.id)" alt="Image 1"></b-img-lazy>
          </div>
          
        </div>
    </b-tab>
  </b-tabs>
  </div>
</template>
<script>
import Vue from "vue";
import { mapGetters } from "vuex";

import "./style.scss";
export default {
  data() {
    return {
      mainProps: {
        center: true,
        fluidGrow: true,
        blank: true,
        blankColor: "#bbb",
        class: "my-5"
      },
      showImages: false,
      loadFile: false,
    };
  },
  created() {
    this.$store.dispatch( 'users/getUserAuthorizedInfo', {
      email: Vue.prototype.$userEmail
    });
  },
   computed: {
   ...mapGetters( 'users', [ 'userAuthorizedInfo'] ),
    ...mapGetters( [ 'alertType', 'alertMsg', 'showAlert' ] ),
    photo: function(){
      if(this.userAuthorizedInfo.photo){
        return `/bundles/users/1000_${this.userAuthorizedInfo.photo}`
      }
      return null;
    },
    createdAt: function(){
      return (new Date(this.userAuthorizedInfo.created_at)).toLocaleDateString() 
    },
    gender: function(){
      return this.userAuthorizedInfo.gender === 'm' ? 'мужской' : 'Женский'
    },
    target: function(){
      let target = '';

      switch(this.userAuthorizedInfo.target){
        case 'm':
          target = 'мужчину';
          break;
        case 'f':
          target = 'женщину';
          break;
        case 'p':
          target = 'пару';
          break;
        default:
          break;
      }
      return target
    },
    userphoto: {
      get(){
        return null;
      },
      set(newValue){
        this.loadFile = true;
        let photoUser = '';
        let formData = new FormData();
        formData.append('photo', newValue);

        Vue.http.post('users/addPhhoto', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            if(response.success){
              this.loadFile = false;
              const {id, photo} = response;
              console.log(response);

              this.userAuthorizedInfo.photos.splice(this.userAuthorizedInfo.photos.length, 1, {
                id,
                photo
              })
             
              this.$store.dispatch('openAlert',{
                alertType : 'success',
                alertMsg : 'Фото загружено'
              }, { root: true })
              
            }
            
          }, response => {
            this.$store.dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
          });
        
      }
    }
  },
  methods: {
    getImageUrl(imageId) {
      const { width } = this.mainProps
      const  image = this.userAuthorizedInfo.photos.find(item => item.id === imageId);
      return `/bundles/users/1000_${image.photo}`;
    },
    deletePhoto(id, photo){
      let formData = new FormData();

      formData.append('id', +id);
      formData.append('author_id', +this.userAuthorizedInfo.id);
      formData.append('photo', photo);
      console.log(photo);
      Vue.http.post('users/removePhhoto', formData)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          if(response.success){
           const photoId = this.userAuthorizedInfo.photos.findIndex(item => +item.id === +id);
         
            this.$delete(this.userAuthorizedInfo.photos, photoId)
            
            this.$store.dispatch('openAlert',{
              alertType : 'success',
              alertMsg : 'Фото загружено'
            }, { root: true })
            return photo;
          }
          
        }, response => {
          this.$store.dispatch('openAlert',{
            alertType : 'danger',
            alertMsg : 'Произошла ошибка. Попробуйте еще раз'
          }, { root: true })
        });
    },
    closeAlert(){
      this.$store.dispatch( 'closeAlert')
    }
  }
};
</script>

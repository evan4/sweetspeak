<template>
  <div>
    <div class="top-main">
      <h1 class="h1">Профиль пользователя {{getCurrentUser.name}}</h1>
      <router-link class="fa-lg flex-center" title="Редактировать профиль"
      v-if="role === 'admin' || this.role === 'superadmin'"
      to="/users/edit">
          <font-awesome-icon icon="edit" class="text-prinary" />
      </router-link>
    </div>
    <div class="row" v-if="getCurrentUser">
      <div class="col-lg-6 col-xs-12">

        <table class="table able-responsive table-bordered table-striped" >
          <tbody>
            <tr>
              <td>Имя</td>
              <td>{{getCurrentUser.name}}</td>
            </tr>
            <tr>
              <td>Ник</td>
              <td>{{getCurrentUser.nickname}}</td>
            </tr>
            <tr>
              <td>Email</td>
              <td>{{getCurrentUser.email}}</td>
            </tr>
            <tr>
              <td>Роль</td>
              <td>{{getCurrentUser.status}}</td>
            </tr>
            <tr>
              <td>Баланс</td>
              <td>{{getCurrentUser.balance}}</td>
            </tr>
            <tr>
              <td>Создан</td>
              <td>{{createdAt}}</td>
            </tr>
            <tr>
              <td>Статус пользователя</td>
              <td>{{getCurrentUser.userstatus}}</td>
            </tr>
            <tr>
              <td>Девиз</td>
              <td>{{getCurrentUser.motto}}</td>
            </tr>
            <tr>
              <td>Ищу</td>
              <td>{{target}}</td>
            </tr>
            <tr>
              <td>О себе</td>
              <td>{{getCurrentUser.bio}}</td>
            </tr>
            <tr>
              <td>Пол</td>
              <td>{{gender}}</td>
            </tr>
            <tr>
              <td>Возраст</td>
              <td>{{getCurrentUser.age}}</td>
            </tr>
            <tr>
              <td>Город</td>
              <td>{{getCurrentUser.city}}</td>
            </tr>
            <tr>
              <td>instagram</td>
              <td>{{getCurrentUser.instagram}}</td>
            </tr>
            <tr>
              <td>vk</td>
              <td>{{getCurrentUser.vk}}</td>
            </tr>
            <tr>
              <td>twitter</td>
              <td>{{getCurrentUser.twitter}}</td>
            </tr>
            <tr>
              <td>facebook</td>
              <td>{{getCurrentUser.facebook}}</td>
            </tr>
            <tr>
              <td>website</td>
              <td>{{getCurrentUser.website}}</td>
            </tr>
            <tr>
              <td>Количество статей</td>
              <td>{{getCurrentUser.articles}}</td>
            </tr>
            <tr>
              <td>Количество комментраиев</td>
              <td>{{getCurrentUser.comments}}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-lg-6 col-xs-12">
        <div class="row">
          <img :src="photo" :alt="getCurrentUser.name" class="mb-3 img-fluid"
          v-if="getCurrentUser.photo">
        </div>
        
        <b-button v-if="getCurrentUser.photo && getCurrentUser.photos.length > 0"
        @click="showImages = !showImages">Остальные фото пользователя</b-button>
      </div>
    </div>
    <v-select :options="userstates" class="form__select"
              label="text"
              @input="changeUserVisibility"
              :reduce="userstates => userstates.value"
              v-model="getCurrentUser.confirm"></v-select>

    <div class="row" v-if="showImages">
      <div class="photo-block" v-for="item in getCurrentUser.photos" :key="item.id">
        <font-awesome-icon icon="trash" class="text-danger fa-2x iconphoto" 
          role="button" @click="deletePhoto(item.id, item.photo)"/>
        <b-img-lazy v-bind="mainProps"  class="img-fluid"
        :src="getImageUrl(item.id)" alt="Image 1"></b-img-lazy>
      </div>
    </div>
        
  </div>
</template>
<script>
import Vue from 'vue'
import { mapGetters } from 'vuex';

export default {
  data() {

    return {
      mainProps: {
        center: true,
        fluidGrow: true,
        blank: true,
        blankColor: '#bbb',
        class: 'my-5'
      },
      userstates: [
        {
          value: "0",
          text: "не подтвержден"
        },
        {
          value: "1",
          text: "активен"
        },
        {
          value: "2",
          text: "неактивен"
        }
      ],
      showImages: false,
      role: "user",
    };

  },
  created() {
    this.role = Vue.prototype.$role;
    this.$store.dispatch( 'users/getCurrentUser', +this.$route.params.id);
  },
  computed: {
    ...mapGetters( 'users', [ 'getCurrentUser' ] ),
    photo: function(){
      if(this.getCurrentUser.photo){
        return `/bundles/users/${this.getCurrentUser.photo}`
      }
      return null;
    },
    createdAt: function(){
      return (new Date(this.getCurrentUser.created_at)).toLocaleDateString() 
    },
    gender: function(){
      return this.getCurrentUser.gender === 'm' ? 'мужской' : 'Женский'
    },
    target: function(){
      let usertarget = '';

      switch (this.getCurrentUser.target) {
        case 'm':
          usertarget = 'мужчину'
          break;
        case 'p':
          usertarget = 'пару'
          break;
        case 'f':
          usertarget = 'женщину'
          break;
        default:

          break;
      }
      return usertarget
    },
    photo: function(){
      return `/bundles/users/500_${this.getCurrentUser.photo}`
    },
    
  },
  methods:{
    changeUserVisibility(value){
      let formData = new FormData();
      formData.append('id', this.getCurrentUser.id);
      formData.append('confirm', value);
       Vue.http.post('users/changeUserVisibility', formData)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          
          if(response){
            this.$store.dispatch('openAlert',{
              alertType : 'success',
              alertMsg : 'Статус пользователя изменен'
            }, { root: true })
            
          }
          
        }, response => {
          this.$store.dispatch('openAlert',{
            alertType : 'danger',
            alertMsg : 'Произошла ошибка. Попробуйте еще раз'
          }, { root: true })
        });
    },
     getImageUrl(imageId) {
        const { width } = this.mainProps
        const  image = this.getCurrentUser.photos.find(item => item.id === imageId);
        return `/bundles/users/500_${image.photo}`;
      },
       deletePhoto(id, photo){
      let formData = new FormData();

      formData.append('id', +id);
      formData.append('author_id', +this.getCurrentUser.id);
      formData.append('photo', photo);
      console.log(photo);
      Vue.http.post('users/removePhhoto', formData)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          if(response.success){
           const photoId = this.getCurrentUser.photos.findIndex(item => +item.id === +id);

            this.$delete(this.getCurrentUser.photos, photoId)
            
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
}
</script>
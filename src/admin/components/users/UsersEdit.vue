<template>
  <div>
    <h1>Редактирование пользователя</h1>

    <div v-if="userData">
      <b-form-group v-if="formData.photo">
          <b-img :src="formData.photo" fluid :alt="formData.name"></b-img>
      </b-form-group>
       <b-button v-if="formData.photos && formData.photos.length > 0"
      @click="showImages = !showImages">Остальные фото пользователя</b-button>
      <div v-if="showImages">
        <b-img-lazy v-bind="mainProps"  v-for="item in formData.photos" :key="item.id"
          :src="getImageUrl(item.id)" alt="Image 1"></b-img-lazy>
      </div>

       <b-form @submit.prevent="onSubmit">
        
        <b-row>
          
          <b-col md="4">
            <b-form-group id="input-group-name" label="Имя:" label-for="input-name">
              <b-form-input
                id="input-name"
                v-model="formData.name"
                class="form__input"
                required
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group id="input-group-nickname" label="Псевдоним:" label-for="input-nickname">
              <b-form-input
                id="input-nickname"
                v-model="formData.nickname"
                class="form__input"
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group id="input-group-age" label="Возраст:" label-for="input-age">
              <b-form-input
                id="input-age"
                type="number"
                class="form__input"
                v-model="formData.age"
              ></b-form-input>
            </b-form-group>
          </b-col>
        </b-row>

        <b-row>
          <b-col md="4">
            <b-form-group
              id="input-group-1"
              label="Email"
              label-for="input-email"
            >
            <b-form-input
                id="input-email"
                v-model="formData.email"
                type="email"
                class="form__input"
                @keyup="checkUniqueEmail"
                :disabled="disableEmailField  == 1"
                ref="email"
                required
              ></b-form-input>
              <b-form-invalid-feedback :state="!uniqueEmail">{{uniqueEmailHint}}</b-form-invalid-feedback>
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group id="input-group-target" label="Ищет" label-for="input-3">
               <v-select :options="targets" 
                  id="input-target"
                  class="form__select"
                  label="text"
                  :reduce="targets => targets.value"
                  required
                  v-model="formData.target"></v-select>
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group id="input-group-city" label="Город:" label-for="input-city">
              <b-form-input
                id="input-city"
                v-model="formData.city"
                class="form__input"
              ></b-form-input>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col md="4">
            <b-form-group id="input-group-facebook" label="facebook:" label-for="input-facebook">
              <b-form-input
                id="input-facebook"
                v-model="formData.facebook"
                class="form__input"
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group id="input-group-vk" label="vk:" label-for="input-vk">
              <b-form-input
                id="input-vk"
                v-model="formData.vk"
                class="form__input"
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group id="input-group-instagram" label="instagram:" label-for="input-instagram">
              <b-form-input
                id="input-instagram"
                v-model="formData.instagram"
                class="form__input"
              ></b-form-input>
            </b-form-group>
          </b-col>
          </b-row>

          <b-row>
            <b-col md="4">
              <b-form-group id="input-group-website" label="Сайт:" label-for="input-website">
                <b-form-input
                  id="input-website"
                  v-model="formData.website"
                  class="form__input"
                ></b-form-input>
              </b-form-group>
            </b-col>
            <b-col md="4">
              <b-form-group id="input-group-twitter" label="twitter:" label-for="input-twitter">
                <b-form-input
                  id="input-twitter"
                  v-model="formData.twitter"
                  class="form__input"
                ></b-form-input>
              </b-form-group>
            </b-col>
            <b-col md="4">
              <b-form-group id="input-group-status" label="Роль" label-for="input-status">
                <v-select :options="roles" 
                  id="input-status"
                  class="form__select"
                  label="text"
                  :reduce="roles => roles.value"
                  required
                  v-model="formData.status"></v-select>
              </b-form-group>
            </b-col>
          </b-row>
          <b-form-group id="input-group-gender" label="ПОл:" label-for="input-gender">
             <v-select :options="genders" class="form__select"
                label="text"
                :reduce="genders => genders.value"
                transition
                required
                v-model="formData.gender"></v-select>
                </b-form-group>
          </b-form-group>
          <b-form-group id="input-group-motto" label="Девиз пользователя:" label-for="input-motto">
              <b-form-textarea
                id="input-motto"
                v-model="formData.motto"
                class="form__textarea"
              ></b-form-textarea>
          </b-form-group>
          <b-form-group id="input-group-bio" label="О себе" label-for="input-bio">
            <b-form-textarea
              id="input-group-bio"
              v-model="formData.bio"
              class="form__textarea"
            ></b-form-textarea>
          </b-form-group>
    
        <b-button type="submit" :disabled="submit"
        class="button button_red form-comment__button button_submit">
          <b-spinner small type="grow" v-show="submit"></b-spinner>
          <span>Сохранить</span>
        </b-button>
      </b-form>

    </div>
  </div>
  
</template> 
<script>

import { mapGetters } from 'vuex';

export default {
    data() {
      return {
        emailReg: /^(([^<>()\]\\.,;:\s@"]+(\.[^<>()\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
        uniqueEmail: false,
        uniqueEmailHint: '',
        passwordValidHint: 'Пароль должен быть не меньше 8 символов',
        errors: false,
        disableEmailField: 0,
        roles: ['superadmin', 'admin', 'moderator', 'user' ],
        formData: [],
        targets: [
          { value: '', text: 'Выберете значение' },
          { value: 'm', text: 'мужчину' },
          { value: 'f', text: 'женщину' },
          { value: 'p', text: 'пару' },
        ],
        genders: [
          { value: 'm', text: 'мужской' },
          { value: 'f', text: 'женский' },
        ],
        created_at: null,
        gender: null,
        submit: false,
        photo: '',
        photos: [],
        showImages: false,
        mainProps: {
          center: true,
          fluidGrow: true,
          blank: true,
          blankColor: '#bbb',
          width: 600,
          class: 'my-5'
        }
      }
    },
    created(){
    },
     computed: {
      ...mapGetters( [ 'alertType', 'alertMsg', 'showAlert' ] ),
      userData: {
        // геттер:
        get: function () {
          const data = this.$store.state.users.currentuser;
          delete data.created_at;
          this.formData = JSON.parse(JSON.stringify(data));
          this.formData.photo = this.formData.photo ? `/bundles/users/300_${this.formData.photo}` : '';
          return data;
        },
        // сеттер:
        set: function (newValue) {
          return newValue
        }
      },
      emailValidation() {
        return this.formData.email !== this.$store.state.users.currentuser.email && 
          this.emailReg.test(String(this.formData.email).toLowerCase());
      },
      passwordValidation(){
        return this.formData.password >= 8;
      }
    },
    methods: {
      checkUniqueEmail(){
        if(this.emailValidation){
          this.disableEmailField = 1;
          this.uniqueEmailHint = '';
          this.$store.dispatch( 'users/checkEmailUniqueness', this.formData.email)
          .then(
            result => {
              console.log(result);
              if(result.success){
                this.uniqueEmail = false;
              }else{
                this.uniqueEmail = true;
                this.uniqueEmailHint = result.email_unique
              }
              this.disableEmailField = 0;
            },
            error => {
              
              this.$store.dispatch( 'openAlert', {
                alertType : 'danger',
                alertMsg : 'Произошла ошибка. Попробуйте еще раз'
              });
              this.disableEmailField = 0;
              
            }
          );
          
        }else{
          this.uniqueEmail = false;
        }
      },
      getImageUrl(imageId) {
        const { width } = this.mainProps
        const  image = this.photos.find(item => item.id === imageId);
        return image.photo;
      },
      onSubmit() {
        if ( !this.formData.name ) {

          this.errors.push( 'Name required.' );

        }
        if ( !this.formData.email ) {

          this.errors.push( 'Email required.' );

        }
        if ( this.uniqueEmail ) {

          this.errors.push(this.uniqueEmailHint);

        }

        if ( !this.errors.length ) {
          this.submit = true;
          
          this.$store.dispatch( 'users/updateUser', {
            data: this.formData
          })
          .then(
            result => {
              
              this.submit = false;
              
            },
            error => {
              this.submit = false;
            }
          );
        }
      },
      closeAlert(){
        this.$store.dispatch( 'closeAlert')
      }
    },
    destroyed() {

      this.errors = [];
      this.formData = null

    },
}
</script>